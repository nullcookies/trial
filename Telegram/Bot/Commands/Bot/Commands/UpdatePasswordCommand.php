<?php

namespace App\Telegram\Bot\Commands;

use Hash;

class UpdatePasswordCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'update_password';

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
            $this->welcomeAndAskForCurrentPassword();
        } elseif ('waiting_for_current_password' === $state->status) {
            $this->validateCurrentPasswordAndAskForNewPassword($state->data);
        } elseif ('waiting_for_new_password' === $state->status) {
            $this->validateNewPasswordAndUpdate($state->data);
        }
    }

    protected function welcomeAndAskForCurrentPassword()
    {
        $this->replyWithMessage([
            'text' => <<<EOF
Please enter your current password:
EOF
        ]);

        $this->saveState('waiting_for_current_password');
    }

    protected function validateCurrentPasswordAndAskForNewPassword($data)
    {
        $currentPassword = $this->getText();

        $user = $this->getUser();

        if (!Hash::check($currentPassword, $user->password)) {
            return $this->handleInvalidCurrentPassword();
        }

        $this->replyWithMessage([
            'text' => <<<EOF
Please enter your new password:
EOF
        ]);

        $this->saveState('waiting_for_new_password');
    }

    protected function validateNewPasswordAndUpdate($data)
    {
        $newPassword = $this->getText();

        if (6 > mb_strlen($newPassword)) {
            return $this->handleShortPassword();
        }

        $user = $this->getUser();

        $user->update(['password' => bcrypt($newPassword)]);

        $this->resetState();

        $this->replyWithMessageAndBack(['text' => <<<EOL
Your password successfully updated.
EOL
        ]);
    }

    protected function handleInvalidCurrentPassword()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Provided password doesn't match with your current password.
Please enter the valid password:
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
