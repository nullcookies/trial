<?php

namespace App\Telegram\Bot\Commands;

use App\User;

class UpdateEmailCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'update_email';

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
            $this->welcomeAndAskForEmail();
        } elseif ('waiting_for_email' === $state->status) {
            $this->validateEmailAndUpdate($state->data);
        }
    }

    protected function welcomeAndAskForEmail()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
Please enter your new Email address:
EOF
        ]);

        $this->saveState('waiting_for_email');
    }

    protected function validateEmailAndUpdate($data)
    {
        $email = $this->getText();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->handleInvalidEmail();
        }

        $user = $this->getUser();

        $emailCount = User::where('id', '!=', $user->id)->where('email', $email)->count();

        if (0 < $emailCount) {
            return $this->handleDublicateEmail();
        }

        $user->update(['email' => $email]);

        $this->resetState();

        $this->replyWithMessageAndBack(['text' => <<<EOL
Your Email address successfully updated.
EOL
        ]);
    }

    protected function handleInvalidEmail()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Invalid Email address provided.
Please enter a valid Email address:
EOL
        ]);
    }

    protected function handleDublicateEmail()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Provided Email address already exists in our database.
Please enter an other Email address:
EOL
        ]);
    }
}
