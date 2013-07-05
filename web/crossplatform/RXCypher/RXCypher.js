/*
 * Title:		RXCypher
 * Description:	Simple and fast stream cipher algorithm - JavaScript implementation
 * Author:		Jonathan Voss
 * Date:		8/7/2012
 * Namespace:	CodeHeaven.com
 * License:		MIT
 *
 * Basic byte-level algorithm
 * t = plaintext byte, c = ciphertext byte, k = encryption key byte
 *
 *		function enc (t, k) { return (t+k>255 ? t+k-255 : t+k)^k; }
 *		function dec (c, k) { return (c^k)-k<0 ? (c^k)-k+255 : (c^k)-k; }
 *
 *
 * Full singleton
 * Methods:
 *		enc ( sString, sKey )
 *			encrypts plaintext sString with encryption key sKey
 *		dec ( sCypherText, sKey )
 *			decrypts cipher text sCypherText with decryption key sKey
*/

var RXCypher = new function () {
	this.enc = function ( sString, sKey ) {
		if (typeof (sString)=="string"&&typeof (sKey)=="string") {
			var sCypherOutput = "", i, k;
			for (i=0, k=0; i<sString.length; i++, k++) {
				if (k >= sKey.length) k = 0;
				sCypherOutput += String.fromCharCode ( _enc ( sString[i].charCodeAt(0), sKey[k].charCodeAt(0) ) );
			}
			return sCypherOutput;
		} else { return false; }
	};
	this.dec = function ( sCypherText, sKey ) {
		if (typeof (sCypherText)=="string"&&typeof (sKey)=="string") {
			var sClearText = "", i, k;
			for (i=0, k=0; i<sCypherText.length; i++, k++) {
				if (k >= sKey.length) k = 0;
				sClearText += String.fromCharCode ( _dec ( sCypherText[i].charCodeAt(0), sKey[k].charCodeAt(0) ) );
			}
			return sClearText;
		} else { return false; }
	};
	function _enc (c, k) {
		var g = (c+k>255) ? c+k-255 : c+k;
		return g ^ k;
	}
	function _dec (c, k) {
		var g = c ^ k;
		return (g-k<0) ? g-k+255 : g-k;
	}
};
