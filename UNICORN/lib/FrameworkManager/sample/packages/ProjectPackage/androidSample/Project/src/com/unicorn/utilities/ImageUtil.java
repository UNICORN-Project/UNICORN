package com.unicorn.utilities;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Matrix;
import android.util.DisplayMetrics;
import android.view.WindowManager;

public class ImageUtil {
	public static Bitmap resizeBitmapToDisplaySize480_Id(Context context, int _id) {

		// 読み込み用のオプションオブジェクトを生成
		BitmapFactory.Options options = new BitmapFactory.Options();
		// この値をtrueにすると実際には画像を読み込まず、
		// 画像のサイズ情報だけを取得することができます。
		options.inJustDecodeBounds = true;
		options.inPreferredConfig = Bitmap.Config.RGB_565;

		// 画像ファイル読み込み
		// ここでは上記のオプションがtrueのため実際の
		// 画像は読み込まれないです。
		BitmapFactory.decodeResource(context.getResources(), _id, options);

		// 画面サイズを取得する
		WindowManager wm = (WindowManager) context.getSystemService(Context.WINDOW_SERVICE);
		Matrix matrix = new Matrix();
		DisplayMetrics metrics = new DisplayMetrics();
		wm.getDefaultDisplay().getMetrics(metrics);
		matrix.postScale(metrics.density, metrics.density);

		options.inJustDecodeBounds = false;

		// リサイズ
		Bitmap src = null;

		try {
			src = BitmapFactory.decodeResource(context.getResources(), _id, options);
		} catch (OutOfMemoryError e) {
			System.gc();
			src = BitmapFactory.decodeResource(context.getResources(), _id, options);
		}

		Bitmap dst = null;

		try {
			dst = Bitmap.createBitmap(src, 0, 0, options.outWidth, options.outHeight, matrix, true);
		} catch (OutOfMemoryError e) {
			System.gc();
			dst = Bitmap.createBitmap(src, 0, 0, options.outWidth, options.outHeight, matrix, true);
		}
		if (src.hashCode() != dst.hashCode()) {
			src.recycle();
			src = null;
		}
		System.gc();
		return dst;
	}

	public static Bitmap maskBitmap(Bitmap src, Bitmap mask, int maskColor) {
		if (src != null && mask != null) {
			int w = src.getWidth();
			int h = src.getHeight();
			if (w != mask.getWidth() || h != mask.getHeight()) {
				Matrix matrix = new Matrix();
				float scale = (float) src.getWidth() / (float) mask.getWidth();
				if (scale != (float) src.getHeight() / (float) mask.getHeight()) {
					return null;
				} else {
					matrix.setScale(scale, scale);
					mask = Bitmap.createBitmap(mask, 0, 0, mask.getWidth(), mask.getHeight(),
							matrix, true);
				}
				// return null;
			}
			int length = w * h;
			int srcPixels[] = new int[length];
			int maskPixels[] = new int[length];
			int newPixels[] = new int[length];

			src.getPixels(srcPixels, 0, w, 0, 0, w, h);
			mask.getPixels(maskPixels, 0, w, 0, 0, w, h);

			for (int i = 0; i < length; i++) {
				newPixels[i] = maskPixels[i] == maskColor ? Color.TRANSPARENT : srcPixels[i];
			}

			return Bitmap.createBitmap(newPixels, w, h, Bitmap.Config.ARGB_4444);
		}
		return null;
	}

	/**
	 * densityを求めて、引数をdipに合わせたピクセル値にする
	 * 
	 * @param _pix
	 * @return
	 */
	public static int Px2Dip(Context context, int _dip) {
		int pix = 0;

		// Densityの値を取得
		float tmpDensity = context.getResources().getDisplayMetrics().density;

		// ピクセル値を求める
		pix = (int) (_dip * tmpDensity + 0.5f);
		return pix;

	}

	/**
	 * densityを求めて、引数をdipに合わせたピクセル値にする
	 * 
	 * @param _pix
	 * @return
	 */
	public static int Px2Sp(Context context, int _dip) {
		int pix = 0;

		// scaleDensityの値を取得
		float tmpScaleDensity = context.getResources().getDisplayMetrics().scaledDensity;

		if (tmpScaleDensity > 2.0f)
			tmpScaleDensity = 1.5f;

		// ピクセル値を求める
		pix = (int) ((_dip * tmpScaleDensity + 0.5f));
		return pix;

	}

	public static int[] getSize(Context context, int resource_name) {
		
		int[] size = new int[2];
		// リソースからbitmapを作成
		Bitmap image = BitmapFactory.decodeResource(context.getResources(), resource_name);

		// 画像サイズ取得
		size[0] = image.getWidth();
		size[1] = image.getHeight();

		return size;
	}

}
