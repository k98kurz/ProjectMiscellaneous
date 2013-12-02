package com.noobgrinder.jonathan.rxcypher;

public class Test {
	public static void main (String args[]) throws Exception {
		String test = "Hello world! :D";
		String key = "L337_p4ssw0rd";
		String res;
		RXCypher cip = new RXCypher(key);
		System.out.println("String: \"" + test + "\"");
		System.out.println("Encryption Key: \"" + key + "\"");
		res = cip.encrypt(test);
		System.out.println("\nEncrypted: \"" + res + "\"");
		res = cip.decrypt(res);
		System.out.println("\nDecrypted: \"" + res + "\"");
	}
}
