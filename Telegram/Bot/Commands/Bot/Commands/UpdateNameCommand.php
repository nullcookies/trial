<?php

namespace App\Telegram\Bot\Commands;

class UpdateNameCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'update_name';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $state = $this->state();

        if (null === $state) {
            $this->welcomeAndAskForName();
        } elseif ('waiting_for_name' === $state->status) {
            $this->validateNameAndUpdate($state->data);
        }
    }

    protected function welcomeAndAskForName()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
Please enter your new name:
EOF
        ]);

        $this->saveState('waiting_for_name');
    }

    protected function validateNameAndUpdate($data)
    {
        $name = $this->getText();

        if ('' === trim($name)) {
            return $this->handleInvalidName();
        }

        $user = $this->getUser();

        $user->update(['name' => $name]);

        $this->resetState();

        $this->replyWithMessageAndBack(['text' => <<<EOL
Your name successfully updated.
EOL
        ]);
    }

    protected function handleInvalidName()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Invalid name provided. your name can not be empty.
Please enter a valid name:
EOL
        ]);
    }
}
