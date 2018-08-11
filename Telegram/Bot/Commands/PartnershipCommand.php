<?php

namespace App\Telegram\Bot\Commands;

class PartnershipCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'partnership';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $inlineKeyboard = [
            [
                ['text' => 'Statistics', 'callback_data' => 'command=partnership_statistics'],
                ['text' => 'Promo Material', 'callback_data' => 'command=promo_stuff'],
            ],
            [
                ['text' => 'Commissions', 'callback_data' => 'command=partnership_commissions'],
                ['text' => 'Downline', 'callback_data' => 'command=partnership_downline'],
            ],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Partnership Network</b>
Make 10% referral bonus on each user referred into our program by you .
You can also make bonus without investment.
EOF
        ]);
    }
}
