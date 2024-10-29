<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CistercianNumberGeneratorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate',
    description: 'Generate cistercian numbers from 1 to 9999',
)]
final class GenerateCommand extends Command
{
    public function __construct(
        private readonly CistercianNumberGeneratorService $cistercianNumberGeneratorService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numbers = range(1, 9999);

        foreach ($numbers as $number) {
            $this->cistercianNumberGeneratorService->generateCistercianNumber($number);
        }

        return Command::SUCCESS;
    }
}
