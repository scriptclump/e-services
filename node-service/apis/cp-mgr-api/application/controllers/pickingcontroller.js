const picking = require('../models/pickingmodel');
const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;

module.exports = {
	/**
	 * [getAssignedVerificationList To get assigned verifications list in the given date]
	 * @param  {[array]} req [user_token, from_time, to_time]
	 * @param  {[object]} res [Assigned orders list]
	 */
	getAssignedVerificationList: function (req, res) {
		//console.log('am here');
		if (req.body.data) {
			let data = JSON.parse(req.body.data);
			let user_token = data.hasOwnProperty('user_token') ? data.user_token : '';
			if (user_token != '') {
				let query = "select * from users where lp_token = '" + user_token + "'";
				db.query(query, {}, function (err, userdet) {
					if (err) {
						res.send({ 'status': 'failed', 'message': 'Your Session Has Expired. Please Login Again Error_Code : 3001.', 'data': [] });

					} else {
						//console.log(userdet);
						if (userdet.length > 0) {
							let from_date = data.hasOwnProperty('from_time') ? data.from_time : '';
							if (from_date != '') {
								let to_date = data.hasOwnProperty('to_time') ? data.to_time : '';
								if (to_date != '') {
									picking.getAssignedVerificationList(from_date, to_date, userdet[0].user_id).then(data => {
										let res_message = '';
										if (data.length > 0) {
											res_message = 'Data Found';
										} else {
											res_message = 'No Data Found';
										}
										res.send({ status: 200, message: res_message, data: data });
									}, err => {
										res.send({ status: "failed", message: "No Data found", data: [] });
									});
								} else {
									res.send({ status: "failed", message: "Please send to time", data: [] });
								}
							} else {
								res.send({ status: "failed", message: "Please send from time", data: [] });
							}
						} else {
							res.send({ 'status': 'failed', 'message': 'Your Session Has Expired. Please Login Again Error_Code : 3001.', 'data': [] });
						}
					}
				})


			} else {
				res.send({ 'status': 'failed', 'message': 'Please provide authorized token Error_Code : 3002.', 'data': [] });
			}

		} else {
			res.send({ status: "failed", message: "Required parameters missing.", data: [] });
		}
	},
	/**
	 * [getPendingVerification To get pending verification orders list]
	 * @param  {[array]} req [from_date, to_date, checker_id]
	 * @param  {[object]} res [Pending orders list]
	 */
	getPendingVerification: function (req, res) {
		if (req.body.data) {
			let data = JSON.parse(req.body.data);
			let from_date = data.hasOwnProperty('from_date') ? data.from_date : '';
			if (from_date != '') {
				let to_date = data.hasOwnProperty('to_date') ? data.to_date : '';
				if (to_date != '') {
					let checker_id = data.hasOwnProperty('checker_id') ? data.checker_id : '';
					picking.getPendingVerificationList(from_date, to_date, checker_id).then(data => {
						let res_message = '';
						if (data.length > 0) {
							res_message = 'Data Found';
						} else {
							res_message = 'No Data Found';
						}
						res.send({ status: 200, message: res_message, data: data });
					}, err => {
						console.log(err);
						res.send({ status: "failed", message: "No Data found", data: [] });
					});
				} else {
					res.send({ status: "failed", message: "Please send to time", data: [] });
				}
			} else {
				res.send({ status: "failed", message: "Please send from time", data: [] });
			}

		} else {
			res.send({ status: "failed", message: "Required parameters missing.", data: [] });
		}
	},
	/**
	 * [getRtdOrdersData To get Orders list which are in Ready to dispatch status]
	 * @param  {[array]} req [user_token, from_time, to_time]
	 * @param  {[object]} res [RTD orders list]
	 */
	getRtdOrdersData: function (req, res) {
		if (req.body.data) {
			let data = JSON.parse(req.body.data);
			let user_token = data.hasOwnProperty('user_token') ? data.user_token : '';
			if (user_token != '') {
				let query = "select * from users where lp_token = '" + user_token + "' or password_token = '" + user_token + "'";
				db.query(query, {}, function (err, userdet) {
					if (err) {
						console.log(err);
						res.send({ 'status': 'failed', 'message': 'You are already logged into system!!', 'data': [] });

					} else {
						//console.log(userdet);
						if (userdet.length > 0) {
							let from_date = data.hasOwnProperty('from_time') ? data.from_time : '';
							if (from_date != '') {
								let to_date = data.hasOwnProperty('to_time') ? data.to_time : '';
								if (to_date != '') {
									picking.getRtdOrdersList(from_date, to_date, userdet[0].user_id).then(data => {
										let res_message = '';
										if (data.length > 0) {
											res_message = 'Data Found';
										} else {
											res_message = 'No Data Found';
										}
										res.send({ status: 200, message: res_message, data: data });
									}, err => {
										res.send({ status: "failed", message: "No Data found", data: [] });
									});
								} else {
									res.send({ status: "failed", message: "Please send to time", data: [] });
								}
							} else {
								res.send({ status: "failed", message: "Please send from time", data: [] });
							}
						} else {
							res.send({ 'status': 'failed', 'message': 'Your Session Has Expired. Please Login Again Error_Code : 3001.', 'data': [] });
						}
					}
				})


			} else {
				res.send({ 'status': 'failed', 'message': 'Please provide authorized token Error_Code : 3002.', 'data': [] });
			}

		} else {
			res.send({ status: "failed", message: "Required parameters missing.", data: [] });
		}
	}
}