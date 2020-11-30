const banner = require('../models/bannerdetails');
const commonObj = require('../models/commonmodel');

module.exports = {

	impression: function (req, res) {

		var status = "success";
		var result = [];
		var message = "Response recorded!";
		var body = req.body;
		var data = JSON.parse(body.data);
		var customer_token = data.customer_token;
		var mapping_id = data.mapping_id;
		var type = data.type;
		var ic = data.ic;
		var response = { status: status, message: message, data: result };
		var user_id_db = 0;
		var action_type = ic;
		var converted_to = 0;
		var le_wh_id = data.le_wh_id.split(",", 2);
		var hub_id = data.hub_id.split(",", 2);
		var beat_id = data.beat_id;
		console.log("impression", le_wh_id[0], hub_id[0]);
		commonObj.getUserDataByToken(customer_token).then((result) => {
			// getting user id
			return result;
		}).then((user_data) => {

			user_id_db = user_data.user_id;
			if (beat_id == "" || beat_id == null)
				beat_id = 0;
			//checking whether it is in history table
			// 16601 - Banner,16602 - Popup - Banner Table
			// 16603 - Sponsor - Sponsor Table
			// Impression - 16801 ,Click - 16802, Sale - 16803

			if (action_type == undefined)
				action_type = 16801;

			// action_type = 1 - click ,0 - impression
			if (action_type == 1) {
				action_type = 16802;
				converted_to = 1;
			} else {
				action_type = 16801;
				converted_to = 0;
			}
			var req = { config_mapping_id: mapping_id, user_id: user_id_db, action_type: action_type, type: type };
			return banner.getSponsoredDetails(req);
		}).then((details) => {

			if (details != undefined && (Object.keys(details).length != undefined) && (Object.keys(details).length > 0)) {

				if (ic == 0 && details.click_conversion == 0) {
					//do nothing because data is already present 
					return 0;
				} else if (ic == 1 && details.click_conversion == 0) {
					// request is for click,but only impression is present,then update table to click and return

					banner.updateSponsoredDetails({ ic: ic, sponsor_history_id: details.sponsor_history_id });
					return 0;
				} else if (ic == 0 && details.click_conversion == 1) {
					// since database is click and request is impression,it means impression as wel as click already recorded,so do nothing
					response = { status: status, message: message, data: result }
					res.send(response);
					return 0;
				}
			} else {

				console.log(type);
				if (type == 16601 || type == 16602) {
					return banner.getBannerDetails(mapping_id);
				} else {
					return banner.getSponsorDetails(mapping_id);
				}
			}

		}).then((mapping_details) => {
			if (mapping_details != undefined && (Object.keys(mapping_details).length != undefined) && (Object.keys(mapping_details).length > 0)) {
				var date = new Date().toISOString().split('T')[0];
				var from = mapping_details.f_date;
				var to = mapping_details.t_date;
				if ((date >= from) && (date <= to)) {
					var cost = mapping_details.impression_cost;

					if (ic == 0)
						cost = mapping_details.impression_cost;
					else
						cost = mapping_details.click_cost;

					var config_mapping_id = 0;
					var display_type = 0;
					if (type == 16601 || type == 16602) {
						config_mapping_id = mapping_details.banner_id;
						display_type = mapping_details.display_type;
					} else {
						config_mapping_id = mapping_details.sponsor_id;
						display_type = 16603;
					}

					console.log(config_mapping_id);
					var impression = [
						[
							config_mapping_id,
							mapping_details.navigate_object_id,
							display_type,
							user_id_db,
							le_wh_id[0],
							hub_id[0],
							beat_id,
							action_type,
							cost,
							converted_to
						]
					];
					// if(ic == 1){
					// 	//click ,but no impression present 
					// 	impression.push([
					// 	config_mapping_id,
					// 	mapping_details.navigate_object_id,
					// 	display_type,
					// 	user_id_db,
					// 	10696,
					// 	10697,
					// 	555,
					// 	16801,
					// 	mapping_details.impression_cost,
					// 	0
					// 	]);
					// }

					return impression;
				} else {
					message = "Promotion expired or not yet started";
				}

			} else {
				return 0;
			}

		}).then((impression) => {
			if (impression != undefined && (Object.keys(impression).length != undefined) && (Object.keys(impression).length > 0)) {
				return banner.insertMappingDetails(impression);

			} else {
				return 0;
			}
		}).then((final_result) => {

			response = { status: status, message: message, data: result }
			res.send(response);

		}).catch(err => {
			console.log(err);
			response = { status: status, message: err, data: result }
			res.send(response);
		});
	}
}
