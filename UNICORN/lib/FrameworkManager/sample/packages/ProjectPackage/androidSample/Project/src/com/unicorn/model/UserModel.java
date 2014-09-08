package com.unicorn.model;

import java.util.HashMap;

import android.content.Context;
import android.os.Handler;

public class UserModel extends ModelBase {

	public String name;
	public String uniq_name;
	public String profile_image_url;
	public String created;
	public String modified;
	public String available;

	public boolean name_replaced;
	public boolean uniq_name_replaced;
	public boolean profile_image_url_replaced;
	public boolean created_replaced;
	public boolean modified_replaced;
	public boolean available_replaced;

	public UserModel(Context argContext) {
		super(argContext);
		modelName = "user";
	}

	public UserModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
	}

	public UserModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, int argTimeout) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
		timeout = argTimeout;
	}

	public UserModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName);
		cryptKey = argCryptKey;
		cryptIV = argCryptIV;
	}

	public UserModel(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV, int argTimeout) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName, argCryptKey,
				argCryptIV);
		timeout = argTimeout;
	}

	public void setName(String argName) {
		name = argName;
		name_replaced = true;
		replaced = true;
	}

	public void setUniq_name(String argUniq_name) {
		uniq_name = argUniq_name;
		uniq_name_replaced = true;
		replaced = true;
	}

	public void setProfile_image_url(String argProfile_image_url) {
		profile_image_url = argProfile_image_url;
		profile_image_url_replaced = true;
		replaced = true;
	}

	public void setCreated(String argCreated) {
		created = argCreated;
		created_replaced = true;
		replaced = true;
	}

	public void setModified(String argModified) {
		modified = argModified;
		modified_replaced = true;
		replaced = true;
	}

	public void setAvailable(String argAvailable) {
		available = argAvailable;
		available_replaced = true;
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
			if (name_replaced) {
				argsaveParams.put("name", name);
			}
			if (uniq_name_replaced) {
				argsaveParams.put("uniq_name", uniq_name);
			}
			if (profile_image_url_replaced) {
				argsaveParams.put("profile_image_url", profile_image_url);
			}
			if (created_replaced) {
				argsaveParams.put("created", created);
			}
			if (modified_replaced) {
				argsaveParams.put("modified", modified);
			}
			if (available_replaced) {
				argsaveParams.put("available", available);
			}
		}

		super.save(argsaveParams);
		return true;
	}

	public void _setModelData(HashMap<String, Object> map) {
		ID = (String) map.get("id");
		name = (String) map.get("name");
		uniq_name = (String) map.get("uniq_name");
		profile_image_url = (String) map.get("profile_image_url");
		created = (String) map.get("created");
		modified = (String) map.get("modified");
		available = (String) map.get("available");

		resetReplaceFlagment();
	}

	public void resetReplaceFlagment() {
		name_replaced = false;
		uniq_name_replaced = false;
		profile_image_url_replaced = false;
		created_replaced = false;
		modified_replaced = false;
		available_replaced = false;
	}

	public HashMap<String, Object> convertModelData() {
		HashMap<String, Object> newMap = new HashMap<String, Object>();
		newMap.put("id", ID);
		newMap.put("uniq_name", uniq_name);
		newMap.put("profile_image_url", profile_image_url);
		newMap.put("created", created);
		newMap.put("modified", modified);
		newMap.put("available", available);
		return newMap;
	}

	public boolean saveWithProfileImage(byte[] imageData, Handler argCompletionHandler) {
		
		HashMap<String, Object> argsaveParams = new HashMap<String, Object>();
		
		if (replaced) {
			if (name_replaced) {
				argsaveParams.put("name", name);
			}
			if (uniq_name_replaced) {
				argsaveParams.put("uniq_name", uniq_name);
			}
			if (profile_image_url_replaced) {
				argsaveParams.put("profile_image_url", profile_image_url);
			}
			if (created_replaced) {
				argsaveParams.put("created", created);
			}
			if (modified_replaced) {
				argsaveParams.put("modified", modified);
			}
			if (available_replaced) {
				argsaveParams.put("available", available);
			}
		}

		super.save(argsaveParams,imageData,"tmp.jpg","image/jpeg","image");
		return true;
	}

}