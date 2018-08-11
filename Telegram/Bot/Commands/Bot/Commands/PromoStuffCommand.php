<?php

namespace App\Telegram\Bot\Commands;

class PromoStuffCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'promo_stuff';

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
                ['text' => 'GIF Banners', 'callback_data' => 'command=gif_banners'],
                ['text' => 'Static Banners', 'callback_data' => 'command=static_banners'],
            ],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Promo Material</b>
Direct Referral Link:
{$directReferralLink}
Telegram Referral Link:
{$telegramReferralLink}
You can also use our advertising banners:
EOF
        ]);
    }
}
