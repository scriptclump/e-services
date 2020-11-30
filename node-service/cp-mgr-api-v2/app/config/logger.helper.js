"use strict";
const winston = require('winston');/* NPM Plugin For Logger */


/* Exporting as a Module, because we do not need names functions
        1=> require logger.js in your controller
        2=> list of log level 
            error, 
            warn, 
            info, 
            verbose, 
            debug, 
            silly  
            example: logger.error(err);        
            note: Maintain the proper levels to store the logs.

 */
module.exports = new (winston.Logger)({
     //for console log
     transports: [
          new (winston.transports.Console)({
               level: 'warn'
          }),
          new winston.transports.File({ filename: 'orderlog.log', level: 'info' }),//used to log info level console into logger file
     ]

});

