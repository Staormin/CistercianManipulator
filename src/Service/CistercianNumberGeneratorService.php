<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\FileUtil;
use App\ValueObject\Quadrant;
use GdImage;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Service to generate and manipulate Cistercian numbers.
 *
 * This service provides methods to generate Cistercian numbers from Arabic numbers
 * and to generate images that highlight the differences between multiple Cistercian numbers.
 */
final readonly class CistercianNumberGeneratorService
{
    private const SEGMENT_TOP = 'top';
    private const SEGMENT_BOTTOM = 'bottom';
    private const SEGMENT_DIAG_ONE = 'diagOne';
    private const SEGMENT_DIAG_TWO = 'diagTwo';
    private const SEGMENT_RIGHT = 'right';
    private const SEGMENTS = [
        0 => [],
        1 => [self::SEGMENT_TOP],
        2 => [self::SEGMENT_BOTTOM],
        3 => [self::SEGMENT_DIAG_ONE],
        4 => [self::SEGMENT_DIAG_TWO],
        5 => [self::SEGMENT_TOP, self::SEGMENT_DIAG_TWO],
        6 => [self::SEGMENT_RIGHT],
        7 => [self::SEGMENT_TOP, self::SEGMENT_RIGHT],
        8 => [self::SEGMENT_BOTTOM, self::SEGMENT_RIGHT],
        9 => [self::SEGMENT_BOTTOM, self::SEGMENT_RIGHT, self::SEGMENT_TOP],
    ];

    private string $outputDirectory;

    private int $width;

    private int $height;

    private int $offset;

    public function __construct(
        #[Autowire('%env(CISTERCIAN_NUMBER_GENERATOR_SEGMENT_LENGTH)%')]
        private int $segmentLength,
        #[Autowire('%env(CISTERCIAN_NUMBER_GENERATOR_LINE_THICKNESS)%')]
        private int $lineThickness,
        #[Autowire('%kernel.project_dir%')]
        string $projectDirectory,
    ) {
        $this->outputDirectory = $projectDirectory . '/output/CistercianNumbers';
        $this->width = $this->segmentLength * 2;
        $this->height = $this->segmentLength * 4;
        $this->offset = (int) floor($this->lineThickness / 2);
    }

    /**
     * Generates a Cistercian number image for the given Arabic number.
     *
     * @param int $number The Arabic number to generate a Cistercian number for.
     * @return string The path to the generated image.
     */
    public function generateCistercianNumber(int $number): string
    {
        FileUtil::ensureDirectoryExists($this->outputDirectory);

        $outputFilename = sprintf('%s/%d.png', $this->outputDirectory, $number);

        if (file_exists($outputFilename)) {
            return $outputFilename;
        }

        $outputImage = $this->createTransparentImage($this->width, $this->height);
        $black = $this->allocateBlackColor($outputImage);
        $this->traceLine($outputImage, $this->width / 2, 0, $this->width / 2, $this->height, $black);

        $digits = $this->extractDigits($number);
        $quadrants = [
            Quadrant::createForTopRight($this->segmentLength, $this->offset),
            Quadrant::createForTopLeft($this->segmentLength, $this->offset),
            Quadrant::createForBottomRight($this->segmentLength, $this->offset),
            Quadrant::createForBottomLeft($this->segmentLength, $this->offset),
        ];

        foreach ($digits as $index => $digit) {
            $this->traceDigit($outputImage, $digit, $black, $quadrants[$index]);
        }

        imagepng($outputImage, $outputFilename);
        imagedestroy($outputImage);

        return $outputFilename;
    }

    /**
     * Generates an image that highlights the differences between the given Cistercian numbers.
     * If a segment is present in one number but not in the other, it will be shown in the output image.
     * If a segment is present in more than one number, it will not be shown in the output image.
     *
     * @param array<int> $numbers
     * @return string The path to the generated image.
     */
    public function generateDifferenceNumbers(array $numbers): string
    {
        FileUtil::ensureDirectoryExists($this->outputDirectory);

        $segmentsToTrace = array_fill_keys(['first', 'second', 'third', 'fourth'], []);
        $alreadyTracedSegments = array_fill_keys(['first', 'second', 'third', 'fourth'], []);

        foreach ($numbers as $number) {
            $digits = [
                'first' => $number % 10,
                'second' => ($number / 10) % 10,
                'third' => ($number / 100) % 10,
                'fourth' => ($number / 1000) % 10,
            ];

            foreach ($digits as $key => $digit) {
                foreach (self::SEGMENTS[$digit] as $segment) {
                    if (in_array($segment, $alreadyTracedSegments[$key], true)) {
                        unset($segmentsToTrace[$key][array_search($segment, $segmentsToTrace[$key], true)]);
                    } else {
                        $segmentsToTrace[$key][] = $segment;
                        $alreadyTracedSegments[$key][] = $segment;
                    }
                }
            }
        }

        $outputFilename = sprintf('%s/difference-%s.png', $this->outputDirectory, implode('-', $numbers));

        if (file_exists($outputFilename)) {
            return $outputFilename;
        }

        $outputImage = $this->createTransparentImage($this->width, $this->height);
        $black = $this->allocateBlackColor($outputImage);
        $quadrants = [
            'first' => Quadrant::createForTopRight($this->segmentLength, $this->offset),
            'second' => Quadrant::createForTopLeft($this->segmentLength, $this->offset),
            'third' => Quadrant::createForBottomRight($this->segmentLength, $this->offset),
            'fourth' => Quadrant::createForBottomLeft($this->segmentLength, $this->offset),
        ];

        foreach ($segmentsToTrace as $key => $segments) {
            $quadrant = $quadrants[$key];

            foreach ($segments as $segment) {
                $this->traceSegment($outputImage, $segment, $black, $quadrant);
            }
        }

        imagepng($outputImage, $outputFilename);
        imagedestroy($outputImage);

        return $outputFilename;
    }

    /**
     * Creates a transparent image with the given dimensions.
     */
    private function createTransparentImage(int $width, int $height): GdImage
    {
        $image = imagecreatetruecolor($width, $height);

        if (false === $image) {
            throw new RuntimeException('Failed to create image');
        }

        imageAlphaBlending($image, false);
        $transparency = imagecolorallocatealpha($image, 0, 0, 0, 127);

        if (false === $transparency) {
            throw new RuntimeException('Failed to allocate color');
        }

        imagefill($image, 0, 0, $transparency);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Allocates a black color in the given image.
     */
    private function allocateBlackColor(GdImage $image): int
    {
        $color = imagecolorallocate($image, 0, 0, 0);

        if (false === $color) {
            throw new RuntimeException('Failed to allocate color');
        }

        return $color;
    }

    /**
     * Extracts the digits of the given number.
     *
     * @return array<int> The digits of the given number. First element is the least significant digit and so on.
     */
    private function extractDigits(int $number): array
    {
        return [
            $number % 10,
            ($number / 10) % 10,
            ($number / 100) % 10,
            ($number / 1000) % 10,
        ];
    }

    /**
     * Traces the given segment in the given quadrant with the given color.
     */
    private function traceSegment(GdImage $image, string $segment, int $color, Quadrant $quadrant): void
    {
        match ($segment) {
            self::SEGMENT_TOP => $this->traceLine(
                $image,
                $quadrant->x1,
                $quadrant->y1 + $quadrant->yOffset,
                $quadrant->x2,
                $quadrant->y1 + $quadrant->yOffset,
                $color
            ),
            self::SEGMENT_BOTTOM => $this->traceLine(
                $image,
                $quadrant->x1,
                $quadrant->y2 + $quadrant->yOffset,
                $quadrant->x2,
                $quadrant->y2 + $quadrant->yOffset,
                $color
            ),
            self::SEGMENT_DIAG_ONE => $this->traceLine(
                $image,
                $quadrant->x1,
                $quadrant->y1,
                $quadrant->x2,
                $quadrant->y2,
                $color
            ),
            self::SEGMENT_DIAG_TWO => $this->traceLine(
                $image,
                $quadrant->x1,
                $quadrant->y2 + $quadrant->yOffset,
                $quadrant->x2 + $quadrant->xOffset,
                $quadrant->y1,
                $color
            ),
            self::SEGMENT_RIGHT => $this->traceLine(
                $image,
                $quadrant->x2 - $quadrant->xOffset,
                $quadrant->y1,
                $quadrant->x2 - $quadrant->xOffset,
                $quadrant->y2,
                $color
            ),
            default => null,
        };
    }

    /**
     * Traces the given digit in the given quadrant with the given color.
     */
    private function traceDigit(GdImage $image, int $digit, int $color, Quadrant $quadrant): void
    {
        foreach (self::SEGMENTS[$digit] as $segment) {
            $this->traceSegment($image, $segment, $color, $quadrant);
        }
    }

    /**
     * Traces a line in the given image with the given color.
     */
    private function traceLine(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $color): void
    {
        imagesetthickness($image, $this->lineThickness);
        imageline($image, $x1, $y1, $x2, $y2, $color);
    }
}
