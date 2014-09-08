package com.unicorn.project;

import android.view.ViewGroup;

public class Constant {

	public static final boolean isDebug = false;
	public static final String NETWORK_CRYPT_KEY = "bdcc45fba7d9865d";
	public static final String NETWORK_CRYPT_IV = "ccfd810a95af4d90";

	public static final String PROTOCOL = "http";
	public static final String DOMAIN_NAME = "192.168.56.1";
	public static final String URL_BASE = "/workspace/Podeo/src/server/apidocs/";
	public static final String COOKIE_TOKEN_NAME = "token";
	public static final String SESSION_CRYPT_KEY = "bdcc45fba7d9865d";
	public static final String SESSION_CRYPT_IV = "ccfd810a95af4d90";

	public static final int WC = ViewGroup.LayoutParams.WRAP_CONTENT;
	public static final int MP = ViewGroup.LayoutParams.MATCH_PARENT;

	public static final int RESULT_OK = 1000;
	public static final int RESULT_FAILED = 1001;
	public static final int RESULT_CANCELED = 1002;

	public static final String SHAREPREF_KEY = "cp_utility";

}
