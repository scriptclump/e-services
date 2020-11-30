const mysql = require('mysql');


const pool = mysql.createPool({
    host: process.env.DATABASE_HOSTNAME,
    user: process.env.DATABASE_USERNAME,
    password: process.env.DATABASE_PASSWORD,
    database: process.env.DATABASE_NAME,
    connectionLimit : 50,
    multipleStatements : true
});

var DB = (function () {
    function _query(query, params, callback) {
        pool.getConnection(function (err, connection) {
            if (err) {
                console.log(err);
                connection.release();
                connection.destroy();
                callback(err, null);
                throw err;
            }
            connection.query(query, params, function (err, rows) {

               // console.log('*****************', query,err,rows);
                connection.release();
                connection.destroy();

                if (!err) {
                    callback(null, rows);
                }
                else {
                    callback(err, null);
                }
            });

            connection.on('error', function (err) {
                connection.release();
                connection.destroy();
                callback(err, null);
                throw err;
            });
        });
    };

    return {
        query: _query
    };
})();

module.exports = { DB: DB, pool: pool };