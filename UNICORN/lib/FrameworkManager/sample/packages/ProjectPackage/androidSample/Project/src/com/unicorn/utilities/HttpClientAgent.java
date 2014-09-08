package com.unicorn.utilities;

import java.io.IOException;
import java.util.Locale;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpUriRequest;
import org.apache.http.entity.BufferedHttpEntity;
import org.apache.http.impl.client.DefaultHttpClient;

import android.content.Context;

public class HttpClientAgent {

	protected HttpClientAgent() {
	}

	public static BufferedHttpEntity getBufferedHttpEntity(Context context, String url) {
		HttpUriRequest httpRequest = new HttpGet(url);
		DefaultHttpClient httpclient = new DefaultHttpClient();
		// UserAgent追加
		httpRequest.addHeader("User-Agent", AsyncHttpClientAgent.getUserAgent(context));
		httpRequest.addHeader("Accept-Language", Locale.getDefault().getLanguage());
		// クッキー追加
		AsyncHttpClientAgent.loadCookies(context, httpclient);
		HttpResponse httpResponse;
		BufferedHttpEntity bufHttpEntity = null;
		try {
			httpResponse = httpclient.execute(httpRequest);
			HttpEntity httpEntity = httpResponse.getEntity();
			bufHttpEntity = new BufferedHttpEntity(httpEntity);
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return bufHttpEntity;
	}

}
