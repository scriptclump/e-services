<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Mongo\MongoResqueModel;

class UpdateResquetaskConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateResque {token} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $resquemongo = new MongoResqueModel();
        $token = $arguments['token'];
        $status = $arguments['status'];
        $resquemongo->updateQueueStatus($token,$status);

    }
}
