<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Communication\Models\Communication;

class MessageQueuesConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MessageQueues {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Message Queues';
    protected $communicationModel;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->communicationModel = new Communication();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Message Started.....');
        $arguments = $this->argument();
        $id = $arguments['id'];
        $message = $this->communicationModel->processPendingMessages($id);
        $this->info($message);
    }
}
