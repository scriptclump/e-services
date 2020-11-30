/**
 * UserController
 *
 * @description :: Server-side logic for managing users
 * @help        :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
const User = require('../models/User');

module.exports = {
	index: function (req, res) {
		var a = 5;
		var b = 6;
		var c = 15;
		var x = c.toString();
		return res.json(200, { method : 'Get', message: 'Ebutor Apis', status: 200});
	},

	create: function(req, res){ 

		var params = req.params.all() ;

		User.create({name: params.name}).exec(
			function createCB(err,created){
		 		return res.json({ notice: 'Created user with name ' + created.name }); 
		}); 
	} 
};

