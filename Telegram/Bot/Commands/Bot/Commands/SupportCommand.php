<?php

namespace App\Telegram\Bot\Commands;

class SupportCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'support';

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
<b>Support Center</b>
For any issues with using our services Message our designated support to resolve your issue with our program.
@priva_admin
EOF
        ]);
    }
}
