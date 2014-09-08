package com.unicorn.utilities;

import android.graphics.Bitmap;
import android.support.v4.util.LruCache;

public final class ImageCache {

	private static final int MEM_CACHE_SIZE = 15 * 2 * 1024 * 1024; // 30MB

	private static LruCache<String, Bitmap> sLruCache;

	static {
		int maxMemory = (int) (Runtime.getRuntime().maxMemory() / 1024);
//		sLruCache = new LruCache<String, Bitmap>(MEM_CACHE_SIZE) {
		sLruCache = new LruCache<String, Bitmap>(maxMemory/8) {
			@Override
			protected int sizeOf(String key, Bitmap bitmap) {
				return bitmap.getRowBytes() * bitmap.getHeight();
			}
		};
	}

	private ImageCache() {
	}

	public static void setImage(String key, Bitmap bitmap) {
		if (getImage(key) == null) {
			sLruCache.put(key, bitmap);
		}
	}

	public static Bitmap getImage(String key) {
		return sLruCache.get(key);
	}
}