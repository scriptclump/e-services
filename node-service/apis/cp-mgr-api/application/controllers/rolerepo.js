const models = require('../tables/index');
const dbconnection = require('../../dbConnection');
const db = dbconnection.DB;

module.exports = {
	/**
	 * [checkPermissionByFeatureCode To check whether user has access to that feature]
	 * @param  {[string]} feature_code [feature code]
	 * @param  {[int]} user_id      [user id]
	 * @return {[boolean]}              [true if user has access/ false if he doesn't have]
	 */
	checkPermissionByFeatureCode: function (feature_code, user_id = null) {
		return new Promise((resolve, reject) => {
			if (user_id == 1) {
				resolve(true);
			} else {
				var query = "select features.name from role_access join features on role_access.feature_id=features.feature_id join user_roles on role_access.role_id=user_roles.role_id where user_roles.user_id =" + user_id + " and features.feature_code ='" + feature_code + "' and features.is_active=1";
				db.query(query, {}, function (err, res) {
					if (err) {
						console.log(err);
						resolve(false);
					} else {
						if (res.length > 0) {
							resolve(true);
						} else {
							resolve(false);
						}
					}
				});
			}
		});
	}
};