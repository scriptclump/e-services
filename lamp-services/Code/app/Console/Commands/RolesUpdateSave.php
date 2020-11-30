<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Session;

use App\Central\Repositories\RoleRepo;
use App\Lib\Queue;
use Log;

class RolesUpdateSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RolesUpdate {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Update Roles';

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
        
        $arguments = $this->argument();
        $data = base64_decode($arguments['data']);
        
        $data = json_decode($data, true);

       
        $roles = new RoleRepo();
        $result=$roles->insertRoleperMission($data);
         
        //return true;
    }
}
