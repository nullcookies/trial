<?php

namespace App\Telegram\Bot\Commands;

use App\Investment;
use App\Transaction;
use App\BitcoinTransaction;

class BitcoinInvestmentCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'bitcoin_investment';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $this->replyWithChatAction([
            'action' => 'typing',
        ]);

        $blockio = resolve('blockio');

        $user = $this->getUser();
        $address = $blockio->get_new_address()->data->address;

        $methodTransaction = new BitcoinTransaction();
        $methodTransaction->address = $address;

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->method = Transaction::METHOD_BITCOIN;

        $investment = new Investment();
        $investment->user_id = $user->id;
        $investment->method = Investment::METHOD_BITCOIN;

        $investment->save();
        $investment->transactions()->save($transaction);
        $transaction->methodTransaction()->save($methodTransaction);

        $link = route('transactions.complete', [$transaction->id]);

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Bitcoin Investment</b>
Use the following link to complete your investment:
{$link}
EOF
        ]);
    }
}
