<?php
	
namespace App\models\MasterLookup;
use Illuminate\Database\Eloquent\Model;

class MasterLookup extends Model {

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'master_lookup';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        public function getInventoryModes() {
            $fieldArr = array('master_lookup.description as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'invetory_mode');
            $inventory_modes = $query->get()->all();
            return $inventory_modes;
	}
	public function getMaginTypes() {
            $fieldArr = array('master_lookup.description as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'margin_types');
            $margin_types = $query->get()->all();
            return $margin_types;
	}
	public function getReturnLocationTypes() {
            $fieldArr = array('master_lookup.description as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'returns_location_type');
            $returns_location_type = $query->get()->all();
            return $returns_location_type;
	}
	public function getLengthUOM() {
            $fieldArr = array('master_lookup.description as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Length UOM');
            $Length_UOM = $query->get()->all();
            return $Length_UOM;
	}
	public function getCapacityUOM() {
            $fieldArr = array('master_lookup.description as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Capacity UOM');
            $Capacity_UOM = $query->get()->all();
            return $Capacity_UOM;
	}
	public function getKVI() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'KVI');
            $KVI = $query->get()->all();
            return $KVI;
	}
	public function getPackType() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Pack Type');
            $PackType = $query->get()->all();
            return $PackType;
	}

	public function getShelfLife() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Shelf Life UOM');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getProductForm() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Product Form');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getLicenseType() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'License Type ');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getPrefferedChannels() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Preferred Channels');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getPopularity() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Popularity');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getTaxType() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'Tax Types');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getAtpPeriod() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_name', 'ATP Period');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getPackSizeUOM() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.description', 'Package Weight UoM');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getEachesLookup() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.description', 'Packing levels');
            $PackType = $query->get()->all();
            return $PackType;
	}
	public function getSupplierDCRel() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.description', 'Supplier DC relationship');
            $dcRel = $query->get()->all();
            return $dcRel;
	}
	public function getSuppliersRank() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.description', 'Supplier Rank');
            $dcRel = $query->get()->all();
            return $dcRel;
	}
	public function getGrnDays() {
            return array('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY');
	}
	public function getOfferPackLookup() {
            $fieldArr = array('master_lookup.master_lookup_name as name', 'master_lookup.value');
            $query = $this->select($fieldArr);
            $query->join('master_lookup_categories','master_lookup_categories.mas_cat_id','=','master_lookup.mas_cat_id');
            $query->where('master_lookup_categories.mas_cat_id', 102);
            $dcRel = $query->get()->all();
            return $dcRel;
	}
}
