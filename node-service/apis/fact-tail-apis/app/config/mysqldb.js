const mysql = require('mysql');


const pool = mysql.createPool({
    host: '68.183.87.183',
    user: 'fbedevapp',
    password: 'FbED@v2pP!$',
    database: 'qcebutor',
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