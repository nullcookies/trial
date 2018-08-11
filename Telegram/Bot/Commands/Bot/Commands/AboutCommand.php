<?php

namespace App\Telegram\Bot\Commands;

class AboutCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'about';

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
<b>About Priva</b>
Priva is an online platform which helps you trade your investments in the best available trading platforms. You do almost nothing after investing as our automated system does it all. The only currency we accept for now is bitcoin. With Priva, you earn 4% daily for 40 days meaning that you get 160% after 40 days. All the process involved in transacting with us have been made very easy. Our support team are always ready to address any issues. You earn 10% on every deposit of your referral. The only informations required of you by Priva are your email and your name.
To learn more visit https://privarobot.trade/faq for more questions.
EOF
        ]);
    }
}
