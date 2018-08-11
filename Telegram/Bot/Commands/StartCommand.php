<?php

namespace App\Telegram\Bot\Commands;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'Start Command to get you started';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        if ($this->isSubscribed()) {
            $this->triggerCommand('main');
        } else {
            $this->triggerCommand('subscribe');
        }
    }
}
