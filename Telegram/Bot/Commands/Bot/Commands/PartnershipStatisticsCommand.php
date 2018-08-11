<?php

namespace App\Telegram\Bot\Commands;

use DB;

class PartnershipStatisticsCommand extends UserCommand
{
    /**
     * @var string Command Name
     */
    protected $name = 'partnership_statistics';

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
        $email = $user->email;
        $ID = DB::table('users')
        ->select('id')
        ->where('email', $email)
        ->value('id');
        
        $totalReferrals = $user->referrals()->count();
        $activeReferrals = DB::table('investments')
        ->join('users', 'users.id', '=', 'investments.user_id')
        ->select('users.name')
        ->whereRaw('users.id = investments.user_id')
        ->whereRaw('investments.amount > 0')
        ->whereRaw('users.referrer_id = ?',[$ID])
        ->distinct()
                ->count('users.name');
        	
        $inactiveReferrals = DB::table('investments')
        ->join('users', 'users.id', '=', 'investments.user_id')
        ->select('users.name')
        ->whereRaw('users.id = investments.user_id')
        ->whereRaw('investments.amount IS NULL')
        ->whereRaw('users.referrer_id = ?',[$ID])
        ->distinct()
                ->count('users.name');
                
            	#$query->select(DB::raw(1))
                #->from('users')
                #->where('investments.user_id', '=', 'users.id');
                #->where('investments.user_id', '=', 'users.id');

        $totalCommissions = $user->referralCommissions()->sum('amount');

        $totalCommissionsFormatted = float_number_format($totalCommissions);

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => <<<EOF
<b>Your Partnership Statistics</b>
Total Referrals: <b>$totalReferrals</b>
Active Referrals: <b>$activeReferrals</b>
Inactive Referrals: <b>$inactiveReferrals</b>
Total BTC Commissions: <b>฿$totalCommissionsFormatted</b>
EOF
        ]);
    }
}