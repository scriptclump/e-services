<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Central\Repositories\MailMongo;
//use App\Central\Repositories\OrderRepo;
use App\Modules\Orders\Models\OrderModel;

class TaxmailConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $mailmongo;
    protected $_ordermodel;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'taxmail {-templatename} 
                                   {-orderid} {-data} {-response}
                                   ';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->mailmongo = new MailMongo();
        $this->_ordermodel = new OrderModel();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        $templatename = $arguments['-templatename'];
        $orderid = $arguments['-orderid'];
        $data = $arguments['-data'];
        $response = $arguments['-response'];
        

        if(!is_null($orderid) && !is_null($templatename) && !is_null($response) && !is_null($data)){

            $this->info('Moving to mongo class will handle the request');
            $status =  $this->mailmongo->sendmailtax($templatename,$orderid,$data,$response);
            //$subject = $subject . ' Order Id ' . $orderid;
            $this->info($status);
        }else{

            $this->error('Something went wrong!');
        }
       

    }
}
