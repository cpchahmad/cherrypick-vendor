<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Store;
use DB;

class saveShoperappOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update otp on shoper app';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   	
        $data=Order::where('otp_status', 0)->get();
        foreach($data as $row)
        {
            $store=Store::find($row->vendor);
            $vendor_name=$store->name;
            $otp=$row->otp;
            $order_id=$row->shopify_order_id;
            $ch = curl_init();
			$postdata=array('vendor_name' => $vendor_name,'order_id' => $order_id,'otp' => $otp);
			curl_setopt($ch, CURLOPT_URL,"https://phpstack-711164-2355937.cloudwaysapps.com/save-otp");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			curl_close($ch);
			$result=json_decode($server_output, true);
			if($result['status']=='success')
			{
				Order::where('id', $row->id)->update(['otp_status' => '1']);
			}
        }
    }
}
