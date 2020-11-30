<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\WarehouseConfig\Models\WarehouseConfigApi;

class PutawayListUpdatesTrigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:putawayListUpdateStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We are updating every hour for putaway and sales return status in hold.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $whObj = new WarehouseConfigApi();
        $message= $whObj->binAllocationTrigger();
        $this->info($message);
    }
}
