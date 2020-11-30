var express = require('express');

var sequelize = require('../../config/sequelize');


const verifyToken = (req, res) => {
	console.log(req.body.data);
	if (req.body.data) {
		next();
	} else {
		res.json({
			status: 500,
			message: 'Bad request',
			data: []
		})
	}
};

module.exports = verifyToken;