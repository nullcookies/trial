<?php

namespace App\Telegram\Bot\Commands;

class GifBannersCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'gif_banners';

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
                ['text' => '120x120 Banner', 'url' => asset('img/gif/120x120.gif')],
                ['text' => '250x250 Banner', 'url' => asset('img/gif/250x250.gif')],
            ],
            [
                ['text' => '728x90 Banner', 'url' => asset('img/gif/728x90.gif')],
                ['text' => '300x600 Banner', 'url' => asset('img/gif/300x600.gif')],
            ]
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>GIF Banners:</b>
EOF
        ]);
    }
}
