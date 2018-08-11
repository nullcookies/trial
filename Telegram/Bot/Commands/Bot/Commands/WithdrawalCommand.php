<?php

namespace App\Telegram\Bot\Commands;

use App\Investment;
use App\Withdrawal;
use App\BitcoinWithdrawal;
use LinusU\Bitcoin\AddressValidator as BitcoinAddressValidator;

class WithdrawalCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'withdrawal';

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
        $method = $this->arguments;

        switch ($method) {
            case 'bitcoin':
                return $this->bitcoin();
            default:
                return $this->unsupportedMethod();
        }
    }

    protected function unsupportedMethod()
    {
        $this->replyWithMessageAndBack([
            'text' => <<<EOF
Unsupported method.
EOF
        ]);
    }

    protected function bitcoin()
    {
        $state = $this->state();

        if (null === $state) {
            $this->askForBitcoinAmount();
        } elseif ('waiting_for_amount' === $state->status) {
            $this->validateAmountAndAskForBitcoinAddress($state->data);
        } elseif ('waiting_for_bitcoin_address' === $state->status) {
            $this->validateBitcoinAddressAndWithdrawal($state->data);
        }
    }

    protected function askForBitcoinAmount()
    {
        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
How much do you want to withdraw?

<b>Please Note: Minimum withdrawal is 0.0025 we do not charge any fees for withdrawal (Transaction fee may be applied by your e-wallet operator)</b>
EOF
        ]);

        $this->saveState('waiting_for_amount');
    }

    protected function validateAmountAndAskForBitcoinAddress()
    {
        $amount = $this->getText();

        if (!is_numeric($amount)) {
            return $this->invalidAmount();
        }

        if (0.0025 > $amount) {
            return $this->invalidAmount();
        }

        $user = $this->getUser();

        if ($user->bitcoin_balance < $amount) {
            return $this->invalidAmount();
        }

        $this->replyWithMessage([
            'text' => <<<EOF
Please enter your Bitcoin address:
EOF
        ]);

        $this->saveState('waiting_for_bitcoin_address', ['amount' => $amount]);
    }

    protected function validateBitcoinAddressAndWithdrawal($data)
    {
        $address = $this->getText();

        $version = app()->environment('production') ? BitcoinAddressValidator::MAINNET : BitcoinAddressValidator::TESTNET;

        if (!BitcoinAddressValidator::isValid($address, $version)) {
            return $this->invalidBitcoinAddress();
        }

        $amount = $data['amount'];

        $user = $this->getUser();

        $methodWithdrawal = new BitcoinWithdrawal();
        $methodWithdrawal->address = $address;

        $withdrawal = new Withdrawal();
        $withdrawal->user_id = $user->id;
        $withdrawal->amount = $amount;
        $withdrawal->method = Withdrawal::METHOD_BITCOIN;

        $user->bitcoin_balance -= $amount;

        $user->save();
        $user->withdrawals()->save($withdrawal);
        $withdrawal->methodWithdrawal()->save($methodWithdrawal);

        $this->resetState();

        $this->replyWithMessageAndBack([
            'text' => <<<EOF
Your withdrawal has been successfully processed. It will be added to your wallet in 10min to 48hours depending on Bitcoin transaction Traffic.
EOF
        ]);
    }

    protected function invalidAmount()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Invalid amount provided.
Please enter a valid amount:
EOL
        ]);
    }

    protected function invalidBitcoinAddress()
    {
        $this->replyWithCancelableMessage(['text' => <<<EOL
Invalid Bitcoin address provided.
Please enter a valid Bitcoin address:
EOL
        ]);
    }
}
