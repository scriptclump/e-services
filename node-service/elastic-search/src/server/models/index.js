const { esclient, index, type } = require("../../elastic");

//Used to get productDetails based on search query.
async function getDetails(req) {
  let query= {
   query:{
        bool: {
            should :{
              match: {
                //name of the field based on which we want to perform search.
                NAME: {
                  query: req.keyword,
                  operator: "and",
                  minimum_should_match:3,
                  fuzziness: 2,
                }
              }
            }
          }
      },suggest: {
      gotsuggest: {
        text: req.keyword,
        term: { field: 'NAME' }
      }
    }
  }


  //Performing search based on condition
  const { body: { hits } } = await esclient.search({
    from:  req.page  || 0,
    size:  req.limit || 100,
    index: index, 
    type:  type,
    body:  query
  });
 
  const count = hits.total.value;
  const data  = hits.hits.map((hit) => {
        return {
        //List of fields want to return
        id:     hit._id,
        Name:  hit._source.NAME,
        product_id: hit._source.product_id,
        category_id :hit._source.category_id,
        brand_id :hit._source.brand_id,
        manufacturer_id :hit._source.manufacturer_id,
        parent_id :hit._source.parent_id,
        score:  hit._score
      }
       
  });

  return {
    count,
    data
  }

}

async function insertNew(query) {
  return esclient.index({
    index,
    type,
    body: {
     query
    }
  })
};




module.exports = {
  getDetails,
  insertNew
}