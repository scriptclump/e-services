
let cpmessage = {
     'lowinventory': 'One/More products does not have sufficient stock Error_Code : 6001',
     'invalidCustomer': 'please send customer_type Error_Code : 1001',
     'serverError': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 5000",
     'invalidRequestBody': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7002",
     'internalCatch': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7001",//(in case if any db query fails)
     'InvalidOtp': 'Invalid OTP Error_Code : 3001',
     'invalidToken': "Your Session Has Expired. Please Login Again Error_Code : 2001",
     'tokenNotPassed': "Please provide authorized token Error_Code : 3002",
     'UnsuccessfulOperation': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7003",
     'EmptyHubId': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7004",
     "EmptyLeWhId": "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7005",
     "EmptySegmentId": "Something went wrong. Plz contact support on - 04066006442 Error_Code : 7006",
     "InvalidBrandId": "Please enter valid brand details Error_Code : 1010",
     "InvalidOffset": "Please enter valid offset value Error_Code : 1011",
     "InvalidOffsetLimit": "Please enter valid offser limit Error_Code : 1012",
     "InavlidFirst": "Please enter valid first name Error_Code : 1003",
     "InvalidLastname": "Please enter valid last name Error_Code : 1004",
     "Invalid": "Please enter valid email Error_Code : 1005",
     "AlreadyExistMobileNumber": "Mobile number already exist Error_Code : 1001",
     "InvalidPincode": "Please enter valid pincode Error_Code : 1005",
     "InactivePincode": "Pincode is not serviceable Error_Code : 12001",
     "NoBeatAssign": "User does not have any beats assigned Error_Code : 8001",//based on customerToken we were fetching BeatID if not found BeatID then passing this error (categoryController)
     "cacheIsuuses": "Issue with cache",
     "InvalidCategoryId": "Please enter valid category  Error_Code : 1001",
     "EmptyProductId": "Product details required Error_Code : 1009", //productid not passed
     "InvalidProductId": "Invalid product details Error_Code : 6004",
     "addReviewRating": "Thank you for giving your valuable feedback",
     "alreadyRated": "You have already rated to this product Error_Code : 6003",
     "ProductNotFound": "No products available Error_Code : 6002",//in getProducts when response is empty 
     "Emptytelephone": "Telephone number can not be empty Error_Code : 3003",
     "invalidTelephoneNumber": "Please enter valid phone number Error_Code : 1007",
     "foreignkeyconstraint": "We cannot add or update parent details Error_Code : 13001",
     "NotAllowedToAccessFeature": "User not allowed to access this feature Error_Code : 5001",
     "NotMatchingLegalENtity": "Incorrect location mapping with retailer. Contact Ebutor Support Error_Code : 8002",//throwing in case of registration when ff le_id in users table is matching with wh_serviceable table pincode(getFFBeatByPincode )
     "IncorrectStatePincodeMapping": "Please select correct state Error_Code : 1008",//throwing this error when ther is pincode mapping for entered state in cities_pincode table or if there no pincode register for perticular statename
     "EmptyFlagValue": "Flag is required Error_Code : 1001",// for these apis we were passing  flag to get response from them(flag is required field)
     "ConfirmOtpInActiveUser": "Your account has been deactivated. Plz contact support on - 04066006442 Error_Code : 8003",//if user is in active then legal_entity_type_id
     "InCheckInGettingError": "Banner server got down  Error_Code : 13002",
     "UpdateProfileRestrictionForDcFc": "You don't have permission to edit this retailer Error_Code : 8004", // retricting dc/fc updateProfile by ff 
     "FeedbackSubmitted": "Feedback submitted successfully",
     "NoReponse": "Could not get any response. Plz contact support on - 04066006442 Error_Code : 1001",
     "WrongDcFcConfiguration": "Improper Dc and Hub Configuration for the retailer or field force Error_Code : 8005",
     'GenarateRetailerToken': "Something went wrong. Plz contact support on - 04066006442 Error_Code : 130001",
     'InvalidReturnOrderRequest': 'You can not return product before delivery Error_Code :9009',
     'CanNotReturnMoreThenOrderQty': 'You can not return product more then ordered quantity Error_Code :9011',
     'ErrorWhileSalesReturn': 'Something went wrong. Plz contact support on - 04066006442 Error_Code : 9010',
     'ReturnRequestProcess': 'Your return request intiated successfully.',
     'CancelReturnRequest': 'Cancelled successfully.'
}

module.exports = cpmessage;














