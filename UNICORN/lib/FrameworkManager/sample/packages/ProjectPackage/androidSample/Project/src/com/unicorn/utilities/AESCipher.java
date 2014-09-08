package com.unicorn.utilities;

import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;

import javax.crypto.BadPaddingException;
import javax.crypto.Cipher;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

import com.unicorn.project.Constant;

public class AESCipher {

	public static String encryptPKCS7PaddingUTF8(String text)
			throws java.io.UnsupportedEncodingException, NoSuchAlgorithmException,
			NoSuchPaddingException, InvalidKeyException, InvalidAlgorithmParameterException,
			IllegalBlockSizeException, BadPaddingException {

		return encrypt(text.getBytes("UTF-8"), "AES/CBC/PKCS7Padding");
	}

	private static String encrypt(byte[] textBytes, String padding)
			throws java.io.UnsupportedEncodingException, NoSuchAlgorithmException,
			NoSuchPaddingException, InvalidKeyException, InvalidAlgorithmParameterException,
			IllegalBlockSizeException, BadPaddingException {

		IvParameterSpec ivSpec = new IvParameterSpec(
				PublicFunction.asByteArray(Constant.NETWORK_CRYPT_IV));
		SecretKeySpec newKey = new SecretKeySpec(Constant.NETWORK_CRYPT_KEY.getBytes("UTF-8"),
				"AES");
		Cipher cipher = null;
		cipher = Cipher.getInstance(padding);
		cipher.init(Cipher.ENCRYPT_MODE, newKey, ivSpec);
		byte[] tmp = cipher.doFinal(textBytes);
		return PublicFunction.byteArrayToHexString(tmp);
	}

	public static String decryptPKCS7PaddingUTF8(String text)
			throws java.io.UnsupportedEncodingException, NoSuchAlgorithmException,
			NoSuchPaddingException, InvalidKeyException, InvalidAlgorithmParameterException,
			IllegalBlockSizeException, BadPaddingException {
		return decrypt(PublicFunction.hexStringToByteArray(text), "AES/CBC/PKCS7Padding");
	}

	public static String decryptZeroPaddingUTF8(String text)
			throws java.io.UnsupportedEncodingException, NoSuchAlgorithmException,
			NoSuchPaddingException, InvalidKeyException, InvalidAlgorithmParameterException,
			IllegalBlockSizeException, BadPaddingException {
		return decrypt(PublicFunction.hexStringToByteArray(text), "AES/CBC/ZeroBytePadding");
	}

	private static String decrypt(byte[] textBytes, String padding)
			throws java.io.UnsupportedEncodingException, NoSuchAlgorithmException,
			NoSuchPaddingException, InvalidKeyException, InvalidAlgorithmParameterException,
			IllegalBlockSizeException, BadPaddingException {

		IvParameterSpec ivSpec = new IvParameterSpec(
				PublicFunction.asByteArray(Constant.NETWORK_CRYPT_IV));
		SecretKeySpec newKey = new SecretKeySpec(Constant.NETWORK_CRYPT_KEY.getBytes("UTF-8"),
				"AES");
		Cipher cipher = Cipher.getInstance(padding);
		cipher.init(Cipher.DECRYPT_MODE, newKey, ivSpec);
		byte[] tmp = cipher.doFinal(textBytes);

		return new String(tmp, "UTF-8");

	}
}
