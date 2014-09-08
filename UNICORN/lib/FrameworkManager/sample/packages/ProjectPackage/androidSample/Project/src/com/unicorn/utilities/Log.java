package com.unicorn.utilities;

public class Log {

	private static final String TAG = "Project";
	private static final boolean IS_PRINT_DEBUG = true;
	private static final boolean IS_PRINT_WARNNING = true;
	private static final boolean IS_PRINT_ERROR = true;
	private static final boolean IS_PRINT_CLASS_AND_METHOD = true;

	private static String getParentClassAndMethod() {
		StackTraceElement[] stackTrace = Thread.currentThread().getStackTrace();
		String className = stackTrace[4].getClassName();
		String methodName = stackTrace[4].getMethodName();
		int lineNumber = stackTrace[4].getLineNumber();
		return String.format("%s-%s(%d)", className, methodName, lineNumber);
	}

	public static void printClassAndMethod() {
		if (IS_PRINT_CLASS_AND_METHOD) {
			android.util.Log.d(TAG, getParentClassAndMethod());
		}
	}

	public static void d(Object obj) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.d(TAG, getParentClassAndMethod());
			}

			if (obj == null) {
				android.util.Log.d(TAG, "null");
			} else {
				android.util.Log.d(TAG, obj.toString());
			}
		}
	}

	public static void d(String tag, String mes) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.d(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.d(tag, mes);
			}
		}
	}

	public static void w(Exception e) {
		if (IS_PRINT_WARNNING) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.w(TAG, getParentClassAndMethod());
			}

			if (e == null) {
				android.util.Log.w(TAG, "null");
			} else {
				android.util.Log.w(TAG, e.getMessage(), e);
			}
		}
	}

	public static void w(String tag, String mes) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.w(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.w(tag, mes);
			}
		}
	}

	public static void e(Exception e) {
		if (IS_PRINT_ERROR) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.w(TAG, getParentClassAndMethod());
			}

			if (e == null) {
				android.util.Log.e(TAG, "null");
			} else {
				android.util.Log.e(TAG, e.getMessage(), e);
			}
		}
	}

	public static void e(String tag, String mes) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.e(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.e(tag, mes);
			}
		}
	}

	public static void e(String tag, String mes, Exception e) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.e(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.e(tag, mes, e);
			}
		}
	}

	public static void i(Object obj) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.i(TAG, getParentClassAndMethod());
			}

			if (obj == null) {
				android.util.Log.i(TAG, "null");
			} else {
				android.util.Log.i(TAG, obj.toString());
			}
		}
	}

	public static void i(String tag, String mes) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.i(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.i(tag, mes);
			}
		}
	}

	public static void v(Object obj) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.v(TAG, getParentClassAndMethod());
			}

			if (obj == null) {
				android.util.Log.v(TAG, "null");
			} else {
				android.util.Log.v(TAG, obj.toString());
			}
		}
	}

	public static void v(String tag, String mes) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.v(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.v(tag, mes);
			}
		}
	}

	public static void v(String tag, String mes, Exception e) {
		if (IS_PRINT_DEBUG) {
			if (IS_PRINT_CLASS_AND_METHOD) {
				android.util.Log.v(TAG, getParentClassAndMethod());
			}

			if (tag != null && mes != null) {
				android.util.Log.v(tag, mes, e);
			}
		}
	}
}
