/*
 * Lamport public key cracker, v1.0
 * JS is about 1/(nCPUcores) the speed of your full cpu, so this will be slow
 * Making this with openCL or openGL would drastically improve performance
 */

function keyCracker (nBits, nPairs, hashFunction, scope, verbose) {
	return new keyCracker.prototype.init(nBits, nPairs, hashFunction, scope, verbose);
}

// possibly improves functionality by not brute forcing values already brute forced
keyCracker.solutions = new function () {
	hashmap = {};
	this.add = function (hash, value) {
		if (!this.search(hash)) { hashmap[hash] = value; }
		return true
	};
	this.search = function (hash) {
		if (hashmap.hasOwnProperty(hash)) { return hashmap[hash]; }
		return false;
	};
};

keyCracker.prototype.init = function (nBits, nPairs, hashFunc, scop, verbos) {
	if (!nBits||nBits%8!==0||nBits<8) { nBits = 64; }
	if (!nPairs) { nPairs = 64; }
	var nbits = nBits, npairs = nPairs, hashFunction = hashFunc, scope = (typeof scop=="object"?scop:this), verbose;
	if (typeof hashFunction!=="function") { throw "keyCracker::__construct required function for hashFunc argument"; }
	verbose = (typeof verbos=="boolean"? verbos : false);
	
	// the things
	function crackNumber (hash) {
		var i, n, cobj = {};
		cobj.incremented = [];
		cobj.current = []
		n = hash.length/2;
		//console.log(hash + ".length/2 = " + n);
		for (i=0;i<n;i++) {
			cobj.array.push(0);
		}
		//console.log(cobj.current);
		cobj.cond = false;
		cobj.knum = "";
		cobj.index = cobj.array.length-1;
		cobj.match = function () {
			var i, k="", m=0;
			
			for (i=0;i<cobj.array.length;i++) {
				m=cobj[i].toString(16);
				while(m.length<2){m="0"+m;}
				k+=m;
			}
			if (hashFunction.call(scope, k)==hash) {
				cobj.knum = k;
				cobj.cond = true;
				if (verbose&&!diagnostic_suppress_verbose) {
					console.log("Value found: "+cobj.knum);
				}
			}
		}; 
		kraken(cobj);
		return cobj.knum;
	}
	// recursive madness
	function kraken (cobj) {
		var i, k, ind;
		if (cobj.index>0) {
			ind = cobj.index
			for (i=0;i<256;i++) {
				if (cobj.cond) { return; }
				cobj.array[ind]
				kraken(cobj);
				/*if (cobj.incremented[cobj.incremented.length-1]>253) {
					console.log("cobj.incremented[cobj.incremented.length-1]>253");
				}*/
			}
			console.log("kracken recursion(3):"+
				"\n	typeof cobj.current: "+typeof cobj.current+
				"\n	cobj.current.length: "+cobj.current.length+
				"\n	cobj.current: "+cobj.current.toString()+
				"\n	typeof cobj.incremented: "+typeof cobj.current+
				"\n	cobj.incremented.length: "+cobj.incremented.length+
				"\n	cobj.incremented: "+cobj.incremented.toString());
		} else {
			for (k=0;k<256;k++) {
				if (cobj.cond) { return; }
				cobj.current[0] = k;
				cobj.match();
				if (cobj.current[0]>253) {
					console.log("cobj.current[0]>253");
				}
			}
		}
	}
	this.crackPublicKey = function (publicKeyArray, diagnostic_suppress_verbose) {
		if (typeof scope!=="object"){scope = this;}
		var i, j, ns, n, cn, h, privateKeyArray = [], dt, stime, endtime;
		if (verbose&&!diagnostic_suppress_verbose){dt = new Date(); stime = dt.getTime(); console.log("crackPublicKey stime: " + stime + " ms");}
		for (i=0;i<publicKeyArray.length;i++){
			if (keyCracker.solutions.search(publicKeyArray[i][0])) {
				privateKeyArray.push([keyCracker.solutions.search(publicKeyArray[i][0])]);
			} else {
				cn = crackNumber.call(this, publicKeyArray[i][0]);
				privateKeyArray.push([cn]);
				keyCracker.solutions.add(publicKeyArray[i][0], cn);
			}
			if (keyCracker.solutions.search(publicKeyArray[i][1])) {
				privateKeyArray[i].push([keyCracker.solutions.search(publicKeyArray[i][1])]);
			} else {
				cn = crackNumber.call(this, publicKeyArray[i][1]);
				privateKeyArray[i].push(cn);
				keyCracker.solutions.add(publicKeyArray[i][1], cn);
			}
		}
		if(verbose&&!diagnostic_suppress_verbose){dt = new Date(); endtime = dt.getTime();
		console.log("crackPublicKey endtime: "+endtime + " ms");
		console.log("crackPublicKey total time: " + Math.round((endtime-stime)/1000) + " seconds");}
		return privateKeyArray;
	}
	
	function createPublicKey (privateKeyArray, hashFunction, scope) {
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
	}
	
	// diagnostic and dependencies
	this.crackNKeys = function (n, verbose, diagnostic_suppress_verbose) {
		var i, pkey, pubk, crackedk, dt, stime, endtime, collisions=0;
		dt = new Date(); stime = dt.getTime(); console.log("crackNKeys("+n+") start time: "+stime+" ms");
		for (i=0;i<n;i++) {
			pkey = createPrivateKey(); pubk = createPublicKey(pkey, hasher);
			pk1 = crackPublicKey(pubk, hasher, diagnostic_suppress_verbose);
			if(verbose){console.log("crackNKeys("+n+")["+i+"]: (pk1.toString() == pkey.toString()) = "+(pk1.toString() == pkey.toString()));}
			if(verbose){console.log("crackNKeys("+n+")["+i+"]: (createPublicKey(pk1) == createPublicKey(pkey)) = "+(createPublicKey(pk1) == createPublicKey(pkey)));}
			if (pk1.toString()!==pkey.toString()) {collisons++;}
		}
		dt = new Date(); endtime = dt.getTime();
		console.log("crackNKeys("+n+") end time: "+endtime+" ms");
		console.log("crackNKeys("+n+") total time: "+Math.round((endtime-stime)/1000)+" seconds");
		console.log("crackNKeys("+n+") collisions: "+collisions);
	}
	function createPrivateKey () {
		var i, privateKeyAr = [];
		for (i=0;i<npairs;i++) {
			privateKeyAr.push(getNumPair());
		}
		return privateKeyAr;
	}
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
};

// expedient hashes = not 100% collision proof
// for testing purposes
function hasher (nbits) {
	return new hasher.prototype.init(nbits);
}
hasher.prototype.init = function (nBits) {
	var nbits = nBits;
	
	this.hash = function (data) {
		if (typeof nbits!=="number") { nbits = 256; }
		if (nbits>1024) { nbits = 1024; }
		
		var i, n, c = 1024, n1 = [], data2 = {}, hash = "";
		function pad (data, index) {
			var s1 = data.substr(0, index), s2 = data.substr(index, data.length-index);
			"pad: "+s1 + algos.call(this, 16)(s1.substr(s1.length-2,2)+s2.substr(0, 2)).charAt(2) + s2;
			return s1 + algos.call(this, 16)(s1.substr(s1.length-2,2)+s2.substr(0, 2)).charAt(2) + s2;
		}
		
		n = nbits-nbits%8;
		if (n<32){n1.push(16);data2[16]=[];}
		while (c>17&&n>0) {
			if (c>n&&n%c) {
				c = c/2;
			} else if (n%c) {
				n = n%c;
				n1.push(c);
				data2[c]=[];
			} else {
				n1.push(c);
				data2[c]=[];
				n-=c;
				c = c/2;
			}
		}
		
		data = hasher.checkHex(data);
		data = (function(data, n, padscope) {
			var i=2;
			while (data.length<n) {
				if (i>data.length-2) { i=2; }
				data = pad.call(padscope, data, i);
				i+=2;
			}
			return data;
		})(data, nbits/4, this);
		data = (function(data, n, padscope) {
			var i=2;
			while (data.length<n) {
				if (i>data.length-2) { i=2; }
				data = pad.call(padscope, data, i);
				i+=2;
			}
			return data;
		})(data, n1[0]/4, this);
		
		while (data.length>=n1[n1.length-1]/4) {
			for (i=0;i<n1.length;i++) {
				if (data.length>=n1[i]/4) {
					data2[n1[i]].push(data.substr(0,n1[i]/4));
					data = data.substring(n1[i]/4, data.length);
				}
			}
		}
		for (n in data2) {
			for (i=0;i<data2[n].length;i++) {
				hash += hasher.algorithms[(data2[n][i].length*4).toString()](data2[n][i]);
			}
		}
		while (hash.length*4>nbits&&nbits>15&&!(nbits%8)) {
			hash = (function(h){
				var a,b,c;
				a = hash.substr(0,2); b = hash.substr(hash.length-3,2);
				c = hasher.algorithms["16"](a+b);
				return hash.substring(2,hash.length/2)+c[1]+c[2]+hash.substring(hash.length/2+1,hash.length-1);
			})(hash);
		}
		
		return hash;
	};
	
	function algos (bits) {
		if (typeof bits!=="number"||bits<16) { bits = 16; }
		var nbits = bits-bits%16;
		
		run = function (data) {
			if (typeof data!=="string") { data = "0"; }
			if (data=="0") {
				while (data.length<nbits/4) { data += "0"; }
				return data;
			}
			data = hasher.checkHex(data);
			function pad (data, index) {
				var s1 = data.substr(0, index), s2 = data.substr(index, data.length-index);
				return s1 + hasher.algorithms["16"].call(this, s1.substr(s1.length-2,2)+s2.substr(0, 2)).charAt(2) + s2;
			}
			data = (function(data, padscope) {
				var i=2;
				while (data.length%(nbits/4)!=0) {
					while (data.length<4) { data += "0"; break; }
					if (i>data.length-2) { i=2; }
					data = pad.call(padscope, data, i);
					i+=2;
				}
				return data;
			})(data, this);
			return hasher.algorithms[nbits.toString()](data);
		};
		
		return run;
	}
};

hasher.checkHex = function (str) {
	var i, k;
	if (str.length%2) {
		return convert(str);
	} else {
		k = false;
		for (i=0;i<str.length;i+=2) {
			if (isNaN(parseInt(str.substr(i,1), 16))) { k = true; }
			if (isNaN(parseInt(str.substr(i+1,1), 16))) { k = true; }
		}
		if (k) { return convert(str); }
		return str;
	}
	function convert (chars) {
		var i, hex = "";
		for (i=0;i<str.length;i++) {
			hex += str.charCodeAt(i).toString(16);
		}
		return hex;
	}
	return hex;
};
	
hasher.algorithms = {
	"16": function hasher16bit (data) {
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
	},
	"32": function hasher32bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,2); b = data.substr(2,2); c = data.substr(4,2); d = data.substr(6,2);
		a2 = hasher.algorithms["16"](a+b); b = hasher.algorithms["16"](b+c); c = hasher.algorithms["16"](c+d); d = hasher.algorithms["16"](d+a); a = a2;
		return hasher.algorithms["16"](a+c)+hasher.algorithms["16"](b+d);
	},
	"64": function hasher64bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,4); b = data.substr(4,4); c = data.substr(8,4); d = data.substr(12,4);
		a2 = hasher.algorithms["32"](a+b); b = hasher.algorithms["32"](b+c); c = hasher.algorithms["32"](c+d); d = hasher.algorithms["32"](d+a); a2 = a;
		return hasher.algorithms["32"](a+c) + hasher.algorithms["32"](b+d);
	},
	"128": function hasher128bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,8); b = data.substr(8,8); c = data.substr(16,8); d = data.substr(24,8);
		a2 = hasher.algorithms["64"](a+b); b = hasher.algorithms["64"](b+c); c = hasher.algorithms["64"](c+d); d = hasher.algorithms["64"](d+a); a2 = a;
		return hasher.algorithms["64"](a+c) + hasher.algorithms["64"](b+d);
	},
	"256": function hasher256bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,16); b = data.substr(16,16); c = data.substr(32,16); d = data.substr(48,16);
		a2 = hasher.algorithms["128"](a+b); b = hasher.algorithms["128"](b+c); c = hasher.algorithms["128"](c+d); d = hasher.algorithms["128"](d+a); a2 = a;
		return hasher.algorithms["128"](a+c) + hasher.algorithms["128"](b+d);
	},
	"512": function hasher512bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,32); b = data.substr(32,32); c = data.substr(64,32); d = data.substr(96,32);
		a2 = hasher.algorithms["256"](a+b); b = hasher.algorithms["256"](b+c); c = hasher.algorithms["256"](c+d); d = hasher.algorithms["256"](d+a); a2 = a;
		return hasher.algorithms["256"](a+c) + hasher.algorithms["256"](b+d);
	},
	"1024": function hasher1024bit (data) {
		if (typeof data=="undefined") { return "0000"; }
		if (typeof data!=="string") { data = data.toString(); }
		var a, a2, b, c, d;
		a = data.substr(0,64); b = data.substr(64,64); c = data.substr(128,64); d = data.substr(192,64);
		a2 = hasher.algorithms["512"](a+b); b = hasher.algorithms["512"](b+c); c = hasher.algorithms["512"](c+d); d = hasher.algorithms["512"](d+a); a2 = a;
		return hasher.algorithms["512"](a+c) + hasher.algorithms["512"](b+d);
	}
};
