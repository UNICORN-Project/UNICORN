package com.unicorn.utilities;

import java.io.UnsupportedEncodingException;
import java.nio.charset.Charset;
import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.text.DateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.TimeZone;
import java.util.Map.Entry;

import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;

import org.apache.http.Header;
import org.apache.http.HttpEntity;
import org.apache.http.client.CookieStore;
import org.apache.http.cookie.Cookie;
import org.apache.http.entity.ByteArrayEntity;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.mime.MultipartEntityBuilder;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.impl.cookie.BasicClientCookie;
import org.apache.http.message.BasicHeader;
import org.json.JSONArray;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.preference.PreferenceManager;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.JsonHttpResponseHandler;
import com.loopj.android.http.RequestParams;
import com.unicorn.project.Constant;

public class AsyncHttpClientAgent {
	private static final String TAG = AsyncHttpClientAgent.class.getSimpleName();

	private static final String mEncPNumber = "cyencpn";
	private static final String mEncddata = "cyencdd";

	public static void post(final Context context, final String url, RequestParams params,
			final JsonHttpResponseHandler responseHandler) {
		if (params == null) {
			params = new RequestParams();
		}
		final RequestParams finalParams = params;

		final AsyncHttpClient asyncHttpClient = new AsyncHttpClient();

		CookieStore cookieStore = AsyncHttpClientAgent.createToken(context, asyncHttpClient
				.getHttpClient());
		asyncHttpClient.setCookieStore(cookieStore);
		asyncHttpClient.setUserAgent(getUserAgent(context));
		asyncHttpClient.addHeader("Content-Type", "text/html; charset=UTF-8");
		asyncHttpClient.post(url, finalParams, new JsonHttpResponseHandler() {
			@Override
			public void onSuccess(JSONObject response) {
				responseHandler.onSuccess(response);
			}

			@Override
			public void onSuccess(JSONArray response) {
				responseHandler.onSuccess(response);
			}

			@Override
			public void onFailure(Throwable e, JSONObject errorResponse) {
				responseHandler.onFailure(e, errorResponse);

			}

			@Override
			public void onFailure(Throwable e, JSONArray errorResponse) {
				responseHandler.onFailure(e, errorResponse);
			}

			@Override
			public void onFailure(Throwable e, String errorResponse) {
				responseHandler.onFailure(e, errorResponse);
			}
		});
	}

	public static void postBinary(final Context context, final String url, HashMap<String,Object> argSaveParams,
			byte[] argUploadData, String argUploadDataName, String argUploadDataContentType,
			String argUploadDataKey, final JsonHttpResponseHandler responseHandler) {

		final AsyncHttpClient asyncHttpClient = new AsyncHttpClient();
		CookieStore cookieStore = AsyncHttpClientAgent.createToken(context, asyncHttpClient
				.getHttpClient());
		asyncHttpClient.setCookieStore(cookieStore);
		asyncHttpClient.setUserAgent(getUserAgent(context));
		MultipartEntityBuilder builder = MultipartEntityBuilder.create();        
		if (argSaveParams != null) {
			for (Iterator<Entry<String, Object>> it = argSaveParams.entrySet().iterator(); it
					.hasNext();) {
				HashMap.Entry<String, Object> entry = (HashMap.Entry<String, Object>) it.next();
				Object key = entry.getKey();
				Object value = entry.getValue();
				if (value instanceof String) {
					builder.addTextBody((String) key, (String) value,ContentType.create("text/plain", Charset.forName("UTF-8")));
				}
			}
		}
		builder.addBinaryBody(argUploadDataKey, argUploadData, ContentType.create(argUploadDataContentType), argUploadDataName);
		
		HttpEntity entity = builder.build();
		String contentType = null;
		asyncHttpClient.post(context, url, null, entity, contentType,
				new JsonHttpResponseHandler() {
					@Override
					public void onSuccess(JSONObject response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onSuccess(JSONArray response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onFailure(Throwable e, JSONObject errorResponse) {
						responseHandler.onFailure(e, errorResponse);

					}

					@Override
					public void onFailure(Throwable e, JSONArray errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}

					@Override
					public void onFailure(Throwable e, String errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}
				});
	}

	public static void putBinary(Context context, String url, HashMap<String,Object> argSaveParams,
			byte[] argUploadData, String argUploadDataName, String argUploadDataContentType,
			String argUploadDataKey, final JsonHttpResponseHandler responseHandler) {

		final AsyncHttpClient asyncHttpClient = new AsyncHttpClient();
		CookieStore cookieStore = AsyncHttpClientAgent.createToken(context, asyncHttpClient
				.getHttpClient());
		asyncHttpClient.setCookieStore(cookieStore);
		asyncHttpClient.setUserAgent(getUserAgent(context));
		Header[] headers = { new BasicHeader("Content-Type", "application/octet-stream"), };

		ByteArrayEntity ben = new ByteArrayEntity(argUploadData);
		String contentType = null;
		asyncHttpClient.put(context, url, headers, ben, contentType,
				new JsonHttpResponseHandler() {
					@Override
					public void onSuccess(JSONObject response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onSuccess(JSONArray response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onFailure(Throwable e, JSONObject errorResponse) {
						responseHandler.onFailure(e, errorResponse);

					}

					@Override
					public void onFailure(Throwable e, JSONArray errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}

					@Override
					public void onFailure(Throwable e, String errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}
				});
	}
	
	public static void putBinary(Context context, String url,
			byte[] argUploadData, final JsonHttpResponseHandler responseHandler) {

		final AsyncHttpClient asyncHttpClient = new AsyncHttpClient();
		CookieStore cookieStore = AsyncHttpClientAgent.createToken(context, asyncHttpClient
				.getHttpClient());
		asyncHttpClient.setCookieStore(cookieStore);
		asyncHttpClient.setUserAgent(getUserAgent(context));
		Header[] headers = { new BasicHeader("Content-Type", "application/octet-stream"), };

		ByteArrayEntity ben = new ByteArrayEntity(argUploadData);
		String contentType = null;
		asyncHttpClient.put(context, url, headers, ben, contentType,
				new JsonHttpResponseHandler() {
					@Override
					public void onSuccess(JSONObject response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onSuccess(JSONArray response) {
						responseHandler.onSuccess(response);
					}

					@Override
					public void onFailure(Throwable e, JSONObject errorResponse) {
						responseHandler.onFailure(e, errorResponse);

					}

					@Override
					public void onFailure(Throwable e, JSONArray errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}

					@Override
					public void onFailure(Throwable e, String errorResponse) {
						responseHandler.onFailure(e, errorResponse);
					}
				});
	}

	public static void get(final Context context, final String url, RequestParams params,
			final JsonHttpResponseHandler responseHandler) {

		if (params == null) {
			params = new RequestParams();
		}

		final RequestParams finalParams = params;

		final AsyncHttpClient asyncHttpClient = new AsyncHttpClient();

		CookieStore cookieStore = AsyncHttpClientAgent.createToken(context, asyncHttpClient
				.getHttpClient());
		asyncHttpClient.setCookieStore(cookieStore);
		asyncHttpClient.setUserAgent(getUserAgent(context));
		asyncHttpClient.addHeader("Content-Type", "text/html; charset=UTF-8");
		asyncHttpClient.post(url, finalParams, new JsonHttpResponseHandler() {
			@Override
			public void onSuccess(JSONObject response) {
				responseHandler.onSuccess(response);
			}

			@Override
			public void onSuccess(JSONArray response) {
				responseHandler.onSuccess(response);
			}

			@Override
			public void onFailure(Throwable e, JSONObject errorResponse) {
				responseHandler.onFailure(e, errorResponse);

			}

			@Override
			public void onFailure(Throwable e, JSONArray errorResponse) {
				responseHandler.onFailure(e, errorResponse);
			}

			@Override
			public void onFailure(Throwable e, String errorResponse) {
				responseHandler.onFailure(e, errorResponse);
			}
		});
	}

	public static CookieStore loadCookies(Context context) {
		AsyncHttpClient asyncHttpClient = new AsyncHttpClient();
		return AsyncHttpClientAgent.loadCookies(context, asyncHttpClient.getHttpClient());
	}

	public static CookieStore loadCookies(Context context, DefaultHttpClient defaultHttpClient) {

		boolean loginCookieEnabled = false;

		CookieStore myCookieStore = defaultHttpClient.getCookieStore();

		List<Cookie> cookies = myCookieStore.getCookies();

		for (Cookie cookie : cookies) {
			if (cookie.getDomain().equals(Constant.DOMAIN_NAME) && cookie.getName().equals("token")) {
				loginCookieEnabled = true;
			}
		}

		if (!loginCookieEnabled) {
			String token = getLocalToken(context);
			BasicClientCookie newCookie = new BasicClientCookie("token", token);
			newCookie.setVersion(0);
			newCookie.setDomain(Constant.DOMAIN_NAME);
			newCookie.setPath("/");
			myCookieStore.addCookie(newCookie);
			loginCookieEnabled = true;
		}

		if (!loginCookieEnabled) {
			return AsyncHttpClientAgent.createToken(context, defaultHttpClient);
		}

		return myCookieStore;
	}

	public static void setCookieInviteCode(String invitecode) {
		AsyncHttpClient asyncHttpClient = new AsyncHttpClient();
		CookieStore myCookieStore = asyncHttpClient.getHttpClient().getCookieStore();
		BasicClientCookie newCookie = new BasicClientCookie("invitecode", invitecode);
		newCookie.setVersion(0);
		newCookie.setDomain(Constant.DOMAIN_NAME);
		newCookie.setPath("/");
		myCookieStore.addCookie(newCookie);
	}

	private static CookieStore createToken(Context context, DefaultHttpClient httpClient) {
		CookieStore myCookieStore = httpClient.getCookieStore();

		List<Cookie> cookies = myCookieStore.getCookies();

		for (Cookie cookie : cookies) {
			if (cookie.getDomain().equals(Constant.DOMAIN_NAME) && cookie.getName().equals("token")) {
				myCookieStore.clearExpired(cookie.getExpiryDate());
			}
		}

		Date date = new Date();
		DateFormat df = new java.text.SimpleDateFormat("yyyyMMddHHmmss");
		df.setTimeZone(TimeZone.getTimeZone("GMT"));
		String gmtstringdata = df.format(date);

		String identifier = AuthAgent.getInstance().getData(context, AuthAgent.AUTH_UUID);
		identifier = "323d323dgfsgsfghjuyt323dgfsgsfghjuyt";
		String encIdentifier = null;
		String encDDString = null;

		try {
			identifier = AESCipher.encryptPKCS7PaddingUTF8(identifier);
			encIdentifier = AESCipher.encryptPKCS7PaddingUTF8(identifier + gmtstringdata);
			encDDString = AESCipher.encryptPKCS7PaddingUTF8(gmtstringdata);
		} catch (InvalidKeyException e) {
			e.printStackTrace();
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (NoSuchAlgorithmException e) {
			e.printStackTrace();
		} catch (NoSuchPaddingException e) {
			e.printStackTrace();
		} catch (InvalidAlgorithmParameterException e) {
			e.printStackTrace();
		} catch (IllegalBlockSizeException e) {
			e.printStackTrace();
		} catch (BadPaddingException e) {
			e.printStackTrace();
		}

		if (null != encIdentifier && null != encDDString) {
			String token = encIdentifier + gmtstringdata;
			BasicClientCookie newCookie = new BasicClientCookie("token", token);
			newCookie.setVersion(0);
			newCookie.setDomain(Constant.DOMAIN_NAME);
			newCookie.setPath("/");
			myCookieStore.addCookie(newCookie);

			// tokenをSharedPreferencesに保存
			saveLocalToken(context, encIdentifier, encDDString, token);
		}

		return myCookieStore;
	}

	public static String getUserAgent(Context context) {
		String versionName = "";
		PackageManager pm = context.getPackageManager();
		try {
			PackageInfo info = null;
			info = pm.getPackageInfo(context.getPackageName(), 0);
			versionName = info.versionName;
		} catch (NameNotFoundException e) {
			e.printStackTrace();
		}

		StringBuilder sb = new StringBuilder();
		sb.append("UnicornProject/");
		sb.append(versionName);
		sb.append(" Android");

		return sb.toString();
	}

	private static String getLocalToken(Context context) {
		String token = AuthAgent.getInstance().getToken();
		if (null == token) {
			SharedPreferences shre = PreferenceManager.getDefaultSharedPreferences(context);
			String pn = shre.getString(mEncPNumber, "");
			String dd = shre.getString(mEncddata, "");
			if (!pn.equals("") && !dd.equals("")) {
				try {
					String decodDdString = AESCipher.decryptPKCS7PaddingUTF8(dd);
					token = pn + decodDdString;
				} catch (InvalidKeyException e1) {
					e1.printStackTrace();
				} catch (UnsupportedEncodingException e1) {
					e1.printStackTrace();
				} catch (NoSuchAlgorithmException e1) {
					e1.printStackTrace();
				} catch (NoSuchPaddingException e1) {
					e1.printStackTrace();
				} catch (InvalidAlgorithmParameterException e1) {
					e1.printStackTrace();
				} catch (IllegalBlockSizeException e1) {
					e1.printStackTrace();
				} catch (BadPaddingException e1) {
					e1.printStackTrace();
				}
			}
		}
		return token;
	}

	private static void saveLocalToken(Context context, String encpn, String encdd, String token) {
		SharedPreferences shre = PreferenceManager.getDefaultSharedPreferences(context);
		Editor edit = shre.edit();
		edit.putString(mEncPNumber, encpn);
		edit.putString(mEncddata, encdd);
		edit.commit();

		// メモリ上で一時保管
		AuthAgent.getInstance().setToken(token);

	}
}
