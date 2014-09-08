package com.unicorn.utilities;

import java.util.HashMap;

import com.unicorn.project.Constant;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;

public class AuthAgent {
	public static final AuthAgent mAuthAgent = new AuthAgent();

	public AuthAgent() {
	}

	public static AuthAgent getInstance() {
		return mAuthAgent;
	}

	public static String AUTH_UUID = "UUID";
	public static String AUTH_RegistrationID = "RegistrationID";
	public static String AUTH_loginedUserID = "loginedUserID";
	public static String AUTH_loginedUserName = "loginedUserName";
	public static String AUTH_loginedUserFirstName = "loginedUserFirstName";
	public static String AUTH_loginedUserLastName = "loginedUserLastName";
	public static String AUTH_loginedUserProfileImageURL = "loginedUserProfileImageURL";
	public static String AUTH_userID = "userID";
	public static String AUTH_IDSearchedAllow = "IDSearchedAllow";
	public static String AUTH_Telephone = "telephone";
	public static String AUTH_CountryCode = "countryCode";
	public static String AUTH_Mailaddress = "mailaddress";
	public static String AUTH_Birthday = "auth_birthday";
	public static String AUTH_Gender = "auth_gender";
	public static String AUTH_New = "new";

	private static String[] removeType = { "RegistrationID", "loginedUserID", "loginedUserName",
			"loginedUserFirstName", "loginedUserLastName", "loginedUserProfileImageURL", "userID",
			"IDSearchedAllow", "telephone", "countryCode", "mailaddress", "auth_birthday",
			"auth_gender", "new" };

	// tokenの一時保管場所・・
	private String AUTH_Token = null;

	private SharedPreferences mPref = null;
	private SharedPreferences.Editor mEditor;
	private HashMap<String, String> mUserData = new HashMap<String, String>();

	/**
	 * ユーザデータを取得する
	 * 
	 * @param context
	 *            コンテキスト
	 * @param type
	 *            取得したいデータのタイプを設定すること。AuthAgent.AUTH_・・のうち、どれかを選択すること
	 * @return 指定したデータをStringで返す。値がない場合はnullを返す
	 */
	public String getData(Context context, String type) {

		if (null == mUserData.get(type)) {
			if (null == mPref) {
				mPref = context.getSharedPreferences(Constant.SHAREPREF_KEY, Activity.MODE_PRIVATE);
			}
			mUserData.put(type, mPref.getString(type, ""));
		}

		if (mUserData.get(type).equals("")) {
			return null;
		} else {
			return mUserData.get(type);
		}
	}

	/**
	 * ユーザデータを設定する
	 * 
	 * @param context
	 *            コンテキスト
	 * @param type
	 *            設定したいデータのタイプを設定すること。AuthAgent.AUTH_・・のうち、どれかを選択すること
	 * @param arg
	 *            設定したい値
	 */
	public void setData(Context context, String type, String arg) {
		if (null == type) {
			// nullだったら何もせずにreturn
			return;
		}

		if (null == mPref) {
			mPref = context.getSharedPreferences(Constant.SHAREPREF_KEY, Activity.MODE_PRIVATE);
		}
		if (null != arg) {
			mUserData.put(type, arg);
		} else {
			if (mUserData.containsKey(type)) {
				mUserData.remove(type);
			}
		}

		mEditor = mPref.edit();
		if (null != arg) {
			mEditor.putString(type, arg);
		} else {
			mEditor.remove(type);
		}
		mEditor.commit();

	}

	public void removeAllData(Context context) {
		if (null == mPref) {
			mPref = context.getSharedPreferences(Constant.SHAREPREF_KEY, Activity.MODE_PRIVATE);
		}

		for (String type : removeType) {
			if (mUserData.containsKey(type)) {
				mUserData.remove(type);
			}

			mEditor = mPref.edit();
			mEditor.remove(type);
			mEditor.commit();
		}
	}

	public void setToken(String token) {
		AUTH_Token = token;
	}

	public String getToken() {
		return AUTH_Token;
	}
}
