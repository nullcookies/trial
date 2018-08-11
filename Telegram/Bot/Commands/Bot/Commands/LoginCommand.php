<?php

namespace App\Telegram\Bot\Commands;

use Hash;
use App\User;
use App\TelegramAccount;

class LoginCommand extends GuestCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'login';

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
            $this->validateEmailAndAskForPassword($state->data);
        } elseif ('waiting_for_password' === $state->status) {
            $this->validatePasswordAndLogin($state->data);
        }
    }

    protected function welcomeAndAskForEmail()
    {
        $this->replyWithMessage(['text' => <<<EOL
Please enter your Email address:
EOL
        ]);

        $this->saveState('waiting_for_email');
    }

    protected function validateEmailAndAskForPassword($data)
    {
        $email = $this->getText();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->handleInvalidEmail();
        }

        $this->replyWithMessage(['text' => <<<EOL
Please enter your password:
EOL
        ]);

        $this->saveState('waiting_for_password', ['email' => $email]);
    }

    protected function validatePasswordAndLogin($data)
    {
        $email = $data['email'];
        $password = $this->getText();

        $user = User::where('email', $email)->first();

        if (!isset($user) or !Hash::check($password, $user->password)) {
            return $this->handleInvalidCredentials();
        }

        $account = new TelegramAccount();
        $account->telegram_id = $this->getId();
        $account->telegram_chat_id = $this->getChatId();

        $user->telegramAccounts()->save($account);

        $this->resetState();

        $this->replyWithMessageAndBack(['text' => <<<EOL
You have successfully logged in.
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

    protected function handleInvalidCredentials()
    {
        $this->replyWithMessageAndBack(['text' => <<<EOL
These credentials do not match our records.
EOL
        ]);
    }
}
