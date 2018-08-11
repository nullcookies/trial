<?php

namespace App\Telegram\Bot\Commands;

use App\Investment;

class InvestmentProfitWithdrawalCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'investment_profit_withdrawal';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $id = $this->arguments;

        if (null === $investment = Investment::find($id)) {
            $this->investmentNotFound($id);
        } else {
            $this->tryToWithdraw($investment);
        }
    }

    protected function investmentNotFound($id)
    {
        $this->replyWithMessageAndBack([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
Investment <b>#$id</b> not found.
EOF
        ]);
    }

    protected function tryToWithdraw($investment)
    {
        $profit = $investment->profit;

        if (0 >= $profit) {
            return $this->investmentHasNoProfit($investment);
        }

        $user = $this->getUser();

        $method = $investment->method;

        $investment->profit = 0;
        $user->{$method . '_balance'} += $profit;

        $investment->save();
        $user->save();

        $this->replyWithMessageAndBack([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
Your profit successfully added to your balance.
EOF
        ]);
    }

    protected function investmentHasNoProfit($investment)
    {
        $this->replyWithMessageAndBack([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
Investment <b>#{$investment->id}</b> has no profit yet.
EOF
        ]);
    }
}
