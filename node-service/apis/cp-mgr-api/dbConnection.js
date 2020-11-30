const config = require('./config/config.json');
const mysql = require('mysql');

let con = null;

//used to created  new db connection
function getDBConnection() {
    con = mysql.createConnection({
        host: config['host'],
        user: config['username'],
        password: config['password'],
        database: config['database'],
    });
    return con;
}
//used to connect with db.
function connectToDB(connection) {
    connection.connect(function (err) {
        if (err) {
            throw err;
        }
    });
}
//used to end the connection after every execution of query.
function endDBConnection(connection) {
    connection.end(function (err) {
        if (err) {
            throw err;
        }
    });
}


var DB = (function () {
    function _query(query, params, cb) {
        var connection = getDBConnection();
        connectToDB(connection);
        connection.query(query, params, function (err, res) {
            if (err) {
                cb(err);
            }
            cb(null, res);
        });
        endDBConnection(connection);
    };

    return {
        query: _query
    };
})();

module.exports = { DB: DB, Conn: getDBConnection() };