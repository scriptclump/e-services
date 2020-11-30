'use strict';

const crypto = require('crypto');//crypto js is library used for encryption and decryption
const ENCRYPTION_KEY = process.env.ENCRYPTION_KEY; // Must be 256 bits (32 characters)
const IV_LENGTH = 16; // For AES, this is always 16(used to make ciphertext more secure )

/*
 * purpose : Used for data encryption using aes-256-cdc algorithms(which is performing 14 rounds of transformation),
 * Required : Encryption_key , IV_length 
 * Return : Encrypted data,
 * Author : Deepak tiwari,
 * Note : Same keys is required from both encryption and decryption of data.
 * */
console.log("-----hvhvh");
function encrypt(text) {
	console.log("ssd");
	let iv = crypto.randomBytes(IV_LENGTH);
	console.log("iv", iv);
	let cipher = crypto.createCipheriv('aes-256-cbc', Buffer.from(ENCRYPTION_KEY), iv);//Used to create an instance of cipher
	let encrypted = cipher.update(JSON.stringify(text));//updating cipher text with encrypted data  
	encrypted = Buffer.concat([encrypted, cipher.final()]);//Final cipher text (We can not update any more )
	return iv.toString('hex') + ':' + encrypted.toString('hex');//returning iv , encrypted  data.//utf16le(encoding algorithms)
}

/*
 * purpose : Used for data decryption using aes-256-cdc algorithms(which is performing 14 rounds of transformation),
 * Required :Encrypted cipher text
 * Return : Decrypted data
 * Author : Deepak tiwari,
 * Note : Same keys is required from both encryption and decryption of data.
 * */



function decrypt(text) {
	let textParts = text.split(':');//spliting the encrypted text based on ':' (Because after encryption each key bind with ':').
	let iv = Buffer.from(textParts.shift(), 'hex');//In this peice of code i am fetching Oth element form textparts array and decoding that key to get origin value of iv keys
	let encryptedText = Buffer.from(textParts.join(':'), 'hex');//joining iv and encrypted text  and decoding them
	let decipher = crypto.createDecipheriv('aes-256-cbc', Buffer.from(ENCRYPTION_KEY), iv);//creating Instances of the Decipher class are used to decrypt data
	let decrypted = decipher.update(encryptedText);//updating cyphertext with decrypted data
	decrypted = Buffer.concat([decrypted, decipher.final()]);//final ciphertext
	return decrypted.toString();
}

module.exports = { decrypt, encrypt };
