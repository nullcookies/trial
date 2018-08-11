<?php

namespace App\Telegram\Bot\Commands;

class PartnershipCommissionsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'partnership_commissions';

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
<b>Your Partnership Commissions</b>
EOF
        ]);

        $user = $this->getUser();

        $commissions = $user->referralCommissions;

        if ($commissions->isEmpty()) {
            $this->handleEmptyCommissions();
        } else {
            $this->handleCommissions($commissions);
        }
    }

    protected function handleCommissions($commissions)
    {
        foreach ($commissions as $commission) {
            $this->replyWithMessage($this->buildCommissionMessage($commission));
        }
    }

    protected function buildCommissionMessage($commission)
    {
        $amountFormatted = float_number_format($commission->amount);

        $text = '';
        $text = "\nUser: <b>#{$commission->referred_id}</b>";
        $text = "\nMethod: <b>{$commission->method}</b>";
        $text = "\nType: <b>{$commission->type}</b>";
        $text = "\nPercent: <b>{$commission->percent}%</b>";
        $text = "\nAmount: <b>฿{$amountFormatted}</b>";
        $text = "\nCreated at: <b>฿{$commission->created_at}</b>";

        return [
            'parse_mode' => 'HTML',
            'text' => $text,
        ];
    }

    protected function handleEmptyCommissions()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
You do not have any referral commission yet.
EOF
        ]);
    }
}
