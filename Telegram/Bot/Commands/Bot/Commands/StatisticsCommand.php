<?php

namespace App\Telegram\Bot\Commands;

use App\Investment;

class StatisticsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'statistics';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $user = $this->getUser();

        $balance = $user->bitcoin_balance;
        $activeCount = $user->investments()->ofStatus('started')->count();
        $completedCount = $user->investments()->ofStatus('completed')->count();
        $totalInvested = $user->investments()->ofStatus(['started', 'completed'])->sum('amount');
        $totalProfit = $user->investments()->ofStatus(['started', 'completed'])->sum('profit');

        $balanceFormatted = float_number_format($balance);
        $totalInvestedFormatted = float_number_format($totalInvested);
        $totalProfitFormatted = float_number_format($totalProfit);

        $inlineKeyboard = [
            [['text' => 'Withdraw Balance', 'callback_data' => 'command=withdrawal&arguments=bitcoin']],
            [['text' => 'My Investments', 'callback_data' => 'command=investments']],
            [['text' => 'My Withdrawal Requests', 'callback_data' => 'command=withdrawals']],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Your Statistics</b>
Your BTC balance: <b>฿$balanceFormatted</b>
Started Investments: <b>$activeCount</b>
Completed Investments: <b>$completedCount</b>
Total BTC Invested: <b>฿$totalInvestedFormatted</b>
Total BTC Profit: <b>฿$totalProfitFormatted</b>
EOF
        ]);
    }
}
