<?php

namespace App\Telegram\Bot\Commands;

class WithdrawalsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'withdrawals';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Your Withdrawal Requests</b>
EOF
        ]);

        $user = $this->getUser();

        $withdrawals = $user->withdrawals()->ofStatus(['pending', 'proceeded'])->get();

        if ($withdrawals->isEmpty()) {
            $this->handleEmptyWithdrawals();
        } else {
            $this->handleWithdrawals($withdrawals);
        }
    }

    protected function handleWithdrawals($withdrawals)
    {
        foreach ($withdrawals as $withdrawal) {
            $this->replyWithMessage($this->buildWithdrawalMessage($withdrawal));
        }
    }

    protected function buildWithdrawalMessage($withdrawal)
    {
        $amountFormatted = float_number_format($withdrawal->amount);

        $text = '';
        $text .= "\n<b>#{$withdrawal->id}</b>";
        $text .= "\nAmount: <b>฿{$amountFormatted}</b>";
        $text .= "\nMethod: <b>{$withdrawal->method}</b>";
        $text .= "\nStatus: <b>{$withdrawal->status}</b>";
        $text .= "\nCreated at: <b>{$withdrawal->created_at}</b>";

        return [
            'parse_mode' => 'HTML',
            'text' => $text,
        ];
    }

    protected function handleEmptyWithdrawals()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
You do not have any withdrawal request yet.
EOF
        ]);
    }
}
