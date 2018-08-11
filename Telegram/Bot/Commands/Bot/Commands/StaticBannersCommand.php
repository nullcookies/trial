<?php

namespace App\Telegram\Bot\Commands;

class StaticBannersCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'static_banners';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $me = $this->getMe();
        $user = $this->getUser();

        $directReferralLink = route('home', ['ref' => $user->referral_token]);
        $telegramReferralLink = "https://telegram.me/{$me->getUsername()}?start={$user->referral_token}";

        $inlineKeyboard = [
            [
                ['text' => '125x125 Banner', 'url' => 'http://example.com'],
                ['text' => '468x60 Banner', 'url' => 'http://example.com'],
                ['text' => '728x90 Banner', 'url' => 'http://example.com'],
            ],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Static Banners:</b>
EOF
        ]);
    }
}
