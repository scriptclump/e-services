var MongoClient = require('mongodb').MongoClient;

/*
 * This file will provides you mongodb connection instance
 */
function mongoConnection() {
     return new Promise((resolve, reject) => {
          try {
               var host = 'mongodb://' + process.env['MONGO_USER'] + ":" + process.env['MONGO_PASSWORD'] + "@" + process.env['MONGO_HOST'] + ":" + process.env['MONGO_PORT'] + "/" + process.env['MONGO_DATABASE'];
               MongoClient.connect(host, { useNewUrlParser: true, useUnifiedTopology: true }, async function (err, db) {
                    if (err) {
                         console.log(err);
                         reject(err);
                    } else {
                         resolve(db);
                    }
               });
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}

mongoConnection().then(instance => {
     let mongoInstance = instance
     module.exports = mongoInstance;
}).catch(err => {
     console.log(err);
})
