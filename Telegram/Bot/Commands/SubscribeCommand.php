<?php

namespace App\Telegram\Bot\Commands;

use App\User;
use App\TelegramAccount;

class SubscribeCommand extends GuestCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'subscribe';

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
                ['text' => 'Register', 'callback_data' => 'command=register&arguments=' . $this->arguments],
                ['text' => 'Login', 'callback_data' => 'command=login'],
            ],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Subscription</b>
Welcome to Priva trade
Where you can make 4% in day and in 40 days 160%.
You can either login into the bot or register now.
EOF
        ]);
    }
}
