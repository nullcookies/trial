<?php

namespace App\Telegram\Bot\Commands;
Use DB;
class MainCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'main';
    

    /**
     * @var string Command Description
     */
    protected $description = '';

    /**
     * @inheritdoc
     */
    protected function execute()
    {
    	$user = $this->getUser();
    	$ID = DB::table('users')
        ->select('is_admin')
        ->where('users.email', $user->email)
        ->value('is_admin');
        
        $keyboard = [];
	
        if ($this->isSubscribed()) {
            $keyboard[] = ["\u{1F4B0} New Investment"];
            $keyboard[] = ["\u{1F3C6} Partnership", "\u{1F4C8} Statistics", "\u{1F6E0} Settings"];
        } else {
            $keyboard[] = ['Subscribe'];
        }

        $keyboard[] = ["\u{1F465} About Us", "\u{1F30D} Socials", "\u{1F198} Support"];
        if ($ID == 1){
            $keyboard[] = ["\u{1F4E2} Broadcast"];
        }

        $replyMarkup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
        ];

        $this->replyWithMessage([
            'reply_markup' => json_encode($replyMarkup),
            'parse_mode' => 'HTML',
        	'text' => <<<EOF
<b>Main Menu:</b>
EOF
        ]);
    }
}
