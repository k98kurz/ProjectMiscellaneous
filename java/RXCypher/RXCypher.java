package com.noobgrinder.jonathan.rxcypher;

public class RXCypher {
	private String key;
	
	RXCypher (String k) throws Exception {
		if (k.length()>0) {
			key = k;
		} else {
			throw new Exception("Must supply an encryption key");
		}
	}
	
	public String encrypt (String str) {
		String result = "";
		int i; int k;
		for (i=0, k=0; i<str.length(); i++, k++) {
			if (k>=key.length()) { k =0; }
			result += enc(str.charAt(i), key.charAt(k));
		}
		return result;
	}
	
	public String decrypt (String str) {
		String result = "";
		int i; int k;
		for (i=0, k=0; i<str.length(); i++, k++) {
			if (k>=key.length()) { k = 0; }
			result += dec(str.charAt(i), key.charAt(k));
		}
		return result;
	}
	
	private char enc (char t, char k) {
		int c;
		c = ((int)k+(int)t>255) ? k+t-255 : k+t;
		c = c^(int)k;
		return (char) c;
	}
	private char dec (char t, char k) {
		int c;
		c = (int)k^(int)t;
		return (char) ((c-(int)k<0) ? c-(int)k+255 : c-(int)k);
	}
}
