<?php

/**
 *
 * Created By : Rasheed Ahamed Shaik
 * date : 30th Jan 2019
 * Description : Single base control is used for clearing cache after updating pricing.
 * 
 */

namespace App\Console\Commands;

date_default_timezone_set("Asia/Kolkata");

use Illuminate\Console\Command;
use App\Modules\Pricing\Controllers\uploadPriceSlabFiles;
use App\Modules\Pricing\Models\uploadSlabProductsModel;
use Log;
class clearcache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clearcache {cache_array}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Single base control is used for for clearing cache after updating pricing';

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
        Log::info("clearcache called");
        $arguments = $this->argument();
        $all_cache_array = base64_decode($arguments['cache_array']);
        $all_cache_array = json_decode($all_cache_array, true);
        $pricObj = new uploadPriceSlabFiles();
        $product_slab_details = new uploadSlabProductsModel();
        foreach ($all_cache_array as $key => $value) {
            Log::info(json_encode($value));
            $cache_key = $value['cache_key'];
            $cache_type = isset($value['cache_type']) ? $value['cache_type'] : "";
            $cache_array = isset($value['cache_array']) ? $value['cache_array'] : array();
            $pricObj->clearCache($cache_key,$cache_type,$cache_array);
        }

        return true;
        
    }
}
