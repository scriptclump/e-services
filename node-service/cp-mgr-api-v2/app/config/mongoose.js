var mongooes = require('mongoose');

var db_url = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + "/" + process.env['MONGO_DATABASE'] + "?retryWrites=true&w=majority";
mongooes.connect(db_url, { useNewUrlParser: true, useUnifiedTopology: true, useCreateIndex: true });

// 0: disconnected// 1: connected// 2: connecting// 3: disconnecting
mongooes.connection.on('connected', function (response) {
         console.log("Mongoose is now Connected at: " + db_url);
});
mongooes.connection.on('disconnected', function (response) {
         if(mongooes.connection.readyState == 2 || mongooes.connection.readyState == 0){
          var db_url = 'mongodb://' + process.env['MONGO_USER_Slave'] + ":" + process.env['MONGO_PASSWORD_Slave'] + "@" + process.env['MONGO_HOST_Slave'] + "/" + process.env['MONGO_DATABASE_Slave'] + "?retryWrites=true&w=majority";
          mongooes.connect(db_url, { useNewUrlParser: true, useUnifiedTopology: true, useCreateIndex: true });   
     }
     console.log("Mongoose is now disconnected:");
});
mongooes.connection.on('Error', function (err) {
     console.log("Error while connecting: " + err);
});
function gracefulShutdown(msg, callback) {
     mongooes.connection.close(function () {
          console.log('Mongoose disconnected through ' + msg);
          callback();
     });
}

// BRING IN YOUR SCHEMAS & MODELS
require('../v2/model/users.model');
