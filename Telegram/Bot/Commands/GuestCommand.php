<?php

namespace App\Telegram\Bot\Commands;

abstract class GuestCommand extends Command
{
    public function handle($arguments)
    {
        if ($this->isSubscribed()) {
            $this->alreadySubscribed();
        } else {
            parent::handle($arguments);
        }
    }

    protected function alreadySubscribed()
    {
        $this->replyWithMessageAndBack(['text' => <<<EOL
You are already subscribed.
EOL
        ]);
    }
}
