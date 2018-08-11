<?php

namespace App\Telegram\Bot\Commands;

class PartnershipDownlineCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'partnership_downline';

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
<b>Your Partnership Downline</b>
EOF
        ]);

        $user = $this->getUser();

        $referrals = $user->referrals;

        if ($referrals->isEmpty()) {
            $this->handleEmptyReferrals();
        } else {
            $this->handleReferrals($referrals);
        }
    }

    protected function handleReferrals($referrals)
    {
        foreach ($referrals as $referral) {
            $this->replyWithMessage($this->buildReferralMessage($referral));
        }
    }

    protected function buildReferralMessage($referral)
    {
        $totalCommissions = $referral->commissions()->where('referrer_id', $referral->referrer_id)->sum('amount');

        $totalCommissionsFormatted = float_number_format($totalCommissions);

        $text = '';
        $text .= "\n<b>#{$referral->id}</b>";
        $text .= "\nName: <b>{$referral->name}</b>";
        $text .= "\nTotal BTC Commissions: <b>฿$totalCommissionsFormatted</b>";

        return [
            'parse_mode' => 'HTML',
            'text' => $text,
        ];
    }

    protected function handleEmptyReferrals()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
You do not have any referral yet.
EOF
        ]);
    }
}
