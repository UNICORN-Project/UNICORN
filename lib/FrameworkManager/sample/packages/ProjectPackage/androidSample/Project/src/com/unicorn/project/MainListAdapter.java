package com.unicorn.project;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.List;

import com.unicorn.model.UserModel;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;

public class MainListAdapter extends ArrayAdapter<UserModel> {
	private Context mContext;
	private List<UserModel> mList;
	private LayoutInflater mInflater;

	public MainListAdapter(Context context, int textViewResourceId, List<UserModel> list) {
		super(context, textViewResourceId, list);

		mContext = context;
		mList = list;
		mInflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	}

	// 1要素分のビューの取得
	@Override
	public View getView(final int position, View convertView, final ViewGroup parent) {
		View view;
		UserModel data = mList.get(position);

		view = mInflater.inflate(R.layout.main_list_row, null);

		return view;
	}
}
