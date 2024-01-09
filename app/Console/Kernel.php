<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
       Commands\updateInventory::class,
       Commands\approveProducts::class,
       Commands\fetchOrders::class,
	   Commands\saveShoperappOtp::class,
       Commands\updateProduct::class,
	   Commands\fetchProductJson::class,
	   Commands\updatePrice::class,
       Commands\ChangeStatus::class,
	   Commands\updatePriceNewConvesionRate::class
   ];
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inventory:update')->everyFiveMinutes();
        //$schedule->command('products:approve')->everyMinute();
        //$schedule->command('fetch:orders')->everyMinute();
        //$schedule->command('update:product')->everyMinute();


        //comment by zain
//		$schedule->command('update:otp')->everyFiveMinutes();
		$schedule->command('update:price')->everyMinute()->withoutOverlapping();
//		$schedule->command('products:approve')->everyMinute()->withoutOverlapping();

        //comment because new approach is developed
//		$schedule->command('fetch:jsonproduct')->daily()->withoutOverlapping();


        $schedule->command('fetch:product-shopifyurl-main')->daily()->withoutOverlapping();
        $schedule->command('fetch:product-shopifyurl')->everyFiveMinutes();

        $schedule->command('fetch:product-shopifyurl-retry')->everyFiveMinutes()->withoutOverlapping();

        $schedule->command('update:priceConversionRate')->everyMinute();


        $schedule->command('products:approvenew')->everyMinute()->withoutOverlapping();
        $schedule->command('clear:old-records')->daily()->withoutOverlapping();

        $schedule->command('sync:api-categories')->daily()->withoutOverlapping();
        $schedule->command('sync:api-product-attributes')->daily()->withoutOverlapping();

        //comment because new approach is developed
//        $schedule->command('fetch:product-api')->daily()->withoutOverlapping();

//        $schedule->command('sync:api-inventory')->daily()->withoutOverlapping();


//        $schedule->command('fetch:jsonproductandupdateprice')->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
