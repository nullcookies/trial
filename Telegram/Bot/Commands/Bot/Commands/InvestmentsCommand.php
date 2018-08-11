<?php

namespace App\Telegram\Bot\Commands;

class InvestmentsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'investments';

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
<b>Your Investments</b>
EOF
        ]);

        $user = $this->getUser();

        $investments = $user->investments()->ofStatus(['pending', 'started', 'ended', 'completed'])->get();

        if ($investments->isEmpty()) {
            $this->handleEmptyInvestments();
        } else {
            $this->handleInvestments($investments);
        }
    }

    protected function handleInvestments($investments)
    {
        foreach ($investments as $investment) {
            $this->replyWithMessage($this->buildInvestmentMessage($investment));
        }
    }

    protected function buildInvestmentMessage($investment)
    {
        $amountFormatted = float_number_format($investment->amount);
        $profitFormatted = float_number_format($investment->profit);

        $inlineKeyboard = [];

        if ($investment->isStarted() or ($investment->isCompleted() and $investment->profit)) {
            $inlineKeyboard[0][0] = ['text' => 'Add Profit to Balance', 'callback_data' => 'command=investment_profit_withdrawal&arguments=' . $investment->id];
        }

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $text = '';
        $text .= "\n<b>#{$investment->id}</b>";
        $text .= "\nAmount: <b>฿{$amountFormatted}</b>";
        if ($investment->isStarted() or $investment->isCompleted()) {
            $text .= "\nProfit: <b>฿{$profitFormatted}</b>";
        }
        $text .= "\nMethod: <b>{$investment->method}</b>";
        $text .= "\nStatus: <b>{$investment->status}</b>";
        if ($investment->isStarted() or $investment->isCompleted()) {
            $text .= "\nStarted at: <b>{$investment->started_at}</b>";
        }
        if ($investment->isCompleted()) {
            $text .= "\nCompleted at: <b>{$investment->completed_at}</b>";
        }

        return [
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => $text,
        ];
    }

    protected function handleEmptyInvestments()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
You do not have any investment yet.
EOF
        ]);
    }
}
