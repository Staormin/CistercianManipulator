<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CistercianNumberGeneratorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:generate',
    description: 'Generate cistercian numbers from 1 to 9999',
)]
final class GenerateCommand extends Command
{
    private SymfonyStyle $io;

    private string $outputDirectory;

    public function __construct(
        private readonly CistercianNumberGeneratorService $cistercianNumberGeneratorService,
        #[Autowire('%kernel.project_dir%')]
        string $projectDirectory,
    ) {
        parent::__construct();
        $this->outputDirectory = $projectDirectory . '/output/CistercianNumbers';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Generating Cistercian numbers from 1 to 9999');

        $numbers = range(1, 9999);

        $this->io->progressStart(count($numbers));

        foreach ($numbers as $number) {
            $this->cistercianNumberGeneratorService->generateCistercianNumber($number);
            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        $outputDirectoryForDisplay = implode('/', array_slice(explode('/', $this->outputDirectory), -2));

        $this->io->success(
            sprintf('Cistercian numbers generated successfully! You can find them in %s/', $outputDirectoryForDisplay)
        );
        
        return Command::SUCCESS;
    }
}
