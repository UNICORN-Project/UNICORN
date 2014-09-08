package com.unicorn.project;

import com.unicorn.utilities.Log;

import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.app.Activity;
import android.view.Menu;
import android.widget.ListView;

public class MainActivity extends Activity {

	public ListView listView;
//	UserModel userModel;
//	MovieModel movieModel;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		listView = (ListView)findViewById(R.id.main_list);
		
		Handler handler = new Handler(){
			public void handleMessage(Message msg) {
				Log.d("handler");
			}
		};
		
		
//		
//		userModel = new UserModel(this,Constant.PROTOCOL,Constant.DOMAIN_NAME,Constant.URL_BASE,Constant.COOKIE_TOKEN_NAME,Constant.SESSION_CRYPT_KEY,Constant.SESSION_CRYPT_IV);
//		Bitmap testBitmap = BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher);
//		ByteArrayOutputStream baos = new ByteArrayOutputStream();
//		testBitmap.compress(CompressFormat.JPEG, 100, baos);
//		byte[] imageData = baos.toByteArray();
//		userModel.setName("siosiosio");
//		userModel.saveWithProfileImage(imageData, handler);
//		Log.d("test","test");
//		
//		movieModel = new MovieModel(this,Constant.PROTOCOL,Constant.DOMAIN_NAME,Constant.URL_BASE,Constant.COOKIE_TOKEN_NAME,Constant.SESSION_CRYPT_KEY,Constant.SESSION_CRYPT_IV);
//		Bitmap testBitmap = BitmapFactory.decodeResource(getResources(), R.drawable.ic_launcher);
//		ByteArrayOutputStream baos = new ByteArrayOutputStream();
//		testBitmap.compress(CompressFormat.JPEG, 100, baos);
//		byte[] imageData = baos.toByteArray();
//		movieModel.saveThumbnail(imageData, "1", handler);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
