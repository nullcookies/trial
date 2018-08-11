<?php

namespace App\Telegram\Bot\Commands;

class SettingsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'settings';

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

        $inlineKeyboard = [
            [['text' => "Name: {$user->name}", 'callback_data' => 'command=update_name']],
            [['text' => "Email: {$user->email}", 'callback_data' => 'command=update_email']],
            [['text' => 'Password', 'callback_data' => 'command=update_password']],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Settings</b>
What do you want to update?
EOF
        ]);
    }
}
