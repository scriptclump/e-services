const sequelize = require('../../../config/sequelize');




/*
* Function name: getWarehouseid
* Description: Used to get warehouse_id
*/
module.exports.getWarehouseid = function (pincode, getLegalEntityID = false) {
     let response = {};
     return new Promise((resolve, reject) => {
          let query = "select ws.le_wh_id as le_wh_id , ws.legal_entity_id from wh_serviceables as ws JOIN legalentity_warehouses as lew ON ws.le_wh_id= lew.le_wh_id where ws.pincode =" + pincode + "&&lew.dc_type=118001 && lew.status=1";
          sequelize.query(query).then(leWh => {
               let le_wh = JSON.parse(JSON.stringify(leWh[0]));
               if (le_wh[0] != null) {
                    // If the API wants legal Entity Id, then its set to 1(true)
                    if (getLegalEntityID) {
                         response = { le_wh_id: le_wh[0].le_wh_id, legal_entity_id: le_wh[0].legal_entity_id }
                         resolve(response);
                    } else {
                         // If its only warehouse Id
                         resolve(le_wh[0].le_wh_id);
                    }
               } else {
                    resolve('');
               }
          }).catch(err => {
               console.log(err);
               reject(err);
          })
     })

}