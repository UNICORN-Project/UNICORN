package com.unicorn.model;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.loopj.android.http.JsonHttpResponseHandler;
import com.loopj.android.http.RequestParams;
import com.unicorn.project.Constant;
import com.unicorn.project.R;
import com.unicorn.utilities.AsyncHttpClientAgent;
import com.unicorn.utilities.Log;
import com.unicorn.utilities.PublicFunction;

import android.app.Activity;
import android.content.Context;
import android.os.Handler;
import android.os.Message;

public class ModelBase {

	public enum loadResourceMode {
		myResource, listedResource, automaticResource,
	};

	public static String TAG = "ModelBase";
	public Context context;

	public String protocol;
	public String domain;
	public String urlbase;
	public String cryptKey;
	public String cryptIV;
	public int timeout;
	public String tokenKeyName;
	public String modelName;
	public String ID;
	public String myResourcePrefix;
	public int index;
	public int total;
	public ArrayList<HashMap<String, Object>> responseList;
	// 通信に関する変数
	public boolean replaced;
	public boolean requested;
	public int statusCode;
	// Blockでハンドラを受け取るバージョンの為に用意
	public Handler completionHandler;
	public Handler modelBaseHandler;

	//コンストラクタ
	public ModelBase(Context argContext) {
		context = argContext;
		protocol = "";
		domain = "";
		urlbase = "";
		cryptKey = "";
		cryptIV = "";
		timeout = 10;
		tokenKeyName = "";
		modelName = "";
		ID = null;
		myResourcePrefix = "me/";
		index = 0;
		total = 0;
		responseList = new ArrayList<HashMap<String, Object>>();
		replaced = false;
		requested = false;
		statusCode = 0;
		// Blockでハンドラを受け取るバージョンの為に用意
		completionHandler = null;
	}

	//コンストラクタ
	public ModelBase(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
	}

	//コンストラクタ
	public ModelBase(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, int argTimeout) {
		this(argContext);
		protocol = argProtocol;
		domain = argDomain;
		urlbase = argURLBase;
		tokenKeyName = argTokenKeyName;
		timeout = argTimeout;
	}

	//コンストラクタ
	public ModelBase(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName);
		cryptKey = argCryptKey;
		cryptIV = argCryptIV;
	}

	//コンストラクタ
	public ModelBase(Context argContext, String argProtocol, String argDomain, String argURLBase,
			String argTokenKeyName, String argCryptKey, String argCryptIV, int argTimeout) {
		this(argContext, argProtocol, argDomain, argURLBase, argTokenKeyName, argCryptKey,
				argCryptIV);
		timeout = argTimeout;
	}

	/* RESTfulURLの生成 */
	public String createURLString(String resourceId) {
		String url = "";
		if (null != resourceId) {
			// 更新(Put)
			url = String.format("%s://%s%s%s%s/%s.json", protocol, domain, urlbase,
					myResourcePrefix, modelName, resourceId);
		} else {
			// 新規(POST)
			url = String.format("%s://%s%s%s%s.json", protocol, domain, urlbase, myResourcePrefix,
					modelName);
		}
		return url;
	}

	// モデルを参照する
	public boolean load() {
		if (null == ID || "".equals(ID)) {
			// ID無指定は単一モデル参照エラー
			return false;
		}
		return load(loadResourceMode.myResource, null);
	}

	// モデルを参照する
	public boolean load(Handler argCompletionHandler) {
		if (null == ID) {
			// ID無指定は単一モデル参照エラー
			return false;
		}
		completionHandler = argCompletionHandler;
		return load(loadResourceMode.myResource, null);
	}

	// モデルを参照する
	public boolean list() {
		return load(loadResourceMode.listedResource, null);
	}
	// モデルを参照する
	public boolean list(Handler argCompletionHandler) {
		completionHandler = argCompletionHandler;
		return load(loadResourceMode.listedResource, null);
	}

	//条件を指定してモデルを参照する
	public boolean query(HashMap<String, Object> argWhereParams) {
		return load(loadResourceMode.automaticResource, argWhereParams);
	}

	//条件を指定してモデルを参照する
	public boolean query(HashMap<String, Object> argWhereParams, Handler argCompletionHandler) {
		completionHandler = argCompletionHandler;
		return load(loadResourceMode.automaticResource, argWhereParams);
	}

	public boolean save() {
		completionHandler = null;
		return true;
	}

	public boolean save(Handler argCompletionHandler) {
		completionHandler = argCompletionHandler;
		return true;
	}

	public boolean save(HashMap<String, Object> argSaveParams, byte[] argUploadData,
			String argUploadDataName, String argUploadDataContentType, String argUploadDataKey) {

		String url = createURLString(ID);

		if (ID != null) {
			// 更新(Put)

			AsyncHttpClientAgent.putBinary(context, url, argSaveParams, argUploadData,
					argUploadDataName, argUploadDataContentType, argUploadDataKey,
					new JsonHttpResponseHandler() {
						@Override
						public void onSuccess(JSONObject response) {
							Log.v(TAG, "post->onSuccessJsonObject");
							try {
								responseList.add(createMapFromJSONObject(response));
								Log.v(TAG, "post->onSuccessJsonObject->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onSuccess(JSONArray response) {
							Log.v(TAG, "post->onSuccessJsonArray");
							try {
								responseList = createArrayFromJSONArray(response);
								Log.v(TAG, "post->onSuccessJsonArray->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onFailure(Throwable e, JSONObject errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, JSONArray errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, String errorResponse) {
							Log.d(TAG + " error", errorResponse);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = errorResponse;
							returnMainTheread(msg);
						}
					});
		} else {
			// 更新(Post)

			AsyncHttpClientAgent.postBinary(context, url, argSaveParams, argUploadData,
					argUploadDataName, argUploadDataContentType, argUploadDataKey,
					new JsonHttpResponseHandler() {
						@Override
						public void onSuccess(JSONObject response) {
							Log.v(TAG, "post->onSuccessJsonObject");
							try {
								responseList.add(createMapFromJSONObject(response));
								Log.v(TAG, "post->onSuccessJsonObject->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onSuccess(JSONArray response) {
							Log.v(TAG, "post->onSuccessJsonArray");
							try {
								responseList = createArrayFromJSONArray(response);
								Log.v(TAG, "post->onSuccessJsonArray->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onFailure(Throwable e, JSONObject errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, JSONArray errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, String errorResponse) {
							Log.d(TAG + " error", errorResponse);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = errorResponse;
							returnMainTheread(msg);
						}
					});
		}
		return true;
	}

	/* ファイルを一つのモデルリソースと見立てて保存(アップロード)する */
	/* PUTメソッドでのアップロード処理を強制します！ */
	public boolean _save(byte[] argUploadData) {
		String url = createURLString(ID);

		if (null != ID) {
			// 更新(Put)

			AsyncHttpClientAgent.putBinary(context, url, argUploadData,
					new JsonHttpResponseHandler() {
						@Override
						public void onSuccess(JSONObject response) {
							Log.v(TAG, "post->onSuccessJsonObject");
							try {
								responseList.add(createMapFromJSONObject(response));
								Log.v(TAG, "post->onSuccessJsonObject->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onSuccess(JSONArray response) {
							Log.v(TAG, "post->onSuccessJsonArray");
							try {
								responseList = createArrayFromJSONArray(response);
								Log.v(TAG, "post->onSuccessJsonArray->pauseSuccess");
								Message msg = new Message();
								msg.arg1 = Constant.RESULT_OK;
								msg.obj = response;
								returnMainTheread(msg);
							} catch (JSONException e) {
								e.printStackTrace();
							}
						}

						@Override
						public void onFailure(Throwable e, JSONObject errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, JSONArray errorResponse) {
							String error = e.toString();
							Log.d(TAG + " error", error);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = error;
							returnMainTheread(msg);
						}

						@Override
						public void onFailure(Throwable e, String errorResponse) {
							Log.d(TAG + " error", errorResponse);
							Message msg = new Message();
							msg.arg1 = Constant.RESULT_FAILED;
							msg.obj = errorResponse;
							returnMainTheread(msg);
						}
					});
		} else {
			// XXX ID無しのファイルアップロードは出来ない！
			return false;
		}
		return false;
	}

	public boolean save(HashMap<String, Object> argsaveParams) {

		String url = createURLString(ID);

		if (ID != null) {
			// 更新(Put)
			RequestParams requestParam = new RequestParams();

			for (Iterator<Entry<String, Object>> it = argsaveParams.entrySet().iterator(); it
					.hasNext();) {
				HashMap.Entry<String, Object> entry = (HashMap.Entry<String, Object>) it.next();
				Object key = entry.getKey();
				Object value = entry.getValue();
				if (value instanceof String) {
					requestParam.put((String) key, (String) value);
				}
			}

			AsyncHttpClientAgent.post(context, url, requestParam, new JsonHttpResponseHandler() {
				@Override
				public void onSuccess(JSONObject response) {
					Log.v(TAG, "post->onSuccessJsonObject");
					try {
						responseList.add(createMapFromJSONObject(response));
						Log.v(TAG, "post->onSuccessJsonObject->pauseSuccess");
						Message msg = new Message();
						msg.arg1 = Constant.RESULT_OK;
						msg.obj = response;
						returnMainTheread(msg);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}

				@Override
				public void onSuccess(JSONArray response) {
					Log.v(TAG, "post->onSuccessJsonArray");
					try {
						responseList = createArrayFromJSONArray(response);
						Log.v(TAG, "post->onSuccessJsonArray->pauseSuccess");
						Message msg = new Message();
						msg.arg1 = Constant.RESULT_OK;
						msg.obj = response;
						returnMainTheread(msg);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}

				@Override
				public void onFailure(Throwable e, JSONObject errorResponse) {
					String error = e.toString();
					Log.d(TAG + " error", error);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = error;
					returnMainTheread(msg);
				}

				@Override
				public void onFailure(Throwable e, JSONArray errorResponse) {
					String error = e.toString();
					Log.d(TAG + " error", error);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = error;
					returnMainTheread(msg);
				}

				@Override
				public void onFailure(Throwable e, String errorResponse) {
					Log.d(TAG + " error", errorResponse);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = errorResponse;
					returnMainTheread(msg);
				}
			});
		} else {
			// 新規(POST)
			RequestParams requestParam = new RequestParams();

			for (Iterator<Entry<String, Object>> it = argsaveParams.entrySet().iterator(); it
					.hasNext();) {
				HashMap.Entry<String, Object> entry = (HashMap.Entry<String, Object>) it.next();
				Object key = entry.getKey();
				Object value = entry.getValue();
				if (value instanceof String) {
					requestParam.put((String) key, (String) value);
				}
			}

			AsyncHttpClientAgent.post(context, url, requestParam, new JsonHttpResponseHandler() {
				@Override
				public void onSuccess(JSONObject response) {
					Log.v(TAG, "post->onSuccessJsonObject");
					try {
						responseList.add(createMapFromJSONObject(response));
						Log.v(TAG, "post->onSuccessJsonObject->pauseSuccess");
						Message msg = new Message();
						msg.arg1 = Constant.RESULT_OK;
						msg.obj = response;
						returnMainTheread(msg);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}

				@Override
				public void onSuccess(JSONArray response) {
					Log.v(TAG, "post->onSuccessJsonArray");
					try {
						responseList = createArrayFromJSONArray(response);
						Log.v(TAG, "post->onSuccessJsonArray->pauseSuccess");
						Message msg = new Message();
						msg.arg1 = Constant.RESULT_OK;
						msg.obj = response;
						returnMainTheread(msg);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}

				@Override
				public void onFailure(Throwable e, JSONObject errorResponse) {
					String error = e.toString();
					Log.d(TAG + " error", error);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = error;
					returnMainTheread(msg);
				}

				@Override
				public void onFailure(Throwable e, JSONArray errorResponse) {
					String error = e.toString();
					Log.d(TAG + " error", error);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = error;
					returnMainTheread(msg);
				}

				@Override
				public void onFailure(Throwable e, String errorResponse) {
					Log.d(TAG + " error", errorResponse);
					Message msg = new Message();
					msg.arg1 = Constant.RESULT_FAILED;
					msg.obj = errorResponse;
					returnMainTheread(msg);
				}
			});
		}
		return false;
	}

	//argsaveParamsを元にsaveのURLを生成する
	public String createGetURl(String url, HashMap<String, Object> argsaveParams) {
		for (Iterator<Entry<String, Object>> it = argsaveParams.entrySet().iterator(); it.hasNext();) {
			HashMap.Entry<String, Object> entry = (HashMap.Entry<String, Object>) it.next();
			Object key = entry.getKey();
			Object value = entry.getValue();
			if (value instanceof String) {
				url = url + " " + (String) key + "=" + (String) value;
			}
		}
		return url;
	}

	public boolean load(loadResourceMode argLoadResourceMode, HashMap<String, Object> argWhereParams) {

		switch (argLoadResourceMode) {
		case myResource:
			_load(ID, null);
			break;
		case listedResource:
			_load(null, null);
			break;
		case automaticResource:
			_load(ID, argWhereParams);
			break;
		default:
			break;
		}
		return true;
	}

	//通信レスポンスデータを元にmodelにデータをセットする。
	//暫定で0番目を指定
	public void setModelData() {
		total = responseList.size();
		if (0 < total) {
			_setModelData(responseList.get(0));
		}
	}

	//0番目のHashMapを元にmodelにデータをセットする。
	//セット部分は各モデルで_setModelDataをOverrideして実装して下さい。
	public void setModelData(ArrayList<HashMap<String, Object>> list) {
		responseList = list;
		total = responseList.size();
		if (0 < total) {
			index = 0;
			_setModelData(responseList.get(0));
		}
	}

	//argIndex番目のHashMapを元にmodelにデータをセットする。
	//セット部分は各モデルで_setModelDataをOverrideして実装して下さい。
	public void setModelData(ArrayList<HashMap<String, Object>> list, int argIndex) {
		responseList = list;
		total = list.size();
		if (0 < total) {
			index = argIndex;
			_setModelData(list.get(index));
		}
	}

	public void _setModelData(HashMap<String, Object> map) {

	}

	public HashMap<String, Object> convertModelData() {
		return null;
	}

	public void _load(String resourceId, HashMap<String, Object> argWhereParams) {

		String url = createURLString(resourceId);
		if (argWhereParams != null) {
			url = createGetURl(url, argWhereParams);
		}
		AsyncHttpClientAgent.get(context, url, null, new JsonHttpResponseHandler() {
			@Override
			public void onSuccess(JSONObject response) {
				Log.v(TAG, "get->onSuccessJsonObject");
				try {
					responseList.add(createMapFromJSONObject(response));
					Log.v(TAG, "get->onSuccessJsonObject->pauseSuccess");
					setModelData();

					Message msg = new Message();
					msg.arg1 = Constant.RESULT_OK;
					msg.obj = response;
					returnMainTheread(msg);
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}

			@Override
			public void onSuccess(JSONArray response) {
				Log.v(TAG, "get->onSuccessJsonArray");
				try {
					responseList = createArrayFromJSONArray(response);
					Log.v(TAG, "get->onSuccessJsonArray->pauseSuccess");
					setModelData();

					Message msg = new Message();
					msg.arg1 = Constant.RESULT_OK;
					msg.obj = response;
					returnMainTheread(msg);
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}

			@Override
			public void onFailure(Throwable e, JSONObject errorResponse) {
				String error = e.toString();
				Log.d(TAG + " error", error);
				Message msg = new Message();
				msg.arg1 = Constant.RESULT_FAILED;
				msg.obj = error;
				returnMainTheread(msg);
			}

			@Override
			public void onFailure(Throwable e, JSONArray errorResponse) {
				String error = e.toString();
				Log.d(TAG + " error", error);
				Message msg = new Message();
				msg.arg1 = Constant.RESULT_FAILED;
				msg.obj = error;
				returnMainTheread(msg);
			}

			@Override
			public void onFailure(Throwable e, String errorResponse) {
				Log.d(TAG + " error", errorResponse);
				Message msg = new Message();
				msg.arg1 = Constant.RESULT_FAILED;
				msg.obj = errorResponse;
				returnMainTheread(msg);
			}
		});
	}

	//handlerがある場合mainスレッドに制御を戻す
	public void returnMainTheread(Message msg) {
		if (completionHandler != null) {
			completionHandler.sendMessage(msg);
			completionHandler = null;
		}
	}

	//JsonArrayをArrayListに変換
	public ArrayList<HashMap<String, Object>> createArrayFromJSONArray(JSONArray data)
			throws JSONException {
		ArrayList<HashMap<String, Object>> array = new ArrayList<HashMap<String, Object>>();
		for (int i = 0; i < data.length(); i++) {
			JSONObject jsonObject = data.getJSONObject(i);
			array.add(createMapFromJSONObject(jsonObject));
		}
		return array;
	}

	//JsonObjectをkey,valueでHashMapに変換
	public HashMap<String, Object> createMapFromJSONObject(JSONObject data) throws JSONException {
		HashMap<String, Object> map = new HashMap<String, Object>();
		Iterator<?> keys = data.keys();

		while (keys.hasNext()) {
			String key = (String) keys.next();
			if (data.get(key) instanceof JSONObject) {
				map.put(key, createMapFromJSONObject((JSONObject) data.get(key)));
			} else if (data.get(key) instanceof JSONArray) {
				map.put(key, createArrayFromJSONArray((JSONArray) data.get(key)));
			} else if (data.get(key) instanceof String) {
				map.put(key, data.get(key));
			}
		}
		return map;
	}
	
	/* 特殊なメソッド1 インクリメント(加算) */
	public boolean increment(){
	    return true;
	}
	
	public boolean _increment(HashMap<String,Object> argSaveParams){
	    if(null != ID){
	    	return save(argSaveParams);
	    }
	    // インクリメントはID指定ナシはエラー！
	    return false;
	}

	/* 特殊なメソッド2 デクリメント(減算) */
	public boolean decrement(){
	    return true;
	}

	public boolean _decrement(HashMap<String,Object> argSaveParams){
	    if(null != ID){
	        return save(argSaveParams);
	    }
	    // インクリメントはID指定ナシはエラー！
	    return false;
	}

	public boolean next() {
		if (index < responseList.size() - 1) {
			index++;
			setModelData(responseList, index);
			return true;
		}
		return false;
	}

	public ModelBase objectAtIndex(int argIndex) {
		ModelBase nextModel = null;
		if (0 < total && argIndex < responseList.size()) {
			nextModel = new ModelBase(context, protocol, domain, urlbase, tokenKeyName, cryptKey,
					cryptIV, timeout);
			nextModel.setModelData(responseList, argIndex);
		}
		return nextModel;
	}

	public void insertObject(ModelBase model, int argIndex) {
		HashMap<String, Object> response = model.convertModelData();
		responseList.add(argIndex, response);
		total = responseList.size();
	}

	public void replaceObject(ModelBase model, int argIndex) {
		HashMap<String, Object> response = model.convertModelData();
		responseList.remove(argIndex);
		responseList.add(argIndex, response);
		total = responseList.size();
	}

	public void removeObject(int argIndex) {
		responseList.remove(argIndex);
		total = responseList.size();
	}

	//activityに管理させる為、引数にactivityを追加
	public void showRequestError(int argStatusCode,Activity activity) {
		String errorMsg = context.getString(R.string.errorMsgTimeout);
		if (0 < argStatusCode) {
			errorMsg = context.getString(R.string.errorMsgServerError);
			if (400 == argStatusCode) {
				errorMsg = context.getString(R.string.errorMsg400);
			}
			if (401 == argStatusCode) {
				errorMsg = context.getString(R.string.errorMsg401);
			}
			if (404 == argStatusCode) {
				errorMsg = context.getString(R.string.errorMsg404);
			}
			if (503 == argStatusCode) {
				errorMsg = context.getString(R.string.errorMsg503);
			}
		}

		if (context != null) {
			PublicFunction.showAlert(context, errorMsg,activity);
		}
	}
}