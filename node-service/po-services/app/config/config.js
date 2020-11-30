var path = require('path');
var rootPath = path.normalize(__dirname + '/../../');
module.exports = {
    development: {
        db: 'mongodb://127.0.0.1/database',
        rootPath: rootPath,
        port: process.env.PORT || 1201
    },
    production: {
        rootPath: rootPath,
        db: 'mongodb://127.0.0.1/database',
        port: process.env.PORT || 1202
    }
}
