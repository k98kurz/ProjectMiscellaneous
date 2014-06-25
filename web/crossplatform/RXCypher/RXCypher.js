/*
 * Title:		RXCypher
 * Description:	Simple and fast stream cipher algorithm - JavaScript implementation
 * Author:		Jonathan Voss
 * Date:		8/7/2012
 * Namespace:	github.com/k98kurz
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

var RXCypher = {
	encrypt: function(plaintext, key) {
		var ciphertext = "", i, j, g, t, k;
		for (i=0, j=0; i<plaintext.length; i++, j++) {
			j = (j==key.length) ? 0 : j;
			t = plaintext.charCodeAt(i);
			k = key.charCodeAt(j);
			g = (t+k>255 ? t+k-255 : t+k)^k;
			ciphertext += g.toString(16);
		}
		return ciphertext;
	},
	decrypt: function(ciphertext, key) {
		var plaintext = "", i, j, g, c, k;
		for (i=0, j=0; i<ciphertext.length; i++, j++) {
			j = (j==key.length) ? 0 : j;
			c = parseInt(ciphertext.charAt(i)+ciphertext.charAt(++i),16);
			k = key.charCodeAt(j);
			g = (c^k)-k<0 ? (c^k)-k+255 : (c^k)-k;
			plaintext += String.fromCharCode(g);
		}
		return plaintext;
	}
};
