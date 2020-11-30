const model = require("../models");

/**
 * @function getDetails used to get product details based on search
 * @param {Object} req Express request object
 * @param {Object} res Express response object
 * @returns {void}
 */

async function getDetails(req, res) {
  try {
      const query  = JSON.parse(req.body.data);
      if (!query) {
        res.status(422).json({status:'failed',data: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7002"});
        return;
      }
      //fetching details based on search keyword
      const result = await model.getDetails(query);
      res.status(200).json({ status:'success', data: result });
  } catch (err) {
    console.log(err);
    res.status(500).json({ status:'failed', 'message': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000."});
  }
}

 /**
 * @function getDetails used to get product details based on search
 * @param {Object} req Express request object
 * @param {Object} res Express response object
 * @returns {void}
 */

async function getSuggestion(req, res) {
  try {
      const query  = JSON.parse(req.body.data);
      if (!query) {
        res.status(422).json({status:'failed',data: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7002"});
        return;
      }
      //fetching details based on search keyword
      const result = await model.getSuggestions(query);
      res.status(200).json({ status:'success', data: result });
  } catch (err) {
    console.log(err);
    res.status(500).json({ status:'failed', 'message': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000."});
  }
}

/**
 * @function addElement used to add new element in existing index
 * @param {Object} req Express request object
 * @param {Object} res Express response object
 * @returns {void}
 */

async function addElement(req, res) {
  try {
    const query  = JSON.parse(req.body.data);
    if (!query) {
      res.status(422).json({status:'failed',data: "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7002"});
      return;
    }
    //used to add element in existing index
    const result = await model.insertNew(req.body.data);
    res.status(200).json({ status:'success', data: result });
  } catch (err) {
    console.log(err);
    res.status(500).json({ status:'failed', 'message': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000."});
  }

}

module.exports = {
  getDetails,
  getSuggestion,
  addElement
};