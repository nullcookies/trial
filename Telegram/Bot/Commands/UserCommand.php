<?php

namespace App\Telegram\Bot\Commands;

abstract class UserCommand extends Command
{
    public function handle($arguments)
    {
        if ($this->isSubscribed()) {
            parent::handle($arguments);
        } else {
            $this->notSubscribed();
        }
    }

    protected function notSubscribed()
    {
        $this->replyWithMessageAndBack(['text' => <<<EOL
You need to first subscribe to access this command.
EOL
        ]);
    }
}
