'use strict';

const database = require('../../../config/mysqldb');
const db = database.DB;

module.exports.saveBannerScreen = async (img, text, order = "", userId, userName) => {
    return new Promise(async (resolve, reject) => {

        // while creating a new banner screen, taking the status as 1 (i.e., active)
        if (order != "") {

            //query to add new banner at custom position
            let query = `SELECT MAX(sort_order) as value FROM intro_screens;`
            db.query(query, {}, (err, data) => {
                if (err) {
                    console.log("query Error :", err);
                    reject("Error in getting the previous count of Banners");
                }
                let orderPosition = order < data[0].value ? order : data[0].value + 1;

                // console.log('orderPosition', orderPosition);
                //overwriting query
                query = `UPDATE intro_screens 
            SET sort_order = (CASE WHEN sort_order >= ${orderPosition} THEN sort_order + 1 ELSE sort_order END)
            WHERE sort_order >=  ${orderPosition};
            INSERT INTO intro_screens (banner_image,banner_text,sort_order,STATUS,created_by) VALUES("${img}"," ${text}"," ${orderPosition}","1","${userName}");`

                // console.log('query', query);
                db.query(query, {}, (err, data) => {
                    if (err) {
                        console.log("query Error :", err);
                        reject("Banner Creation Unsuccessful");
                    }
                    // console.log("data", data);
                    resolve(true);
                })
            });
        } else {
            //query to add new banner at the bottom order.
            let query1 = `SELECT MAX(sort_order) as value FROM intro_screens;`
            db.query(query1, {}, (err, data) => {
                if (err) {
                    console.log("query Error :", err);
                    reject("Error in getting the previous count of Banners");
                }

                let response = data[0].value + 1;

                //overwriting query1
                query1 = `INSERT INTO intro_screens (banner_image,banner_text,sort_order,STATUS,created_by) VALUES("${img}","${text}","${response}","1","${userName}");`
                db.query(query1, {}, (err, data) => {
                    if (err) {
                        console.log("query1 Error :", err);
                        reject("Banner Creation Unsuccessful");
                    }
                    // console.log('data',data);
                    resolve(true);
                });

            })
        }
    })
};

module.exports.getBannerList = async (getBanners = 1) => {
    return new Promise((resolve, reject) => {
        if (getBanners) {
            //query showing only active banners

            let query = `SELECT is_id,banner_image,banner_text,sort_order,status FROM intro_screens WHERE STATUS != 0 ORDER BY sort_order;`
            db.query(query, (err, data) => {
                if (err) {
                    console.log("error while getting active banners :", err);
                    reject("Error getting active Banners List");
                }
                resolve(data);
            })
        } else {
            //query showing all banners
            let query = `SELECT is_id,banner_image,banner_text,sort_order,status FROM intro_screens ORDER BY sort_order;`
            db.query(query, (err, data) => {
                if (err) {
                    console.log("error while getting active banners :", err);
                    reject("Error getting active Banners List");
                }
                resolve(data);
            })
        }
    })
};

module.exports.getAllBannerList = async () => {
    return new Promise((resolve, reject) => {
        // if (getBanners) {
            //query showing only active banners

            let query = `SELECT banner_image,banner_text FROM intro_screens ORDER BY sort_order;`
            db.query(query, (err, data) => {
                if (err) {
                    console.log("error while getting active banners :", err);
                    reject("Error getting active Banners List");
                }
                resolve(data);
            })
        // } 
        // else {
        //     //query showing all banners
        //     let query = `SELECT is_id,banner_image,banner_text,sort_order,status FROM intro_screens ORDER BY sort_order;`
        //     db.query(query, (err, data) => {
        //         if (err) {
        //             console.log("error while getting active banners :", err);
        //             reject("Error getting active Banners List");
        //         }
        //         resolve(data);
        //     })
        // }
    })
};

module.exports.updateBanner = async (isId, url) => {
    return new Promise((resolve, reject) => {
        let query = `UPDATE intro_screens SET banner_image = "${url}" WHERE is_id = ${isId};`
        db.query(query, (err, data) => {
            if (err) {
                console.log("error while updating image :", err);
                reject("Image update unsuccessful");
            } else if (data.affectedRows == 0) {
                reject("No such banner exists");
            }
            resolve(true);
        })
    })
};

module.exports.updateDetails = async (data) => {
    return new Promise(async (resolve, reject) => {
        let isId = data.is_id;

        if (data.hasOwnProperty('sort_order') || data.hasOwnProperty('status')) {
            // get the current order position (i.e.,sort_order) of the banner
            let currentOrderPosition = await new Promise((resolve, reject) => {
                let querySort = `SELECT sort_order from intro_screens WHERE is_id = ${isId};`
                db.query(querySort, (err, response) => {
                    if (err) {
                        console.log("error in getting currentOrderPosition :", err);
                        resolve(0);
                    }
                    // console.log("response", response);
                    resolve(response[0].sort_order);
                })
            });

            //finding the maximum sort_order in the db
            let maxSortOrderPositionInDB = await new Promise((resolve, reject) => {
                //query to add new banner at custom position
                let query = `SELECT MAX(sort_order) as value FROM intro_screens;`
                db.query(query, {}, (err, data) => {
                    if (err) {
                        console.log("query Error :", err);
                        reject("Error in getting the previous count of Banners");
                    }
                    // let orderPosition = order < data[0].value ? order : data[0].value + 1;
                    // console.log("data",data);
                    let result = data[0].value;
                    resolve(result);
                });
            });


            // new Banner can be inserted either in between other banners or at the end
            let newOrderPosition;
            if (data.hasOwnProperty('sort_order')) {
                newOrderPosition = data.sort_order < maxSortOrderPositionInDB ? data.sort_order : maxSortOrderPositionInDB;
            } else {
                newOrderPosition = maxSortOrderPositionInDB;
            }

            // console.log("current,new ", currentOrderPosition, newOrderPosition);

            /**
             * IF CONDITION TO DISABLE THE BANNER
             * -------------------------------------------------------------
             * if status shall become 0, then sort_order value becomes NULL 
             * and the remaining sort_order column re-arrange in serial order
             * -------------------------------------------------------------
             */
            if (data.hasOwnProperty('status') && data.status == 0) {
                if (currentOrderPosition != null) {
                    let query = `UPDATE intro_screens SET sort_order = NULL, status = 0 WHERE is_id = ${isId};
            UPDATE intro_screens SET sort_order = (CASE WHEN sort_order > ${currentOrderPosition} THEN sort_order - 1 ELSE sort_order END)
                WHERE sort_order >= ${currentOrderPosition};`;

                    db.query(query, (err, response) => {
                        if (err) {
                            console.log("error in updating status to 0 :", err);
                            reject("Error while changing status to 0");
                        }
                        resolve(true);
                    })
                } else {
                    reject("The Banner is already Disabled")
                }
            }

            //ELSE CONDITION TO UPDATE BANNER DETAILS INCLUDING ENABLE THE BANNER
            else {

                if (currentOrderPosition == 0) reject("No Banner Found with the given input");

                else if (currentOrderPosition == null) {

                    // query1 to create a space in sort_order to enter the new order position
                    let query1 = `UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order >= ${newOrderPosition} THEN sort_order + 1  ELSE sort_order END)
                WHERE sort_order >= ${newOrderPosition};`

                    //query2 to enter the new sort order in sort_order column
                    let query2;
                    if (data.hasOwnProperty('banner_text')) {
                        query2 = `Update intro_screens SET banner_text = "${data.banner_text}",sort_order = ${newOrderPosition},status = 1 WHERE is_id = ${isId};`
                    }
                    else {
                        query2 = `Update intro_screens SET sort_order = ${newOrderPosition},status = 1 WHERE is_id = ${isId};`
                    }
                    db.query(query1 + query2, (err, data) => {
                        if (err) {
                            console.log("Error when current is null :", err);
                            reject("Error while updating details.")
                        }
                        resolve(true);
                    })
                }

                /**
                 * Conditions to sort differ when currentOrderPosition < newOrderPostion & viceversa. Thus, code written to fulfill following conditions
                 */
                else if (currentOrderPosition > newOrderPosition) {

                    // query1 to create a space in sort_order to enter the new order position
                    let query1 = `UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order >= ${newOrderPosition} THEN sort_order + 1  ELSE sort_order END)
                WHERE sort_order >= ${newOrderPosition} ;`

                    //query2 to enter the new sort order in sort_order column
                    let query2;
                    if (data.hasOwnProperty('banner_text')) {
                        query2 = `Update intro_screens SET banner_text = "${data.banner_text}",sort_order = ${newOrderPosition} WHERE sort_order = ${currentOrderPosition + 1} AND is_id = ${isId};`
                    }
                    else {
                        query2 = `Update intro_screens SET sort_order = ${newOrderPosition} WHERE sort_order = ${currentOrderPosition + 1} AND is_id = ${isId};`
                    }

                    // query3 to re-arrange the sort_order in serial number series
                    let query3 = `UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order > ${currentOrderPosition} THEN sort_order - 1 ELSE sort_order END)
                WHERE sort_order >= "${currentOrderPosition}";`

                    db.query(query1 + query2 + query3, (err, data) => {
                        if (err) {
                            console.log("Error when current > new position :", err);
                            reject("Error while updating details.")
                        }
                        resolve(true);
                    })
                }

                else if (currentOrderPosition < newOrderPosition) {

                    // query1 to create a space in sort_order to enter the new order position
                    let query1 = `UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order > ${newOrderPosition} THEN sort_order + 1 ELSE sort_order END)
                WHERE sort_order >= ${newOrderPosition};`;

                    //query2 to enter the new sort order in sort_order column
                    let query2;
                    if (data.hasOwnProperty('banner_text')) {
                        query2 = `UPDATE intro_screens SET banner_text = "${data.banner_text}", sort_order = ${newOrderPosition + 1} WHERE sort_order = ${currentOrderPosition} AND is_id = ${isId};`
                    } else {
                        query2 = `UPDATE intro_screens SET sort_order = ${newOrderPosition + 1} WHERE sort_order = ${currentOrderPosition} AND is_id = ${isId};`
                    };

                    // query3 to re-arrange the sort_order in serial number series
                    let query3 = `UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order >= ${currentOrderPosition} THEN sort_order - 1 ELSE sort_order END)
                WHERE sort_order >= ${currentOrderPosition};`;

                    db.query(query1 + query2 + query3, (err, data) => {
                        if (err) {
                            console.log("Error when current < new position :", err);
                            reject("Error while updating details.")
                        }
                        resolve(true);
                    })
                } else {
                    // this else block is the case where currentOrderPosition = newOrderPosition
                    let query = `UPDATE intro_screens SET banner_text ="${data.banner_text}" WHERE is_id = ${isId};`
                    db.query(query, {}, (err, data) => {
                        if (err) {
                            console.log("query Error :", err);
                            reject("Error in updating Banner Text");
                        } else if (data.affectedRows == 0) {
                            reject("No such Banner Found")
                        } else resolve(true);
                    });
                }
            }
        }
        // in the else condition assuming that only banner_text needs to be updated but not sort_order;
        else {
            let query = `UPDATE intro_screens SET banner_text ="${data.banner_text}" WHERE is_id = ${isId};`
            db.query(query, {}, (err, data) => {
                if (err) {
                    console.log("query Error :", err);
                    reject("Error in updating Banner Text");
                } else if (data.affectedRows == 0) {
                    reject("No such Banner Found")
                } else resolve(true);
            });
        }
    })
};


module.exports.deleteBannerFromDB = async (isId) => {
    return new Promise(async (resolve, reject) => {
        // get the current order position (i.e.,sort_order) of the banner
        let currentOrderPosition = await new Promise((resolve, reject) => {
            let querySort = `SELECT sort_order from intro_screens WHERE is_id = ${isId};`
            db.query(querySort, (err, response) => {
                if (err) {
                    console.log("error in getting currentOrderPosition :", err);
                    // reject(`Error while deleting Banner with is_id ${isId}`);
                    resolve('error');
                } else if (response.length == 0) {
                    console.log("this not printing");
                    resolve('undefined');
                } else {
                    // console.log("response", response.length);
                    resolve(response[0].sort_order);
                }
            })
        });
        // console.log("currentOrderPosition", Number.isInteger(currentOrderPosition));
        if (currentOrderPosition == null) {
            let query = `DELETE FROM intro_screens WHERE is_id = ${isId};`
            db.query(query, (err, response) => {
                if (err) {
                    console.log("error in getting currentOrderPosition :", err);
                    reject(`Error while deleting Banner with is_id ${isId}`);
                } else if (response.affectedRows == 0) {
                    reject(`No Banner exists with is_id ${isId}`);
                } else {
                    resolve(true);
                }
            })
        } else if (Number.isInteger(currentOrderPosition)) {
            let query = `DELETE FROM intro_screens WHERE is_id = ${isId};
                UPDATE intro_screens
                SET sort_order = (CASE WHEN sort_order >= ${currentOrderPosition} THEN sort_order - 1 ELSE sort_order END)
                WHERE sort_order >= ${currentOrderPosition};`
                db.query(query, (err, response) => {
                    if (err) {
                        console.log("error while deleting Banner :", err);
                        reject(`Error while deleting Banner with is_id ${isId}`);
                    } else if (response[0].affectedRows == 0) {
                        reject(`No Banner exists with is_id ${isId}`);
                    } else {
                        resolve(true);
                    }
                })
        }
        else if (currentOrderPosition == 'undefined') resolve(false);
        else if (currentOrderPosition == 'error') reject(`Error while deleting Banner with is_id ${isId}`);
    })
};



