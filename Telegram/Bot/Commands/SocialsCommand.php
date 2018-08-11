<?php

namespace App\Telegram\Bot\Commands;

class SocialsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'socials';

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
            [['text' => 'Facebook', 'url' => 'https://www.facebook.com/privarobot']],
            [['text' => 'Twitter', 'url' => 'https://twitter.com/priva_robot']],
            [['text' => 'Youtube', 'url' => 'https://www.youtube.com/channel/UCoLuM39Ef0aNrpcesGfttBA']],
            [['text' => 'Telegram', 'url' => 'https://t.me/joinchat/HVBE4U_9X7kQdkxye6fNFQ']],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Social Networks</b>
Follow us through our different media outlet and join our communities to follow up on bonuses, promos and crucial news pertaining to your involvement in our project.
\u{2764}\u{2764}\u{2764}
EOF
        ]);
    }
}
