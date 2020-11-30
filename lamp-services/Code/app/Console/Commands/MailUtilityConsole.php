<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Utility;
use Log;

class MailUtilityConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'mailUtility {data}';


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
        $data = $arguments['data'];        
        $data = base64_decode($data);
        $data = json_decode($data,true);
        Log::info('email Console');
        Log::info("-------");
        //Log::info($data);
        \Utility::sendEmailQueue($data['mailTo'], $data['subject'], $data['body']);
        Log::info('after send email');
    }
}
