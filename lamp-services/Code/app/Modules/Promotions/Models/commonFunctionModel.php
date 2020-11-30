public function getpromotionData(){
		$getpromotionData = DB::table('promotion_template')->get()->all();
		return $getpromotionData;
	}

	public function getstate(id){
			$getstate = DB::table('zone')->where('country_id', '=', 99);
			$query = ->where('status', '=', '1')
					 ->where('name', 'not like', '%All%')
					 ->orderBy("sort_order");
			$concat = $getstate;
			if(id==1){
					$concat .= $query;
				}	
			return $concat->get()->all();
	}