package com.unicorn.utilities;

import android.graphics.Bitmap;
import android.widget.ImageView;
import android.widget.ProgressBar;

public class LoadBitmapItem {

	private ImageView imgView;
	private ProgressBar progress;

	private Bitmap bitmap;
	private String url;
	private boolean isMask;
	
	private int sideLength;

	public int getSideLength() {
		return sideLength;
	}

	public void setSideLength(int sideLength) {
		this.sideLength = sideLength;
	}

	public Bitmap getBitmap() {
		return bitmap;
	}

	public void setBitmap(Bitmap bitmap) {
		this.bitmap = bitmap;
	}

	public String getUrl() {
		return url;
	}

	public void setUrl(String url) {
		this.url = url;
	}

	public ImageView getImgView() {
		return imgView;
	}

	public void setImgView(ImageView imgView) {
		this.imgView = imgView;
	}

	public ProgressBar getProgress() {
		return progress;
	}

	public void setProgress(ProgressBar progress) {
		this.progress = progress;
	}

	public boolean isMask() {
		return isMask;
	}

	public void setMask(boolean isMask) {
		this.isMask = isMask;
	}



}