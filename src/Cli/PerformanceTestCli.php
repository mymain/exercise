<?php

declare(strict_types=1);

namespace App\Cli;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(name: 'app:performance:test')]
final class PerformanceTestCli extends Command
{
    private const LOOPS_NO = 10000000;

    public function __construct(
        private readonly Stopwatch $stopwatch,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Executing performance test',
            '==========================',
            '',
        ]);

        $rates = ['somekey' => 'somevalue'];

        $this->stopwatch->start('isset');

        for ($i = 0; $i < self::LOOPS_NO; ++$i) {
            if (!isset($rates['somekey'])) {
                throw new Exception();
            }
        }

        $duration = $this->stopwatch->stop('isset');
        $output->writeln([
            'Isset duration: ' . $duration->getDuration(),
            '===============',
            '',
        ]);

        $this->stopwatch->start('null-coalescing');

        for ($i = 0; $i < self::LOOPS_NO; ++$i) {
            $rates['somekey'] ?? throw new Exception();
        }

        $duration = $this->stopwatch->stop('null-coalescing');
        $output->writeln([
            'Null coalescing duration: ' . $duration->getDuration(),
            '========================',
            '',
        ]);

        return Command::SUCCESS;
    }
}
