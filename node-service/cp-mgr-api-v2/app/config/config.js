var path = require('path');
//var rootPath = path.normalize(__dirname + '/../../');
module.exports = {
    dev: {
        db: 'mongodb://127.0.0.1/IT-help-desk',
        // rootPath: rootPath,
        port: process.env.PORT || 1203
    },
    qc: {
        // rootPath: rootPath,
        db: 'mongodb://127.0.0.1/IT-help-desk',
        port: process.env.PORT || 5002
    },

    prelive: {
        // rootPath: rootPath,
        db: 'mongodb://127.0.0.1/IT-help-desk',
        port: process.env.PORT || 5003
    },

    production: {
        //rootPath: rootPath,
        db: 'mongodb://127.0.0.1/IT-help-desk',
        port: process.env.PORT || 5005
    }
}
