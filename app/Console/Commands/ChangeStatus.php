<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Change:Status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       dd('hi');
    }
}
