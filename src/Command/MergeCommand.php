<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CistercianNumberGeneratorService;
use App\Utils\FileUtil;
use DateTime;
use GdImage;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Command to merge multiple Cistercian numbers into one image.
 * Please don't judge the chaos that is this file, it was created during several Discord sessions.
 */
#[AsCommand(
    name: 'app:merge',
    description: 'Merge multiple png files into one png file',
)]
final class MergeCommand extends Command
{
    private int $widthOfOneImage;

    private int $heightOfOneImage;

    private string $outputDirectory;

    public function __construct(
        private readonly CistercianNumberGeneratorService $cistercianNumberGeneratorService,
        #[Autowire('%env(CISTERCIAN_NUMBER_GENERATOR_SEGMENT_LENGTH)%')]
        private readonly int $segmentLength,
        #[Autowire('%env(CISTERCIAN_NUMBER_GENERATOR_LINE_THICKNESS)%')]
        private readonly int $lineThickness,
        #[Autowire('%env(CISTERCIAN_NUMBER_GENERATOR_MERGE_PADDING)%')]
        private readonly int $mergePadding,
        #[Autowire('%kernel.project_dir%')]
        string $projectDirectory,
    ) {
        parent::__construct();
        $this->widthOfOneImage = $this->segmentLength * 2;
        $this->heightOfOneImage = $this->segmentLength * 4;
        $this->outputDirectory = $projectDirectory . '/output/merge/' . (new DateTime())->getTimestamp();
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    public function getBaseTallImage(array $numbers): GdImage
    {
        $outputWidth = $this->widthOfOneImage * 6 + $this->mergePadding;
        $outputHeight = count($numbers) * $this->heightOfOneImage + $this->mergePadding;
        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);

        if (false === $outputImage) {
            throw new RuntimeException('Failed to create image');
        }

        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        return $outputImage;
    }

    /**
     * @param array<int, array<int>> $numbers
     * @return array{0: int, 1: GdImage}
     */
    public function getBaseWideImage(array $numbers): array
    {
        $heightOfOneImage = $this->segmentLength * 4;
        $widthOfOneImage = $this->segmentLength * 2;

        $outputWidth = ($widthOfOneImage * count($numbers)) - (count(
            $numbers
        ) * $this->lineThickness) + $this->mergePadding;
        $outputHeight = $heightOfOneImage + $this->mergePadding;

        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        return [$widthOfOneImage, $outputImage];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        FileUtil::ensureDirectoryExists($this->outputDirectory);

        $numbers = [
            [5038, 4245],
            [5816, 5725],
            [5626, 7119],
            [3220, 5123],
            [7457, 2254],
            [7542, 7258],
            [8149, 6445],
            [112, 6754, 6050],
            [6118, 1719],
            [5032, 5213],
            [8030, 59],
        ];

        $this->generateOneLineDifference($numbers);
        $this->generateOneLineSideToSideUnmerged($numbers);
        $this->generateOneLineSideToSideMerged($numbers);
        $this->generateMultipleLinesMerged($numbers);
        $this->generateMulipleLinesUnmerged($numbers);
        $this->generateMultipleLinesUnmergedWithShiftFullSpace($numbers);
        $this->generateMultipleLinesUnmergedWithShiftHalfSpace($numbers);
        $this->generateMultipleLinesMergedWithShiftFullSpace($numbers);
        $this->generateMultipleLinesMergedWithShiftHalfSpace($numbers);

        $this->generateTruncatedOutput();

        return Command::SUCCESS;
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMultipleLinesMergedWithShiftFullSpace(array $numbers): void
    {
        $outputImage = $this->getBaseTallImage($numbers);

        $i = 1;
        $x = 0;
        $y = 0;
        $linesWithShift = [3, 4, 6, 8, 9, 11];
        $image = null;

        foreach ($numbers as $line) {
            $j = 1;

            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                $x += (int) floor($this->mergePadding / 2);
                $x -= $this->lineThickness;

                if (1 === $j && in_array($i, $linesWithShift, true)) {
                    $x += $this->widthOfOneImage;
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x,
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
                $x += $this->lineThickness - (int) floor($this->mergePadding / 2);
                $j++;
            }

            if (null === $image) {
                continue;
            }

            $x = 0;
            $y += imagesy($image) - $this->lineThickness;
            $i++;
        }

        imagepng($outputImage, $this->outputDirectory . '/7-output-multiple-lines-shifted-merged-full-space.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMultipleLinesMergedWithShiftHalfSpace(array $numbers): void
    {
        $outputImage = $this->getBaseTallImage($numbers);

        $i = 1;
        $y = 0;
        $linesWithShift = [3, 4, 6, 8, 9, 11];
        $image = null;

        foreach ($numbers as $line) {
            $j = 1;

            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                $x = (int) floor($this->mergePadding / 2);

                if (1 === $j && in_array($i, $linesWithShift, true)) {
                    $x += (int) floor($this->widthOfOneImage / 2);
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x,
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
                $j++;
            }

            if (null === $image) {
                continue;
            }

            $x = 0;
            $y += imagesy($image) - $this->lineThickness;
            $i++;
        }

        imagepng($outputImage, $this->outputDirectory . '/8-output-multiple-lines-shifted-merged-half-space.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMultipleLinesUnmergedWithShiftHalfSpace(array $numbers): void
    {
        $outputImage = $this->getBaseTallImage($numbers);

        $i = 1;
        $x = 0;
        $y = 0;
        $linesWithShift = [3, 4, 6, 8, 9, 11];
        $image = null;

        foreach ($numbers as $line) {
            $j = 1;

            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                $x += (int) floor($this->mergePadding / 2);

                if (1 === $j && in_array($i, $linesWithShift, true)) {
                    $x += (int) $this->widthOfOneImage / 2 - (int) ($this->lineThickness / 2);
                }

                imagecopy(
                    $outputImage,
                    $image,
                    (int) $x,
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
                $x += imagesx($image) - $this->lineThickness - (int) floor($this->mergePadding / 2);
                $j++;
            }

            if (null === $image) {
                continue;
            }

            $x = 0;
            $y += imagesy($image) - $this->lineThickness;
            $i++;
        }

        imagepng($outputImage, $this->outputDirectory . '/6-output-multiple-lines-shifted-unmerged-half-space.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMultipleLinesUnmergedWithShiftFullSpace(array $numbers): void
    {
        $heightOfOneImage = $this->segmentLength * 4;
        $widthOfOneImage = $this->segmentLength * 2;

        $outputWidth = $widthOfOneImage * 6 + $this->mergePadding;
        $outputHeight = count($numbers) * $heightOfOneImage + $this->mergePadding;
        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        $i = 1;
        $x = 0;
        $y = 0;
        $linesWithShift = [3, 4, 6, 8, 9, 11];
        $image = null;

        foreach ($numbers as $line) {
            $j = 1;

            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                $x += (int) floor($this->mergePadding / 2);

                if (1 === $j && in_array($i, $linesWithShift, true)) {
                    $x += $widthOfOneImage - $this->lineThickness;
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x,
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
                $x += imagesx($image) - $this->lineThickness - (int) floor($this->mergePadding / 2);
                $j++;
            }

            if (null === $image) {
                continue;
            }

            $x = 0;
            $y += imagesy($image) - $this->lineThickness;
            $i++;
        }

        imagepng($outputImage, $this->outputDirectory . '/5-output-multiple-lines-shifted-unmerged-full-space.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMulipleLinesUnmerged(array $numbers): void
    {
        $heightOfOneImage = $this->segmentLength * 4;
        $widthOfOneImage = $this->segmentLength * 2;

        $outputWidth = $widthOfOneImage * 3 + $this->mergePadding;
        $outputHeight = count($numbers) * $heightOfOneImage + $this->mergePadding;
        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        $x = 0;
        $y = 0;
        $image = null;

        foreach ($numbers as $line) {
            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x + (int) floor($this->mergePadding / 2),
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
                $x += imagesx($image) - $this->lineThickness;
            }

            if (null === $image) {
                continue;
            }

            $x = 0;
            $y += imagesy($image) - $this->lineThickness;
        }

        imagepng($outputImage, $this->outputDirectory . '/4-output-multiple-lines-unmerged.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateMultipleLinesMerged(array $numbers): void
    {
        $heightOfOneImage = $this->segmentLength * 4;
        $widthOfOneImage = $this->segmentLength * 2;

        $outputWidth = $widthOfOneImage + $this->mergePadding;
        $outputHeight = count($numbers) * $heightOfOneImage + $this->mergePadding;
        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        $x = 0;
        $y = 0;
        $image = null;

        foreach ($numbers as $line) {
            foreach ($line as $number) {
                $image = imagecreatefrompng(
                    $this->cistercianNumberGeneratorService->generateCistercianNumber($number)
                );

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x + (int) floor($this->mergePadding / 2),
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
            }

            if (null === $image) {
                continue;
            }

            $y += imagesy($image) - $this->lineThickness;
        }

        imagepng($outputImage, $this->outputDirectory . '/3-output-multiple-lines-merged.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateOneLineSideToSideUnmerged(array $numbers): void
    {
        $numbers = array_merge(...$numbers);

        $outputWidth = ($this->widthOfOneImage * count($numbers)) - (count(
            $numbers
        ) * $this->lineThickness) + $this->mergePadding;
        $outputHeight = $this->heightOfOneImage + $this->mergePadding;

        $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        if (false === $white) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($outputImage, 0, 0, $white);

        $x = 0;
        $y = 0;

        foreach ($numbers as $number) {
            $image = imagecreatefrompng($this->cistercianNumberGeneratorService->generateCistercianNumber($number));

            if (false === $image) {
                throw new RuntimeException('Failed to create image');
            }

            imagecopy(
                $outputImage,
                $image,
                $x + (int) floor($this->mergePadding / 2),
                $y + (int) floor($this->mergePadding / 2),
                0,
                0,
                imagesx($image),
                imagesy($image)
            );
            $x += imagesx($image) - $this->lineThickness;
        }

        imagepng($outputImage, $this->outputDirectory . '/1-output-side-to-side-unmerged.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateOneLineSideToSideMerged(array $numbers): void
    {
        [$widthOfOneImage, $outputImage] = $this->getBaseWideImage($numbers);

        $x = 0;
        $y = 0;

        foreach ($numbers as $line) {
            foreach ($line as $number) {
                $image = imagecreatefrompng($this->cistercianNumberGeneratorService->generateCistercianNumber($number));

                if (false === $image) {
                    throw new RuntimeException('Failed to create image');
                }

                imagecopy(
                    $outputImage,
                    $image,
                    $x + (int) floor($this->mergePadding / 2),
                    $y + (int) floor($this->mergePadding / 2),
                    0,
                    0,
                    imagesx($image),
                    imagesy($image)
                );
            }

            $x += $widthOfOneImage - $this->lineThickness;
        }

        imagepng($outputImage, $this->outputDirectory . '/2-output-side-to-side-merged.png');
    }

    /**
     * @param array<int, array<int>> $numbers
     */
    private function generateOneLineDifference(array $numbers): void
    {
        [$widthOfOneImage, $outputImage] = $this->getBaseWideImage($numbers);

        $x = 0;
        $y = 0;

        foreach ($numbers as $line) {
            $image = imagecreatefrompng($this->cistercianNumberGeneratorService->generateDifferenceNumbers($line));

            if (false === $image) {
                throw new RuntimeException('Failed to create image');
            }

            imagecopy(
                $outputImage,
                $image,
                $x + (int) floor($this->mergePadding / 2),
                $y + (int) floor($this->mergePadding / 2),
                0,
                0,
                imagesx($image),
                imagesy($image)
            );

            $x += $widthOfOneImage - $this->lineThickness;
        }

        imagepng($outputImage, $this->outputDirectory . '/0-output-difference.png');
    }

    private function generateTruncatedOutput(): void
    {
        $files = glob($this->outputDirectory . '/*.png');

        if (false === $files) {
            throw new RuntimeException('Failed to get files');
        }

        // Only truncate the first 3 files
        $files = array_slice($files, 0, 3);

        // Truncate the middle part files
        // Keep segmentLength+lineThickness+mergePadding for the top and bottom, delete the rest
        // make the top and bottom part touch each other
        foreach ($files as $file) {
            $top = imagecreatefrompng($file);
            $bottom = imagecreatefrompng($file);

            if (false === $top || false === $bottom) {
                throw new RuntimeException('Failed to create image');
            }

            $width = imagesx($top);
            $height = imagesy($top) - $this->segmentLength * 2;
            $cutHeightStart = (int) ($this->mergePadding / 2);

            $outputImage = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($outputImage, 255, 255, 255);

            if (false === $white) {
                throw new RuntimeException('Failed to allocate color');
            }

            imagefill($outputImage, 0, 0, $white);

            $topCrop = imagecrop(
                $top,
                [
                    'x' => 0,
                    'y' => $cutHeightStart,
                    'width' => $width,
                    'height' => $this->segmentLength + $this->lineThickness,
                ]
            );

            if (false === $topCrop) {
                throw new RuntimeException('Failed to crop image');
            }

            $bottomCrop = imagecrop(
                $bottom,
                [
                    'x' => 0,
                    'y' => $cutHeightStart + ($this->segmentLength * 3) + $this->lineThickness,
                    'width' => $width,
                    'height' => $this->segmentLength + $this->lineThickness,
                ]
            );

            if (false === $bottomCrop) {
                throw new RuntimeException('Failed to crop image');
            }

            imagecopy(
                $outputImage,
                $topCrop,
                0,
                (int) ($this->mergePadding / 2),
                0,
                0,
                imagesx($topCrop),
                imagesy($topCrop)
            );
            imagecopy(
                $outputImage,
                $bottomCrop,
                0,
                (int) ($this->mergePadding / 2) + $this->segmentLength + $this->lineThickness,
                0,
                0,
                imagesx($bottomCrop),
                imagesy($bottomCrop)
            );

            imagepng($outputImage, str_replace('.png', '-truncated.png', $file));
        }
    }
}
