var path = require('path');
var fs = require('fs');
var connection = null;

try {
    fs.exists(path.join(appPath, 'application/config', 'mysqlconnect.js'), function (exists) {
        if (exists) {
            var config = require(path.join(appPath, 'application/config', 'mysqlconnect.js'));
            try {
                global.mysql = require('mysql');
            } catch (e) {
                console.log('Please install "mysql" module. run "npm install mysql"');
                process.exit();
            }

            // -------start of new dbconnection configuration ------------------//
            function getDBConnection() {
                connection = mysql.createConnection({
                    host: config.mysqlHost,
                    user: config.mysqlUser,
                    password: config.mysqlPass,
                    database: config.mysqldbName
                });
                return connection;
            }
            //used to connect with db.
            function connectToDB(connections) {
                connections.connect(function (err) {
                    if (err) {
                        throw err;
                    } else {
                        //  console.log("New connection opened")
                    }
                });
            }
            //used to end the connection after every execution of query.
            function endDBConnection(connections) {
                connections.end(function (err) {
                    if (err) {
                        throw err;
                    } else {
                        // console.log("connection closed")
                    }
                });
            }

            //provide db query instance
            global.con = (function () {
                function _query(query, params, cb) {
                    var connections = getDBConnection();
                    connectToDB(connections);
                    connections.query(query, params, function (err, res) {
                        if (err) {
                            cb(err);
                        }

                        cb(null, res);
                    });
                    endDBConnection(connections);
                };

                return {
                    query: _query
                };
            })();
            //-----------------End of new COnnection config -------------------------//
            // global.con = mysql.createConnection({
            //     host: config.mysqlHost,
            //     user: config.mysqlUser,
            //     password: config.mysqlPass,
            //     database: config.mysqldbName

            // });

            // con.connect(function (error) {
            //     if (error) {
            //         console.log(error);
            //         return;
            //     }
            // });
        }
    });
}
catch (err) {
    console.log(err);
}