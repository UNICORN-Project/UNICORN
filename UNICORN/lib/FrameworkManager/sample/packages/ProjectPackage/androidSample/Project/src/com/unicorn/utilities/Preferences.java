package com.unicorn.utilities;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class Preferences {

	public SharedPreferences pref;
	public Context con;

	public Preferences(Context con) {
		this.con = con;
		this.pref = con.getSharedPreferences("Project_Pref", Context.MODE_PRIVATE);
	}

	public void clear() {
		Editor editor = pref.edit();
		editor.clear();
		editor.commit();

	}

	public int getIntValue(String key) {
		return pref.getInt(key, 0);
	}

	public void setIntValue(String key, int value) {
		Editor editor = pref.edit();

		editor.putInt(key, value);

		editor.commit();
	}
}
