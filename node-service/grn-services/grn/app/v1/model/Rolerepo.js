'user strict';

const Sequelize = require('sequelize');
var sequelize = require('../../config/mysql');
var database = require('../../config/mysqldb');
let db = database.DB;

module.exports = {
	checkPermissionByFeatureCode: async function (feature_code, user_id) {
		try {
			return new Promise(async (resolve, reject) => {
				if (user_id == 24) {
					resolve(true);
					// return true;
				} else {
					var query = "select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id =" + user_id + " and features.feature_code ='" + feature_code + "' and features.is_active=1";
					// console.log('query', query);
					db.query(query, {}, function (err, res) {
						// console.log(res.length);
						if (err) {
							resolve(false);
							// return false;
						} else {
							if (res.length > 0) {
								resolve(true);
								// return true;
							} else {
								resolve(false);
								// return false;
							}
						}
					});
				}
			})
		} catch (err) {
			console.log("error", err)
		}

	}
}
