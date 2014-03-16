/*
 * Merkle Signature Scheme Implementation for JavaScript
 * Author:      Jonathan Voss
 * Date:        March 13th, 2014
 * Description: This provides a JavaScript implementation of the Merkle signature scheme,
 *              a Merkle root (hereafter address) is distributed as a public key identifier.
 *              In this system, each address has a number of private keys stored; the root
 *              is calculated by hashing each corresponding public key, then hashing together
 *              public key hashes to form nodes (level 1), then hashing those hashes together
 *              to form other nodes (level 2), down to level n, where 2^n = number of key
 *              pairs available for that address. I recommend using this with my Lamport
 *              signature implementation for cryptographic applications intended to be
 *              hardened against quantum computers. However, it is agnostic for both the key
 *              pair scheme used and hashing algorithm used.
 *              In this implementation, the first byte of the Merkle root/address is n. To
 *              verify that a message signature is valid, the receiver uses the supplied
 *              public key, n hashes, public key index (which of 2^n), and public address.
 *              It first checks that the number of node hashes equals the n value of the root
 *              hash; it then computes the hash of the public key; it then uses the index
 *              to correctly hash the nodes into the Merkle root. If the nodes all match, the
 *              supplied public key is considered valid and the signature is then checked.
 */

var hashtree = function (nLevel, hashfunc, signatureFunc, hfscope, sfscope) {
	return new hashtree.prototype.init (nLevel, hashfunc, signatureFunc, hfscope, sfscope);
};

hashtree.prototype.init = function (n, hashfunc, sigfunc, hfscope, sfscope) {
	if (typeof nLevel!=="number"||isNaN(nLevel)||typeof hashfunc!=="function"||typeof sigfunc!=="function"){ return null;}
	if (typeof hfscope!=="object"||!hfscope) { hfscope = {}; }
	if (typeof sfscope!=="object"||!sfscope) { sfscope = {}; }
	
	// index (0 through n-1):
	var privatekeys = [], nodetree = {l0:[]}, address, index;
	
	// generates a new Merkle tree
	// requires private and public key generation function and optional scopes
	this.generate = function ( privateKeyGenFunc, publicKeyGenFunc, prvkgfscope, pubkgfscope ) {
		if (typeof prvkgfscope!=="object"||!prvkgfscope) { prvkgfscope = {}; }
		if (typeof pubkgfscope!=="object"||!pubkgfscope) { pubkgfscope = {}; }
		
		var i, g, c;
		
		for (i=0;i<Math.pow(2,n);i++) {
			privatekeys.push(privateKeyGenFunc.call(prvkgfscope));
			nodetree.l0.push(hashfunc.call(hfscope, publicKeyGenFunc.call(pubkgfscope, privatekeys[i])));
		}
		
		for (g=1;g<n;g++) {
			c = Math.pow(2,n-g)
			nodetree["l"+g] = [];
			for (i=0;i<c;i+=2) {
				nodetree["l"+g].push(hashfunc.call(hfscope, nodetree["l"+(g-1)][i]+nodetree["l"+(g-1)][i+1]));
			}
		}
		
		nodetree["l"+n] = hashfunc.call(hfscope, nodetree["l"+(n-1)][0]+nodetree["l"+(n-1)][0]);
		address = (n.toString(16)<2 ? "0":"") + n.toString(16) + nodetree["l"+n];
		
		return address;
		
	};
	
	// populate with keys
	// optons: {pubKGfunc:function(){}, pkgfscope:{}} or {publicKeyArray:[]}
	this.populate = function ( privateKeyArray, options ) {
		if (typeof privateKeyArray!=="object"||typeof privateKeyArray.push!=="function"||typeof options!=="object") { return null; }
		if (typeof options.pubKGfunc!=="function"&&(typeof publicKeyArray!=="object"||typeof publicKeyArray.push!=="function")) { return null; }
		
		var i, g, c;
		
		if (options.pubKGfunc) {
			if (typeof options.pukgfscop!=="object") { options.pubkgfscope = {}; }
			for (i=0;i<privateKeyArray.length;i++) {
				privatekeys.push(privateKeyArray[i]);
				nodetree.l0.push(hashfunc.call(hfscope, options.pubKGfunc.call(pubkgfscope, privatekeys[i])));
			}
		} else {
			if (privateKeyArray.length!==options.publicKeyArray.length) { return null; }
			for (i=0;i<privateKeyArray.length;i++) {
				privatekeys.push(privateKeyArray[i]);
				nodetree.l0.push(hashfunc.call(hfscope, options.publicKeyArray[i]));
			}
		}
		
		for (g=1;g<n;g++) {
			c = Math.pow(2,n-g)
			nodetree["l"+g] = [];
			for (i=0;i<c;i+=2) {
				nodetree["l"+g].push(hashfunc.call(hfscope, nodetree["l"+(g-1)][i]+nodetree["l"+(g-1)][i+1]));
			}
		}
		
		nodetree["l"+n] = hashfunc.call(hfscope, nodetree["l"+(n-1)][0]+nodetree["l"+(n-1)][0]);
		address = (n.toString(16)<2 ? "0":"") + n.toString(16) + nodetree["l"+n];
		
		return address;
	};
	
	// set current index
	// this is primarily useful when loading in a keyring/wallet previously used
	this.setIndex = function (i) {
		if (typeof i=="number"&&!isNaN(i)&&i<=Math(2,n)) {
			index = i; return true;
		}
		return false;
	};
	
	// retrieves current private key without incrementing index
	// returns false if all keys have been used
	this.getKey = function () {
		return (index<privatekeys.length ? index : false);
	};
	
	// returns a JSON signature including key index, all nodes, and 
	this.signData = function (data) {
		if (typeof data!=="string") { return null; }
		
		var sig = {}, i, g;
		
		sig.address = address;
		sig.nodes = {};
		sig.index = index;
		
		return sig;
	};
};
