const tesseract = require("node-tesseract-ocr");
const isImageUrl = require('is-image-url');
var request = require('request');
var fs = require('fs');
var path = require('path');
const config = {
    lang: "eng",
    oem: 1,
    psm: 3,
}


getDocumentDetails = function(req, res){ 
    let img_path = req.body.img_path;
    let document_type = req.body.document_type;
    if(img_path == null || document_type == null){
        return res.send('Please send the required parameter img_path, document_type');
    } else{
        if(document_type == 'PAN'){
            var result = {
                "status": "error",
                "message": "Unable to extract pan number. Please validate the document",
                "data": []
            };
            var pan_card = getPanCardNumber(img_path);
            pan_card.then(function(pan_card) {
                if(pan_card != null){
                    var result = {
                        "status": "success",
                        "data": pan_card
                    };
                }
                return res.send(result);
            }, function(err) {
                return res.send(err);
            })
        } else if (document_type == 'Aadhar') {
            var result = {
                "status": "error",
                "message": "Unable to extract Aadhar number. Please validate the document",
                "data": []
            };

            var aadhar_number = getAadharNumber(img_path);
            aadhar_number.then(function(aadhar_number) {
                if(aadhar_number != null){
                    var result = {
                        "status": "success",
                        "data": aadhar_number
                    };
                }
                return res.send(result);
            }, function(err) {
                return res.send(err);
            })
        } else {
            var result = {
                "status": "error",
                "message": "Please send the valid document URL type (PAN|Aadhar)",
                "data": []
            };
            return res.send(result);
        }

    }
}
getPanCardNumber = function(url){
    return new Promise(function(resolve, reject) {

        const dir = '../../assets/scanned_docs/';
        const filename = dir+Date.now()+'-scanned.png';
        var isImage = isImageUrl(url);
        if(isImage){
            var writeFile = fs.createWriteStream(path.resolve(__dirname, filename), (err, data) => {
                if (err) reject(err);
            });
            request(url).pipe(writeFile).on('close', function() {
                tesseract.recognize(path.resolve(__dirname, filename))
                .catch(err => console.error(err))
                .then(function (result) {
                    let pancard_regex = '[A-Z]{5}[0-9]{4}[A-Z]{1}';
                    let has_pancard_text = false;
                    if(result != null){
                        var found_text = false;
                        result.split(/\r?\n/).forEach(function(line_text, index) {
                            if( line_text == "Permanent Account Number"){
                                has_pancard_text = true;
                            }
                            if(line_text.match(pancard_regex) && has_pancard_text == true){
                                resolve(line_text);
                            }
                        });
                        if(!found_text){
                            var result = {
                                "status": "error",
                                "data": "Unable to extract data, Please check the document "
                            };
                            reject(result)
                        }                    
                    } else{
                        var result = {
                            "status": "error",
                            "data": "PAN number not found"
                        };
                        reject(result)
                    }
                    fs.unlink(path.resolve(__dirname, filename), function (err){
                        if(err) throw err;
                    });
                });
            });
        } else{
            var result = {
                "status": "error",
                "data": "No image available"
            };
            reject(result)
        }
    })
}
getAadharNumber = function(url){
    return new Promise(function(resolve, reject) {

        const dir = '../../assets/scanned_docs/';
        const filename = dir+Date.now()+'-scanned.png';
        var isImage = isImageUrl(url);
        if(isImage){
            var writeFile = fs.createWriteStream(path.resolve(__dirname, filename), (err, data) => {
                if (err) reject(err);
            });
            request(url).pipe(writeFile).on('close', function() {
                tesseract.recognize(path.resolve(__dirname, filename))
                .catch(err => console.error(err))
                .then(function (result) {
                 //   console.log('Aadahr Content: ',result);
                  let aadhar_regex_one =  /^\d{4}\s\d{4}\s\d{4}$/gm;
                  let aadhar_regex_two = '[0-9]{12}';
                    if(result != null){
                        var found_text = false;
                        result.split(/\r?\n/).forEach(function(line_text, index) {
                            text = line_text.trim();
                            if( text.match(aadhar_regex_two) ){
                                resolve(text.match(aadhar_regex_two)[0]);
                                found_text = true;
                            }
                            if( text.match(aadhar_regex_one) ){
                                resolve(text.match(aadhar_regex_one)[0]);
                                found_text = true;
                            }                       
                        });
                        if(!found_text){
                            var result = {
                                "status": "error",
                                "data": "Unable to extract data, Please check the document "
                            };
                            reject(result)
                        }                
                    } else{
                        var result = {
                            "status": "error",
                            "data": "Aadhar number not found"
                        };
                        reject(result)
                    }
                    fs.unlink(path.resolve(__dirname, filename), function (err){
                        if(err) throw err;
                    });
                });
            });
        } else{
            var result = {
                "status": "error",
                "data": "No image available"
            };
            reject(result)
        }
    })
}

module.exports = { getDocumentDetails, getPanCardNumber, getAadharNumber }