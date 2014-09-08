package com.unicorn.model;

import java.util.HashMap;

import android.content.Context;
import android.os.Handler;

public class MovieModel extends ModelBase {

	public String thumbnail;
	public String url;
	
	public boolean thumbnail_replaced;
	public boolean url_replaced;

	public MovieModel(Context argContext) {
		super(argContext);
		modelName = "movie";
	}

	public MovieModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
	}

	public MovieModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, int argTimeout) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
		timeout = argTimeout;
	}

	public MovieModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName);
		cryptKey = argCryptKey;
		cryptIV = argCryptIV;
	}

	public MovieModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV, int argTimeout) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName, argCryptKey,
				argCryptIV);
		timeout = argTimeout;
	}

	public void setThumbnail(String argThumbnail) {
		thumbnail = argThumbnail;
		thumbnail_replaced = true;
		replaced = true;
	}

	public void setUrl(String argUrl) {
		url = argUrl;
		url_replaced = true;
		replaced = true;
	}

	public boolean load() {
		_load(null, null);
		return true;
	}

	public boolean load(Handler argCompletionHandler) {
		completionHandler = argCompletionHandler;
		_load(null, null);
		return true;
	}

	public boolean save(Handler argCompletionHandler) {
		super.save(argCompletionHandler);
		save();
		return true;
	}

	public boolean save() {
		HashMap<String, Object> argsaveParams = new HashMap<String, Object>();

		if (replaced) {
			if (thumbnail_replaced) {
				argsaveParams.put("thumbnail", thumbnail);
			}
			if (url_replaced) {
				argsaveParams.put("url", url);
			}
		}

		super.save(argsaveParams);
		return true;
	}

	public void _setModelData(HashMap<String, Object> map) {
		ID = (String) map.get("id");
		thumbnail = (String) map.get("thumbnail");
		url = (String) map.get("url");

		resetReplaceFlagment();
	}

	public void resetReplaceFlagment() {
		thumbnail_replaced = false;
		url_replaced = false;
	}

	public HashMap<String, Object> convertModelData() {
		HashMap<String, Object> newMap = new HashMap<String, Object>();
		newMap.put("id", ID);
		newMap.put("thumbnail", thumbnail);
		newMap.put("url", url);
		return newMap;
	}

	/* ローカルに書きだした動画のThmubnailをTimelineモデルに保存する場合 */
	public boolean saveThumbnail(byte[] argUploadData,String argTimeLineID,Handler argCompletionHandler)
	{
	    if(null == ID){
	        ID = argTimeLineID + ".jpg";
	        completionHandler = argCompletionHandler;

	        return super._save(argUploadData);
	    }
	    // 異常終了
	    return false;
	}

	
	public boolean saveWithProfileImage(byte[] imageData, Handler argCompletionHandler) {
		
		HashMap<String, Object> argsaveParams = new HashMap<String, Object>();
		
		if (replaced) {
			if (thumbnail_replaced) {
				argsaveParams.put("thumbnail", thumbnail);
			}
			if (url_replaced) {
				argsaveParams.put("url", url);
			}
		}

		super.save(argsaveParams,imageData,"tmp.jpg","image/jpeg","image");
		return true;
	}
}