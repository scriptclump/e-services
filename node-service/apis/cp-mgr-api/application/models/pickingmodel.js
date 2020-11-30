const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;
const role = require('../controllers/role');

module.exports = {
	/**
	 * [getAssignedVerificationList To get assigned verification list in between given date and assigned to input user]
	 * @param  {[string]} from_date [From date]
	 * @param  {[string]} to_date   [To date]
	 * @param  {[int]} user_id   [user id]
	 */
	getAssignedVerificationList: function (from_date, to_date, user_id) {
		let fromdate = from_date + " 00:00:00";
		let todate = to_date + " 23:59:59";
		return new Promise((resolve, reject) => {
			role.getWarehouseData(user_id, 6).then(hubdata => {
				let query = "SELECT go.order_code,go.gds_order_id, go.hub_id, lw.lp_wh_name, got.`checker_id`, CONCAT(u.firstname,' ',u.lastname) AS checker_name FROM gds_order_track got JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id JOIN gds_orders go ON go.gds_order_id = got.gds_order_id JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id JOIN users u ON u.`user_id`=got.`checker_id` WHERE pcm.is_verified = '0' AND got.checker_id IS NOT NULL AND go.`order_status_id` in (17005,17021) AND go.order_date BETWEEN '" + fromdate + "' AND '" + todate + "' and go.hub_id in (" + hubdata + ") GROUP BY go.order_code";
				db.query(query, {}, (err, res) => {
					if (err) {
						console.log(err);
						resolve([]);
					}else{
						console.log('success');
						resolve(res);
					}
				});
			},err=>{
				resolve([]);
			});	
		});
	

	},
	/**
	 * [getPendingVerificationList To get pending verication list assigned to a user in the given dates]
	 * @param  {[string]} from_date  [from date]
	 * @param  {[string]} to_date    [to date]
	 * @param  {[int]} checker_id [user id]
	 */
	getPendingVerificationList: function (from_date, to_date, checker_id) {
		let fromdate = from_date + " 00:00:00";
		let todate = to_date + " 23:59:59";
		return new Promise((resolve, reject) => {
			role.getWarehouseData(checker_id, 6).then(hubdata => {
				let query = "SELECT go.order_code,got.checker_id,getLeWhName(go.hub_id) as hub_name,group_concat(distinct pcm.container_barcode) as container_barcode FROM gds_order_track got JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id JOIN gds_orders go ON go.gds_order_id = got.gds_order_id WHERE           got.checker_id = ? AND pcm.is_verified = '0' AND go.`order_status_id` in (17005,17021) AND pcm.created_at between ? and ? and go.hub_id in (" + hubdata + ") GROUP BY go.order_code,go.gds_order_id";
				db.query(query, [checker_id, fromdate, todate], (err, res) => {
					if (err) {
						console.log(err);
						resolve([]);
					}else{
						console.log('success');
						//console.log(res);
						resolve(res);
					}
				});
			},err=>{
				resolve([]);
			});	
		});
	},
	/**
	 * [getRtdOrdersList To get RTD orders list]
	 * @param  {[string]} from_date [from date]
	 * @param  {[string]} to_date   [to date]
	 * @param  {[int]} user_id   [RTD orders list]
	 */
	getRtdOrdersList: function (from_date, to_date, user_id) {
		let fromdate = from_date + " 00:00:00";
		let todate = to_date + " 23:59:59";
		return new Promise((resolve, reject) => {
			role.getWarehouseData(user_id, 6).then(hubdata => {
				let query = "SELECT go.order_code,go.gds_order_id, go.hub_id, lw.lp_wh_name FROM gds_order_track got LEFT JOIN picker_container_mapping pcm ON got.gds_order_id = pcm.order_id JOIN gds_orders go ON go.gds_order_id = got.gds_order_id JOIN legalentity_warehouses lw ON lw.le_wh_id = go.hub_id WHERE pcm.is_verified = '0'           AND got.checker_id IS NULL AND go.`order_status_id` in (17005,17021) AND go.order_date BETWEEN '" + fromdate + "' AND '" + todate + "' AND go.hub_id in (" + hubdata + ") GROUP BY go.order_code";
				db.query(query, {}, (err, res) => {
					if (err) {
						console.log(err);
						resolve([]);
					}else{
						console.log('success');
						//	console.log(res);
						resolve(res);
					}
				});
			},err=>{
				resolve([]);
			});	
		});
    }
}