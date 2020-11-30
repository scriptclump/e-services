<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class updateGdsTax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateGdsTax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updateGdsTax';

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
        $query = DB::table('gds_orders_tax')
                ->select('*')->where('gds_order_prod_id',NULL)->get()->all();
        $count_unmannaged = count($query);
        $bar = $this->output->createProgressBar($count_unmannaged);
        $bar->setEmptyBarCharacter('*');
        $bar->setBarWidth(100);
        foreach ($query as $value) {
            
            
            $products = DB::table('gds_order_products')
                ->select('gds_order_prod_id')
                ->where('gds_order_id',$value->gds_order_id)
                ->where('product_id',$value->product_id)                
                ->get()->all();
            $products = $products[0];
            DB::statement( "UPDATE gds_orders_tax SET gds_orders_tax.gds_order_prod_id = $products->gds_order_prod_id WHERE gds_orders_tax.gds_order_id = $value->gds_order_id AND gds_orders_tax.product_id = $value->product_id");
            $bar->advance();

        }
        $bar->finish();
        $query = DB::table('gds_orders_tax')
                ->select('*')->where('gds_order_id',NULL)
                ->where('product_id',NULL)->get()->all();
        $count_unmannaged = count($query);
        $bar = $this->output->createProgressBar($count_unmannaged);
        $bar->setEmptyBarCharacter('*');
        $bar->setBarWidth(100);
        foreach ($query as $value) {
            $products = DB::table('gds_order_products')
                ->select('gds_order_id','product_id')
                ->where('gds_order_prod_id',$value->gds_order_prod_id)
                ->get()->all();
            $products = $products[0];
           DB::statement("UPDATE gds_orders_tax SET gds_orders_tax.gds_order_id = $products->gds_order_id,gds_orders_tax.product_id = $products->product_id WHERE gds_orders_tax.gds_order_prod_id = $value->gds_order_prod_id");
            $bar->advance();
        }
        $bar->finish();

    }
}
