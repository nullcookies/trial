<?php

namespace App\Telegram\Bot\Commands;

use App\TelegramAccount;
use App\Request;
use App\TelegramState;
use Telegram\Bot\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    public function getMessage()
    {
        return $this->getUpdate()->getMessage();
    }

    public function getText()
    {
        return optional($this->getMessage())->getText();
    }

    public function getCallbackQuery()
    {
        return $this->getUpdate()->getCallbackQuery();
    }

    public function getFrom()
    {
        $type = $this->getUpdate()->detectType();

        switch ($type) {
            case 'message':
                return $this->getMessage()->getFrom();
            case 'callback_query':
                return $this->getCallbackQuery()->getFrom();
            default:
                return null;
        }
    }

    public function getId()
    {
        return $this->getFrom()->getId();
    }

    public function getChatId()
    {
        return $this->getUpdate()->getChat()->getId();
    }

    public function getFullName()
    {
        $from = $this->getFrom();

        $firstName = $from->getFirstName();
        $lastName = $from->getLastName();

        if ('' == $lastName) {
            return $firstName;
        } else {
            return $firstName . ' ' . $lastName;
        }
    }

    public function getMe()
    {
        return $this->getTelegram()->getMe();
    }

    public function getUser()
    {
        return optional(TelegramAccount::where('telegram_id', $this->getId())->first())->user;
    }

    public function isSubscribed()
    {
        return null !== $this->getUser();
    }

    public function saveState($status, $data = [])
    {
        $state = new TelegramState();
        $state->telegram_id = $this->getId();
        $state->status = $status;
        $state->command = $this->getName();
        $state->arguments = $this->getArguments();
        $state->data = $data;
        $state->save();
    }

    public function resetState()
    {
        TelegramState::reset($this->getId());
    }

    public function state()
    {
        $state = TelegramState::last($this->getId());

        if (null === $state) {
            return;
        }

        if ('none' === $state->status) {
            return;
        }

        if ($state->command !== $this->getName()) {
            return;
        }

        return $state;
    }

    public function replyWithCancelableMessage(array $params)
    {
        $inlineKeyboard = [
            [['text' => "\u{274C} Cancel", 'callback_data' => 'command=cancel']],
        ];

        $replyMarkup = [
            'inline_keyboard' => $inlineKeyboard,
        ];

        $params['reply_markup'] = json_encode($replyMarkup);

        $this->replyWithMessage($params);
    }

    public function replyWithMessageAndBack(array $params)
    {
        $this->replyWithMessage($params);

        $this->triggerCommand('main');
    }

    abstract protected function execute();

    public function handle($arguments)
    {
        $this->execute();
    }
}
