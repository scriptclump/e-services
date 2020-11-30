
const mongoose = require('mongoose');
const moment = require('moment');

/* User Specific Schema */
/* ------ Starts ------ */
var usersSchema = new mongoose.Schema({
     mobile: {
          type: Number,
     },
     user_id: Number,
     password_token: String,
     lp_token: String,
     otp: Number,
     lp_otp: Number,
     is_disabled: Number,
     is_active: Number,
     legal_entity_id: Number,
     createdOn: {
          type: Date,
          default: moment().format("YYYY-MM-DD HH:mm:ss")
     },
     createdBy: Number,
     updatedOn: {
          type: Date,
          default: moment().format("YYYY-MM-DD HH:mm:ss")
     },
     updatedBy: Number,

});

/*------- User temprory table ---------*/
var userTempSchema = new mongoose.Schema({
     mobile: {
          type: Number,
     },
     user_temp_id: {
          type: mongoose.Schema.Types.ObjectId
     },
     otp: Number,
     legal_entity_type_id: Number,
     status: Boolean,
     createdOn: {
          type: Date,
          default: moment().format("YYYY-MM-DD HH:mm:ss")
     },
     updatedOn: {
          type: Date,
          default: moment().format("YYYY-MM-DD HH:mm:ss")
     },

});




mongoose.model('User', usersSchema, 'user');
mongoose.model('userTemp', userTempSchema, 'userTemp')

module.exports = {
     User: usersSchema,
     userTemp: userTempSchema
}
