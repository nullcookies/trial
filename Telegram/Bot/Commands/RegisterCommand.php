<?php

namespace App\Telegram\Bot\Commands;

use App\User;
use App\TelegramAccount;

class RegisterCommand extends GuestCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'register';

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
            $this->validatePasswordAndRegister($state->data);
        }
    }

    protected function welcomeAndAskForEmail()
    {
        $this->replyWithMessage(['text' => <<<EOL
Please enter an Email address:
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
Please enter a password:
EOL
        ]);

        $this->saveState('waiting_for_password', ['email' => $email]);
    }

    protected function validatePasswordAndRegister($data)
    {
        $password = $this->getText();

        if (6 > mb_strlen($password)) {
            return $this->handleShortPassword();
        }

        $email = $data['email'];

        $emailCount = User::where('email', $email)->count();

        if (0 < $emailCount) {
            return $this->handleDublicateEmail();
        }

        $name = $this->getFullName();

        $referrerToken = $this->arguments;
        $referrerId = $this->resolveReferrerId($referrerToken);

        $account = new TelegramAccount();
        $account->telegram_id = $this->getId();
        $account->telegram_chat_id = $this->getChatId();

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->referrer_id = $referrerId;

        $user->save();
        $user->telegramAccounts()->save($account);

        $this->resetState();

        $this->replyWithMessageAndBack(['text' => <<<EOL
You have successfully subscribed.
EOL
        ]);
    }

    protected function resolveReferrerId($token)
    {
        return optional(User::select('id')->where('referral_token', $token)->first())->id;
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

    protected function handleShortPassword()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Password should be at least 6 character.
Please enter a valid password:
EOL
        ]);
    }
}
?>