const elastic = require("../elastic");
const sequelize =  require("../config/sequelize");
const data  = require(`./data.json`);



/**
 * @function getData used to get json record to store in elasti search
 * @returns  { json record }
 * @description Returns an ElasticSearch Action in order to
 *              correctly index documents.
 */

async function getData(){
  try{
    let query =  "CALL getProductsJson()";
    sequelize.query(query,{ type: sequelize.QueryTypes.SELECT }).then(rows=>{
      let result = JSON.parse(JSON.stringify(rows));
      return result;
    }).catch(err=>{
      console.log(err);
    })
  }catch(err){
    console.log(err);
  }
  
}

/**
 * @function createESAction
 * @returns {{index: { _index: string, _type: string }}}
 * @description Returns an ElasticSearch Action in order to
 *              correctly index documents.
 */

const esAction = {
  index: {
    _index: elastic.index,
    _type: elastic.type
  }
};

/**
 * @function pupulateDatabase
 * @returns {void}
 */

async function populateDatabase() {

  const docs = [];
  for (const quote of data) {
    docs.push(esAction);
    docs.push(quote);
  }

  return elastic.esclient.bulk({ body: docs });
}

module.exports = {
  populateDatabase
};