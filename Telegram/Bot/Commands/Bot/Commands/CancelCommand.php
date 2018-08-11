<?php

namespace App\Telegram\Bot\Commands;

class CancelCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'cancel';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $this->resetState();

        $this->triggerCommand('main');
    }
}
