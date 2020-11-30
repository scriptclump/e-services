'user strict';

const mysql = require('mysql');
let con = null;

//used to created  new db connection
function getDBConnection() {
    con = mysql.createConnection({
        host: process.env.DATABASE_HOSTNAME,
        user: process.env.DATABASE_USERNAME,
        password: process.env.DATABASE_PASSWORD,
        database: process.env.DATABASE_NAME
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

