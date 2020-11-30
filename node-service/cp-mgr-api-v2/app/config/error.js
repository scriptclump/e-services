module.exports = {
     /**Error code related to Catch.*/
     EC_7001: {
          MESSAGE: "SomeThing went wrong(Internal catch block which captures error returned by promises.)",
          HTTPCODE: 7001
     },
     EC_7002: {
          MESSAGE: "Required parameter not send (Any request parameters are missing).",
          HTTPCODE: 7002
     },
     EC_7003: {
          MESSAGE: "Unable to process your request.Plz contact support on - 04066006442.(UnsuccessFull operation).",
          HTTPCODE: 7003
     },
     EC_7004: {
          MESSAGE: "EmptyHubId.",
          HTTPCODE: 7004
     },
     EC_7005: {
          MESSAGE: "EmptyLeWhId.",
          HTTPCODE: 7005
     },
     EC_7006: {
          MESSAGE: "EmptySegmentId.",
          HTTPCODE: 7006
     },
     EC_7007: {
          MESSAGE: "EmptyLegalEntityId.",
          HTTPCODE: 7007
     },
     EC_7008: {
          MESSAGE: "EmptySalesToken.",
          HTTPCODE: 7008
     },
     EC_7009: {
          MESSAGE: "EmptyFeedbackId.",
          HTTPCODE: 7009
     },
     EC_7010: {
          MESSAGE: "Emptyfeedback_groupid.",
          HTTPCODE: 7010
     },
     EC_7011: {
          MESSAGE: "Something wrong in request paramater(InvalidFlag).",
          HTTPCODE: 7011
     },
     EC_7012: {
          MESSAGE: "Invalid city parameter.",
          HTTPCODE: 7012
     },
     EC_7013: {
          MESSAGE: "Invalid state parameter.",
          HTTPCODE: 7013
     },
     EC_7014: {
          MESSAGE: "Invalid country parameter.",
          HTTPCODE: 7014
     },
     EC_7015: {
          MESSAGE: "Invalid stateType parameter.",
          HTTPCODE: 7015
     },
     EC_7016: {
          MESSAGE: "Invalid le_wh_id parameter.",
          HTTPCODE: 7016
     },
     EC_7015: {
          MESSAGE: "Invalid countryType parameter.",
          HTTPCODE: 7015
     },
     EC_7016: {
          MESSAGE: "Invalid countryType parameter.",
          HTTPCODE: 7016
     },
     EC_7017: {
          MESSAGE: "Invalid countryType parameter.",
          HTTPCODE: 7017
     },
     EC_7018: {
          MESSAGE: "Invalid segmentType parameter.",
          HTTPCODE: 7018
     },
     EC_7019: {
          MESSAGE: "InvalidBrandId  parameter.",
          HTTPCODE: 7019
     },
     EC_7020: {
          MESSAGE: "Invalidoffset parameter.",
          HTTPCODE: 7020
     },
     EC_7021: {
          MESSAGE: "InvalidoffsetLImit parameter.",
          HTTPCODE: 7021
     },
     EC_7022: {
          MESSAGE: "InvalidCategoryId parameter.",
          HTTPCODE: 7022
     },
     EC_7023: {
          MESSAGE: "InvalidProductId parameter.",
          HTTPCODE: 7023
     },
     /** Error code related to products */
     EC_6001: {
          MESSAGE: "One/More products does not have sufficient stock(Low Invemtory).",
          HTTPCODE: 6001
     },
     EC_6002: {
          MESSAGE: "Invalid product details(Wrong product details sent to the api).",
          HTTPCODE: 6002
     },
     EC_6003: {
          MESSAGE: "You have already rated to this product(Dublicate entry in rating table).",
          HTTPCODE: 6003
     },
     EC_6004: {
          MESSAGE: "No products available(empty response from  get product api).",
          HTTPCODE: 6004
     },
     Ec_6005: {
          MESSAGE: "No products available(empty response from  get product api).",
          HTTPCODE: 6005
     },

     /** Error code related to Invalid paramater send  to the api.*/
     EC_1001: {
          MESSAGE: "Please enter valid (key name).",
          HTTPCODE: 1001
     },
     EC_1003: {
          MESSAGE: "Please enter valid (first name).",
          HTTPCODE: 1003
     },
     EC_1004: {
          MESSAGE: "Please enter valid (last name).",
          HTTPCODE: 1004
     },
     EC_1005: {
          MESSAGE: "Please enter valid (email).",
          HTTPCODE: 1005
     },
     EC_1006: {
          MESSAGE: "Please enter valid ( pincode).",
          HTTPCODE: 1006
     },
     EC_1007: {
          MESSAGE: "Please enter valid ( phone number).",
          HTTPCODE: 1007
     },
     EC_1008: {
          MESSAGE: "Please enter valid (correct state).",
          HTTPCODE: 1008
     },
     EC_1009: {
          MESSAGE: "Please enter valid (Product details required).",
          HTTPCODE: 1009
     },
     EC_1010: {
          MESSAGE: "Please enter valid (brand details).",
          HTTPCODE: 1010
     },
     EC_1011: {
          MESSAGE: "Please enter valid (offset).",
          HTTPCODE: 1011
     },
     EC_1012: {
          MESSAGE: "Please enter valid (offset limit ).",
          HTTPCODE: 1012
     },
     EC_1002: {
          MESSAGE: "Format not supported.(Try to upload non supported image file).",
          HTTPCODE: 1002
     },
     EC_1013: {
          MESSAGE: "Invalid address.",
          HTTPCODE: 1013
     },

     /** Error code related to session  */
     EC_2001: {
          MESSAGE: "Your Session Has Expired. Please Login Again.(Token mismatch)",
          HTTPCODE: 2001
     },
     EC_2002: {
          MESSAGE: "Please provide authorized token.(Token not sent to the api.)",
          HTTPCODE: 2002
     },

     /**Error code related to Otp & mobile details */
     EC_3001: {
          MESSAGE: "Invalid OTP.(Entered wrong otp.)",
          HTTPCODE: 3001
     },
     EC_3002: {
          MESSAGE: "Mobile number already exist.(When user is trying to update or register with already exist mobile number.)",
          HTTPCODE: 3002
     },
     EC_3003: {
          MESSAGE: "Telephone number can not be empty.(telephone number field is empty.)",
          HTTPCODE: 3003
     },
     Ec_3004: {
          MESSAGE: "Please enter valid phone number.(Invalid mobile number sent to api.)",
          HTTPCODE: 3004
     },
     /** Error code related to Feature */
     Ec_5000: {
          MESSAGE: "Internal server error.(error captured my try catch block.)",
          HTTPCODE: 5000
     },
     Ec_5001: {
          MESSAGE: "User not allowed to access this feature.(Specific feature not assigned to user.)",
          HTTPCODE: 5001
     },

     /**Error code related to configuration - beat , dc, fc */
     Ec_8001: {
          MESSAGE: "User does not have any beats assigned.",
          HTTPCODE: 8001
     },
     Ec_8002: {
          MESSAGE: "Incorrect location mapping with retailer. Contact Ebutor Support.(Trying to register the retailer who does not belong to ff assigned warehouse)",
          HTTPCODE: 8002
     },
     Ec_8003: {
          MESSAGE: "Your account has been deactivated. Plz contact support on - 04066006442.(Inactive user trying to login)",
          HTTPCODE: 8003
     },
     Ec_8004: {
          MESSAGE: "You don't have permission to edit this retailer.(Restricting Dc/Fc from updating there profile details.)",
          HTTPCODE: 8004
     },
     Ec_8005: {
          MESSAGE: "Improper Dc and Hub Configuration for the retailer or field force.(No mapping found for specific dc and hub.)",
          HTTPCODE: 8005
     },

     /**Error code related to orders */
     Ec_9001: {
          MESSAGE: "Order has been put on hold.(Order is no hold.)",
          HTTPCODE: 9001
     },
     Ec_9002: {
          MESSAGE: "You can\'t create shipment or invoice without confirm order.(Order is not yet confirmed.)",
          HTTPCODE: 9002
     },
     Ec_9003: {
          MESSAGE: "There is no product available for shipment.",
          HTTPCODE: 9003
     },
     Ec_9004: {
          MESSAGE: "There is no product available for cancel.",
          HTTPCODE: 9004
     },
     Ec_9005: {
          MESSAGE: "You have already created invoice for all products.",
          HTTPCODE: 9005
     },
     Ec_9006: {
          MESSAGE: "Inventory is not available of {SKU} product.",
          HTTPCODE: 9006
     },
     Ec_9007: {
          MESSAGE: "You have already created shipment of {SKU} product.",
          HTTPCODE: 9007
     },
     Ec_9008: {
          MESSAGE: "You can not cancel product more than available quantity.(Trying to cancel the order more then the available order quantity).",
          HTTPCODE: 9008
     },
     Ec_9009: {
          MESSAGE: "You can not return product before delivery.(InvalidReturnRequest).",
          HTTPCODE: 9009
     },
     Ec_9010: {
          MESSAGE: "ErrorWhileSalesReturn.",
          HTTPCODE: 9010
     },
     Ec_9011: {
          MESSAGE: "CanNotReturnMoreThenOrderQty.",
          HTTPCODE: 9011
     },

     /** Error code related to Pincode  */
     Ec_12001: {
          MESSAGE: "Pincode is not serviceable.(Trying to register retailer for the region which not under ebutor.).",
          HTTPCODE: 12001
     },

     /**Error code related to Retailer  */
     Ec_13001: {
          MESSAGE: "Genarate Retailer token (Mobile nunber not found in database).",
          HTTPCODE: 12001
     },
};
