<?php

declare(strict_types=1);

namespace App\Cli;

use App\Messenger\Command\TransactionUpdateCommand;
use App\Dto\TransactionUpdateDto;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Trait\GetEnvelopeResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:transaction:update')]
final class TransactionUpdateCli extends Command
{
    use GetEnvelopeResult;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TransactionRepository $transactionRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Cli Transaction Update',
            '======================',
            '',
        ]);

        $helper = $this->getHelper('question');

        $transactionId = (int) $helper->ask($input, $output, new Question('Please provide transaction id: '));
        $targetCurrency = $helper->ask($input, $output, new Question('Please provide new target currency: '));

        try {
            $transactionUpdateDto = new TransactionUpdateDto(
                transactionId: $transactionId,
                targetCurrency: $targetCurrency,
            );
            $envelope = $this->commandBus->dispatch(new TransactionUpdateCommand(
                transactionUpdateDto: $transactionUpdateDto,
            ));

            /** @var Transaction $transaction */
            $transaction = $this->getEnvelopeResult($envelope);

            $output->writeln([
                'Transaction updated:',
                '===================',
                'Rate: ' . $transaction->getExchangeRate(),
                'Target amount: ' . $transaction->getTargetAmount(),
                'Target currency: ' . $transaction->getTargetCurrency(),
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
