<?php

namespace App\Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
        public $timestamps = false;
        protected $fillable = ['legal_entity_id','est_year','sup_add1','sup_add2','sup_country','sup_state','sup_city','sup_pincode','sup_account_name','sup_bank_name','sup_account_no','sup_account_type','sup_ifsc_code','sup_branch_name','sup_micr_code','sup_currency_code'];
	protected $table = "space";
	protected $primaryKey = 'space_id';
}
