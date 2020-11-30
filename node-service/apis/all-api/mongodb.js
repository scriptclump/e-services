var MongoClient = require('mongodb').MongoClient
const config = require('./config/config.json');

var host = 'mongodb://'+config['mongo_host']+":"+config['mongo_port']+"/"+config['mongo_database']
MongoClient.connect(host, function (err, db) {
  if (err) throw err

  db.collection('mammals').find().toArray(function (err, result) {
    if (err) throw err

    console.log(result)
  })
})