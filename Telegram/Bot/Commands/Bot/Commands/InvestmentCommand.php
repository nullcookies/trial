<?php

namespace App\Telegram\Bot\Commands;

class InvestmentCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'investment';

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
            [['text' => "\u{1F49B} Bitcoin", 'callback_data' => 'command=bitcoin_investment']],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>New Investment</b>
Choose your Preferred method of deposit.

<b>Please Note: We will be adding other payment processors along the way.</b>
EOF
        ]);
    }
}
