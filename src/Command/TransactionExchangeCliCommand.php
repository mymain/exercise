<?php

declare(strict_types=1);

namespace App\Command;

use App\Messenger\Command\TransactionExchangeCommand;
use App\Dto\TransactionExchangeDto;
use App\Entity\Transaction;
use App\Trait\GetEnvelopeResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:transaction:exchange')]
class TransactionExchangeCliCommand extends Command
{
    use GetEnvelopeResult;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Cli Transaction Exchange',
            '========================',
            '',
        ]);

        $helper = $this->getHelper('question');

        $baseAmount = (int) $helper->ask($input, $output, new Question('Please provide amount to exchange: '));
        $baseCurrency = $helper->ask($input, $output, new Question('Please provide base currency: '));
        $targetCurrency = $helper->ask($input, $output, new Question('Please provide target currency: '));

        $exchangeDto = new TransactionExchangeDto(
            baseCurrency: $baseCurrency,
            baseAmount: $baseAmount,
            targetCurrency: $targetCurrency,
        );

        try {
            $envelope = $this->commandBus->dispatch(new TransactionExchangeCommand(
                exchangeDto: $exchangeDto,
                ip: 'cli-command',
            ));

            /** @var Transaction $transaction */
            $transaction = $this->getEnvelopeResult($envelope);

            $output->writeln([
                'Transaction created:',
                '===================',
                'Id: ' . $transaction->id,
                'Rate: ' . $transaction->exchangeRate,
                'Target amount: ' . $transaction->targetAmount,
            ]);
        } catch (HandlerFailedException $exception) {
            $output->writeln([
                'Error occurred:',
                '==============',
                $exception->getMessage(),
            ]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
