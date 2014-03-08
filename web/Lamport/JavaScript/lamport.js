/*
 * Lamport Signature Implementation for JavaScript
 * Author:      Jonathan Voss
 * Date:        March 6th, 2014
 * Description: This provides a JavaScript implementation of the Lamport signature scheme,
 *              in which a private key is an array made of pairs random numbers of nBits length,
 *              where nBits is a multiple of 8. The array length is equal to nBits, and the size
 *              of a data it can sign is data.length<=nBits. If the data is greater, then a hash
 *              function will be used. This implementation is hash function agnostic and can
 *              thus be used with any hash function (I recommend sha-3).
 *              The signature is a collection of numbers from the private key: for every bit in
 *              the data or hash, there is one number for 0 and another for 1. This is verified
 *              by hashing the supplied numbers and comparing to the corresponding hashes in the
 *              published public key (which is each number in the private key hashed).
 *              This implementation uses hexadecimal to avoid encoding issues and to minimize
 *              memory use (compared to using strings in base 2). The shortened method names 
 *              output and input purely in text, while the long method names output and input in
 *              arrays. There are internal functions for parsing between the two forms.
 * 
 */

var lamport = function (nBits, nPairs) {
	return new lamport.prototype.init (nBits, nPairs);
}
lamport.prototype.init = function (nBits, nPairs) {
	if (!nBits||nBits%8!==0||nBits<8) { nBits = 64; }
	if (!nPairs) { nPairs = 64; }
	var nbits = nBits, npairs = nPairs;
	
	this.cPK = function () {
		return rawHex(this.createPrivateKey());
	};
	this.sD = function (data, privateKeyText, hashFunction, scope) {
		return rawHex(this.signData(data, hexToKeyArray(privateKeyText), hashFunction, scope));
	};
	this.chkSig = function (data, signatureText, publicKeyText, hashFunction, scope) {
		return this.verifySignature (data, hexToSigArray(signatureText), hexkeyToArray(publicKeyText), hashFunction, scope);
	};
	
	this.createPrivateKey = function () {
		var i, privateKeyAr = [];
		for (i=0;i<npairs;i++) {
			privateKeyAr.push(getNumPair());
		}
		return privateKeyAr;
	};
	this.createPublicKey = function (privateKeyArray, hashFunction, scope) {
		if (typeof hashFunction!=="function"||typeof privateKeyArray!=="object"||typeof privateKeyArray.push!=="function") { return null; }
		if (!scope||typeof scope!=="object") { scope = this; }
		var i, k, publicKeyAr = [];
		
		for (i=0;i<privateKeyArray.length;i++) {
			publicKeyAr.push([
				hashFunction.call(scope, privateKeyArray[i][0]),
				hashFunction.call(scope, privateKeyArray[i][1])
				]);
		}
		return publicKeyAr;
	};
	this.signData = function (data, privateKeyArray, hashFunction, scope, dataIsHex) {
		if (!scope||typeof scope!=="object") { scope = this; }
		if (typeof data=="undefined"||typeof hashFunction!=="function"||typeof privateKeyArray!=="object"||typeof privateKeyArray.push!=="function") { return null; }
		var i, k, bits, hash, nhashbits, signature = [];
		
		data = (typeof data=="string" ? data : data.toString());
		
		if (data.length<privateKeyArray.length) {
			if (typeof dataIsHex!=="undefined"&&dataIsHex) {
				for (i=0;i<data.length;i+=2) {
					bits = parseInt(data[i] + data[i+1], 16).toString(2).split("");
					while (bits.length<8) { bits.splice(0,0,"0"); }
					for (k=0;k<8;k++) {
						signature.push(privateKeyArray[i/2][parseInt(bits[k])]);
					}
				}
			} else {
				for (i=0;i<data.length;i++) {
					bits = data[i].charCodeAt(0).toString(2).split("");
					while (bits.length<8) { bits.splice(0,0,"0"); }
					for (k=0;k<8;k++) {
						signature.push(privateKeyArray[i][parseInt(bits[k])]);
					}
				}
			}
		} else {
			hash = hashFunction.call(scope, data);
			hash = (typeof hash=="string" ? hash : hash.toString()).split("");
			if (hash.length*4>privateKeyArray.length) { return null; }
			for (i=0;i<hash.length;i+=2) {
				bits = parseInt(hash[i]+hash[i+1], 16).toString(2).split("");
				while (bits.length<8) { bits.splice(0,0,"0"); }
				for (k=0;k<8;k++) {
					signature.push(privateKeyArray[i/2][parseInt(bits[k])]);
				}
			}
		}
		
		return signature;
	};
	this.verifySignature = function (data, signatureArray, publicKey, hashFunction, scope) {
		if (!scope||typeof scope!=="object") { scope = this; }
		
	};
	
	
	// Private Methods
	
	function getNumPair () {
		var ns = [[""],[""]], n1, n2, i, k;
		for (i=0;i<(nbits/8);i++) {
			n1 = ""; n2 = "";
			for (k=0;k<8;k++) {
				n1 += Math.round(Math.random());
				n2 += Math.round(Math.random());
			}
			n1 = parseInt(n1, 2).toString(16);
			n2 = parseInt(n2, 2).toString(16);
			ns[0] += (n1.length<2 ? (n1.length<1?"0":"") + "0" : "" ) + n1;
			ns[1] += (n2.length<2 ? (n2.length<1?"0":"") + "0" : "" ) + n2;
		}
		return ns;
	}
	
	// recursive function; converts multi-dimension arrays of strings to block of hex
	function charToHex (charData) {
		var output = "", i, k;
		for (i=0;i<charData.length;i++) {
			if (typeof charData[i]=="object"&&typeof charData[i].push=="function") {
				output += charToHex(charData[i]);
			} else if (typeof charData=="string") {
				output += charData.charCodeAt(i).toString(16);
			} else if (typeof charData[i]=="string") {
				for (k=0;k<charData[i].length;k++) {
					output += charData[i].charCodeAt(k).toString(16);
				}
			}
		}
		return output;
	}
	// recursive function; converts multi-dimension arrays of hex into a single block
	function rawHex (hexArray) {
		var output = "", i;
		for (i=0;i<hexArray.length;i++) {
			if (typeof hexArray[i]=="object"&&typeof hexArray[i].push=="function") {
				output += rawHex(hexArray[i]);
			} else {
				output += (hexArray[i]).toString(); // just to make sure everything is a string
			}
		}
		return output;
	}
	// recursive function to convert from hex to chars
	function rawChars (hexData) {
		var output = "", i, k;
		for (i=0;i<hexData.length;i++) {
			if (typeof hexData[i]=="object"&&typeof hexData[i].push=="function") {
				output += rawChars(hexData[i]);
			} else {
				if (typeof hexData=="string") {
					output += String.fromCharCode(parseInt(hexData[i]+hexData[i+1], 16));
					i++;
				} else {
					for (k=0;k<hexData[i].length;k+=2) {
						output += String.fromCharCode(parseInt(hexData[i][k]+hexData[i][k+1], 16));
					}
				}
			}
		}
		return output;
	}
	function hexToKeyArray (hexString) {
		
	}
	function hexToSigArray (hexString) {
		
	}
	
	/* collision resistant 16 bit hashing function for testing purposes (limited to 16 bits input; for use with nBits = 16 only)
	 * 16 bit testing creates less messy hex data to wade through and is actually pretty legit:
	 * privatekey is [nbits][2][nbits], or n possible private keys = 2^(nbits*2*nbits) 
	 * 2^(16*2*16) = 2^512 = 1.34078*10^134 total possible key pairs
	 * 8 bits would be only 2^(8*2*8) = 2^128 =  3.4028*10^38 possible private keys ... yeah, Lamport signature scheme is pretty effective
	 * It gets even worse: if we use a 256 bit hashing algorithm for signing data and 8 bit random numbers, we end up with
	 * privatekey = [256][2][8] = 4,096; n unique keys = 2^4096 = 1.044*10^1233
	 */
	this.hasher = function (data) {
		var a, b, c, d, aa, bb;
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		a = parseInt(data[0]+data[1], 16);
		b = parseInt(data[2]+data[3], 16);
		c = (b-a/b<0 ? (b-a/b)*(-1) : b-a/b);
		d = (a-b/a<0 ? (a-b/a)*(-1) : a-b/a);
		a = String.fromCharCode(parseInt(data[0]+data[1], 16));
		b = String.fromCharCode(parseInt(data[2]+data[3], 16));
		aa = (a^c|b&(~c)).toString(16); while (aa.length<2) {aa = "0"+aa;}
		bb = (b^d|a&(~d)).toString(16); while (bb.length<2) {bb = "0"+bb;}
		return aa + bb;
	}
}
