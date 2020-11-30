"use strict";

require('dotenv').config();
require('./config/mongoose');//for mongodb connection
var chalk = require('chalk');
var figlet = require('figlet');
var express = require('express');
const logger = require('morgan');
var env = process.env.NODE_ENV = process.env.NODE_ENV || 'development';
var app = express();
//var config = require('./config/config')[env];
var cluster = require('cluster');
if (cluster.isMaster) {
     var numWorkers = require('os').cpus().length;
     console.log('Master cluster setting up ' + numWorkers + ' workers...');

     for (var i = 0; i < numWorkers; i++) {
          cluster.fork();//will create new worker
     }

     cluster.on('exit', function (worker, code, signal) {
          console.log('Worker ' + worker.process.pid + ' died with code: ' + code + ', and signal: ' + signal);
          console.log("Death was suicide:", worker.exitedAfterDisconnect);
          console.log('Starting a new worker');
          cluster.fork();//will
     });
} else {

     require('./config/express')(app);
     require('./config/api')(app);
     require('./config/routes')(app);
     require('./config/mysql');
     app.use(logger('dev'));

     // Starting the server

     var port = process.env.PORT || 1204;
     var server = app.listen(port);
     //console.log(chalk.blue.bold(figlet.textSync('Ebutor v2.0')));
     console.log(chalk.green.bold(`Server Started at port: ${port}`));
}




