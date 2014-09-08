<?php

/**
 * ImageInfo
 *
 * @version 0.9.0
 *
 * 概要：
 * PNG、Jpeg、GIFの各種画像ファイルから画像サイズ等のヘッダー情報を取得します。
 * 趣味で作っている物なので業務用には使用できません。
 *
 * 現状の問題点：
 *  - 壊れているファイルを読み込ませた場合に誤動作する可能性がある。
 *  - 画像ファイルの判別処理が甘い。特にGIFとPNGはファイルのシグネチャが
 *    合っていればGIF画像と認識するので、ファイルのシグネチャだけが合っている
 *    偽物のファイルも認識してしまう。
 *  - ロスレス、拡張ベースライン方式のJpeg画像に対応していない。
 *  - 色数を取得できない。
 *
 * 簡単な使用方法：
 *
 * <code>
 * $result = ImageInfo::getInfoFromFile('test.jpg');
 * if($result->isError())
 	* {
 *     die('エラーコード：'.$result->getErrorCode());
 * }
 * else
 	* {
 *     echo $result->getW()."<br>";
 *     echo $result->getH()."<br>";
 * }
 * </code>
 *
 * @author ひろき
 * @copyright 2006 GASOLINE STAND
 * @link http://hrgs.itbdns.com/
 */


//--------------------------------------------------------------------
// エラーコード
//--------------------------------------------------------------------

/**
 * 原因不明のエラー
 */
define('ImageInfo_ERROR_UNKNOWN',     1);

/**
 * ヘッダー情報の取得に失敗
*/
define('ImageInfo_ERROR_HEADERDATA',  2);


/**
 * サポートされていないフォーマット
*/
define('ImageInfo_ERROR_UNSUPPORTED', 3);


//--------------------------------------------------------------------
// その他の定数
//--------------------------------------------------------------------

/**
 * ベースライン方式のJPEG
 * @access private
*/
define('ImageInfo_JPEG_TYPE_BASELINE',    0);

/**
 * プログレッシブ形式のJPEG
 * @access private
*/
define('ImageInfo_JPEG_TYPE_PROGRESSIVE', 2);


//--------------------------------------------------------------------
// クラス定義
//--------------------------------------------------------------------

/**
 * ファイル、またはバイナリデータから画像の情報を取得するクラス
 * インスタンス化せず、以下のように静的に呼び出して使用します。
 *
 * $result = ImageInfo::getInfoFromFile('/xxx/xxx.xxx');
*/
class ImageInfo
{
	/**
	 * ファイルからImageInfoResultオブジェクトを生成して返す
	 *
	 * @static
	 * @param string $filename ファイル名
	 * @return object ImageInfoResultオブジェクト
	 */
	function &getInfoFromFile($filename)
	{
		$null = null;
		$fp = fopen($filename, 'rb');
		if(!$fp)
		{
			return $null;
		}

		$buffer = '';
		while(!feof($fp))
		{
			$buffer .= fread($fp, 8192);
		}
		fclose($fp);

		$res = &ImageInfo::getInfoFromData($buffer);
		return $res;
	}


	/**
	 * バイナリデータからImageInfoResultオブジェクトを生成して返す
	 *
	 * @static
	 * @param string &$data バイナリデータ
	 * @return object ImageInfoResultオブジェクト
	 */
	function &getInfoFromData(&$data)
	{
		$image_info['w'] = 0;
		$image_info['h'] = 0;
		$image_info['colors'] = 0;
		$image_info['type'] = 0;
		$image_info['error'] = 0;

		if(ImageInfo_Jpeg::isValid($data))
		{
			//JPEG画像の場合
			$obj = new ImageInfo_Jpeg();
		}
		elseif(ImageInfo_Png::isValid($data))
		{
			//PNG画像の場合
			$obj = new ImageInfo_Png();
		}
		elseif(ImageInfo_Gif::isValid($data))
		{
			//GIF画像の場合
			$obj = new ImageInfo_Gif();
		}
		else
		{
			//サポートしていないか、無効な画像データ
			$image_info['error'] = ImageInfo_ERROR_UNSUPPORTED;
			$result = new ImageInfoResult($image_info);
			return $result;
		}

		//画像の情報を取得する
		$obj->getInfo($data, $image_info);
		$result = new ImageInfoResult($image_info);
		return $result;
	}
};


/**
 * 画像データの情報を取得した結果を格納するクラス
 */
class ImageInfoResult
{
	/**
	 * 画像の横幅
	 * @access private
	 * @var int
	 */
	var $w;

	/**
	 * 画像の縦幅
	 * @access private
	 * @var int
	 */
	var $h;

	/**
	 * 画像の種類(IMAGETYPE_XXX)
	 * @access private
	 * @var int
	 */
	var $type;

	/**
	 * 画像の拡張子(jpg|gif|png)
	 * @access private
	 * @var string
	 */
	var $extension;

	/**
	 * 画像の色数
	 * @access private
	 * @var int
	 */
	var $colors;

	/**
	 * エラーがあったかどうか
	 * @access private
	 * @var boolean
	 */
	var $error_flag;

	/**
	 * エラーコード
	 * @access private
	 * @var int
	 */
	var $error_code;

	/**
	 * コンストラクタ。画像の情報をセットする。
	 *
	 * @param array $image_info 画像の情報を含む配列
	 *                        - $arr['w']         = 横幅
	 *                        - $arr['h']         = 横幅
	 *                        - $arr['type']      = 画像の種類(IMAGETYPE_XXX)
	 *                        - $arr['extension'] = 画像の拡張子(jpg|gif|png)
	 *                        - $arr['colors']    = 色数
	 *                        - $arr['error']     = エラーコード
	 */
	function ImageInfoResult($image_info)
	{
		$this->w = 0;
		$this->h = 0;
		$this->type = 0;
		$this->extension = NULL;
		$this->colors = 0;
		$this->error_flag = false;
		$this->error_code = 0;

		if(!is_array($image_info))
		{
			$this->error_flag = true;
			$this->error_code = ImageInfo_ERROR_UNKNOWN;
			return;
		}

		//$image_info配列から値を取り出す
		$w           = (isset($image_info['w']))           ? $image_info['w']           : '';
		$h           = (isset($image_info['h']))           ? $image_info['h']           : '';
		$type        = (isset($image_info['type']))        ? $image_info['type']        : '';
		$extension   = (isset($image_info['extension']))   ? $image_info['extension']   : '';
		$colors      = (isset($image_info['colors']))      ? $image_info['colors']      : '';
		$error       = (isset($image_info['error']))       ? $image_info['error']       : '';

		//各値が正しい値でなければエラー
		if(!isInt($w) || !isInt($h) || !isInt($type) || !isInt($colors) || !isInt($error))
		{
			$this->error_flag = true;
			$this->error_code = ImageInfo_ERROR_UNKNOWN;
			return;
		}


		$this->w = $w;
		$this->h = $h;
		$this->type = $type;
		$this->extension = $extension;
		$this->colors = $colors;
		$this->error_code = $error;

		if($this->error_code != 0)
		{
			//エラーコードが0以外の場合はエラー
			$this->error_flag = true;
		}
	}

	/**
	 * 横幅を返す
	 * @return int 横幅
	 */
	function getW()
	{
		return $this->w;
	}

	/**
	 * 縦幅を返す
	 * @return int 縦幅
	 */
	function getH()
	{
		return $this->h;
	}

	/**
	 * 画像の種類を返す
	 * @return int 画像の種類。(IMAGETYPE_XXX)
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * 色数を返す
	 * @return int 色数
	 */
	function getColors()
	{
		return $this->colors;
	}

	/**
	 * エラーがあったかどうかを返す
	 * @return boolean true  => エラーあり
	 *                 false => エラーなし
	 */
	function isError()
	{
		return $this->error_flag;
	}

	/**
	 * エラーコードを返す
	 * @return int エラーコード。ImageInfo_ERROR_XXX
	 */
	function getErrorCode()
	{
		return $this->error_code;
	}

}

/**
 * ImgaeInfoと依存して、画像を色々する画像Util
 */
class ImageUtil
{

	function crop($argImageBinary, $argCropLeft = 0, $argCropTop = 0, $argCropRight = 0, $argCropBottom = 0){

		$info = ImageInfo::getInfoFromData($argImageBinary);
		$image = imagecreatefromstring($argImageBinary);

		$final_width = 0;
		$final_height = 0;
		$width_old = $info->w;
		$height_old = $info->h;

		$newWidth = $argCropRight - $argCropLeft;
		$newHeight = $argCropBottom - $argCropTop;

		$final_width = ( $newWidth <= 0 ) ? $width_old : $newWidth;
		$final_height = ( $newHeight <= 0 ) ? $height_old : $newHeight;

		$resizedImageData = imagecreatetruecolor( $final_width, $final_height );

		if ( ($info->type == IMAGETYPE_GIF) || ($info->type == IMAGETYPE_PNG) ) {
			$trnprt_indx = imagecolortransparent($image);

			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {

				// Get the original image's transparent color's RGB values
				$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($resizedImageData, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($resizedImageData, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($resizedImageData, $trnprt_indx);


			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($info->type == IMAGETYPE_PNG) {

				// Turn off transparency blending (temporarily)
				imagealphablending($resizedImageData, false);

				// Create a new transparent color for image
				$color = imagecolorallocatealpha($resizedImageData, 255, 255, 255, 127);

				// Completely fill the background of the new image with allocated color.
				imagefill($resizedImageData, 0, 0, $color);

				// Restore transparency blending
				imagesavealpha($resizedImageData, true);
			}
		}

		//echo $final_width, $final_height;
		imagecopyresampled($resizedImageData, $argImageResouceID, 0, 0, $argCropLeft, $argCropTop, $final_width, $final_height, $final_width, $final_height);

		$imageBinary = NULL;

		ob_start();
		if(IMAGETYPE_JPEG == $info->type){
			imagejpeg($resizedImageData);
		}
		elseif(IMAGETYPE_GIF == $info->type){
			imagegif($resizedImageData);
		}
		elseif(IMAGETYPE_PNG == $info->type){
			imagepng($resizedImageData);
		}
		$imageBinary = ob_get_clean();
		imagedestroy($resizedImageData);

		return $imageBinary;
	}

	function resize($argImageBinary, $width = 0, $height = 0, $proportional = false)
	{
		if ( $height <= 0 && $width <= 0 ) {
			return false;
		}

		$info = ImageInfo::getInfoFromData($argImageBinary);
		$image = imagecreatefromstring($argImageBinary);

		$final_width = 0;
		$final_height = 0;
		$width_old = $info->w;
		$height_old = $info->h;

		if (false !== $proportional) {
			if ($width == 0) $factor = $height/$height_old;
			elseif ($height == 0) $factor = $width/$width_old;
			else $factor = min ( $width / $width_old, $height / $height_old);

			$final_width = round ($width_old * $factor);
			$final_height = round ($height_old * $factor);
		}
		else {
			$final_width = $width;
			$final_height = $height;

			$width_gap = $width_old / $width;
			$height_gap = $height_old / $height;
		}

		$resizedImageData = imagecreatetruecolor( $final_width, $final_height );

		if ( ($info->type == IMAGETYPE_GIF) || ($info->type == IMAGETYPE_PNG) ) {
			$trnprt_indx = imagecolortransparent($image);

			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {

				// Get the original image's transparent color's RGB values
				$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($resizedImageData, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($resizedImageData, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($resizedImageData, $trnprt_indx);


			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($info->type == IMAGETYPE_PNG) {

				// Turn off transparency blending (temporarily)
				imagealphablending($resizedImageData, false);

				// Create a new transparent color for image
				$color = imagecolorallocatealpha($resizedImageData, 255, 255, 255, 127);

				// Completely fill the background of the new image with allocated color.
				imagefill($resizedImageData, 0, 0, $color);

				// Restore transparency blending
				imagesavealpha($resizedImageData, true);
			}
		}

		if (false !== $proportional) {
			imagecopyresampled($resizedImageData, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
		}
		else {
			//横より縦の比率が大きい場合は、求める画像サイズより縦長なので縦の上下をカット
			if($width_gap < $height_gap){
				$cut = ceil((($height_gap - $width_gap) * $final_height) / 2);
				imagecopyresampled($resizedImageData, $image, 0, 0, 0, $cut, $final_width, $final_height, $width_old, $height_old - ($cut * 2));
					
				//縦より横の比率が大きい場合は、求める画像サイズより横長なので横の左右をカット
			}elseif($width_gap > $height_gap){
				$cut = ceil((($width_gap - $height_gap) * $final_width) / 2);
				imagecopyresampled($resizedImageData, $image, 0, 0, $cut, 0, $final_width, $final_height, $width_old - ($cut * 2), $height_old);
					
				//縦横比が同じなら、そのまま縮小
			}else{
				imagecopyresampled($resizedImageData, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
			}
		}

		$imageBinary = NULL;

		ob_start();
		if(IMAGETYPE_JPEG == $info->type){
			imagejpeg($resizedImageData);
		}
		elseif(IMAGETYPE_GIF == $info->type){
			imagegif($resizedImageData);
		}
		elseif(IMAGETYPE_PNG == $info->type){
			imagepng($resizedImageData);
		}
		$imageBinary = ob_get_clean();
		imagedestroy($resizedImageData);

		return $imageBinary;
	}
}

/**
 * JPEG画像の情報を処理するクラス
 *
 * ベースライン方式、プログレッシブ形式のみに対応しています。
 * @access private
 */
class ImageInfo_Jpeg
{
	var $jpeg_type;

	/**
	 * バイナリデータからJPEG画像の情報を取得して返す
	 *
	 * @param string &$data        画像のバイナリデータ
	 * @param array  &$image_info  取得した画像情報を格納する配列
	 *
	 * @return boolean true  => 情報の取得に成功
	 *                 false => エラー発生
	 */
	function getInfo(&$data, &$image_info)
	{
		//画像タイプにJPEGを設定。
		$image_info['type'] = IMAGETYPE_JPEG;
		$image_info['extension'] = 'jpg';


		$sof0_pos = $sof2_pos = 0;

		//横幅、縦幅の情報を取得する
		//SOF0、またはSOF2セグメントを探す
		$sof0_pos = strpos($data, chr(0xFF).chr(0xC0));
		if(!$sof0_pos)
		{
			//SOF0が見つからない場合はSOF2セグメントを探す
			$sof2_pos = strpos($data, chr(0xFF).chr(0xC2));
		}

		$sof_pos = 0;
		if($sof0_pos)
		{
			//SOF0が見つかった場合はベースラインJPEG
			$this->jpeg_type = ImageInfo_JPEG_TYPE_BASELINE;
			$sof_pos = $sof0_pos;
		}
		elseif($sof2_pos)
		{
			//SOF0が見つかった場合はプログレッシブJPEG
			$this->jpeg_type = ImageInfo_JPEG_TYPE_PROGRESSIVE;
			$sof_pos = $sof2_pos;
		}
		else
		{
			//どちらもみつからない場合はエラー
			$image_info['error'] = ImageInfo_ERROR_HEADERDATA;
			return false;
		}

		//SOF0、またはSOF2のセグメントから画像のサイズを取得する
		$bin_h = substr($data, $sof_pos+5, 2);
		$bin_w = substr($data, $sof_pos+7, 2);

		$image_info['h'] = bin2int($bin_h);
		$image_info['w'] = bin2int($bin_w);
		return true;
	}


	/**
	 * 有効なJPEGファイルのデータであるかを判定する
	 *
	 * @static
	 * @param string &$data 画像のバイナリデータ
	 * @return boolean true  => JPEG画像
	 *                 false => JPEG画像ではない
	 */
	function isValid(&$data)
	{
		//SOIセグメントの取得。
		//先頭の2バイト。
		$soi = bin2int(substr($data, 0, 2));
		if((int)$soi != (int)0xFFD8)
		{
			return false;
		}

		//APP0セグメントを探す
		$app0_pos = strpos($data, chr(0xFF).chr(0xE0));
		if(!$app0_pos)
		{
			return false;
		}

		//app0セグメントが見つかったら、JFIFのシグネチャを調べる
		$sig = substr($data, $app0_pos+4, 5);
		if(strcmp($sig, 'JFIF'.chr(0)) != 0)
		{
			return false;
		}

		return true;
	}
};


/**
 * PNG画像の情報を処理するクラス
 * @access private
 */
class ImageInfo_Png
{
	/**
	 * バイナリデータからPNG画像の情報を取得して返す
	 *
	 * @param string &$data        画像のバイナリデータ
	 * @param array  &$image_info  取得した画像情報を格納する配列
	 *
	 * @return boolean true  => 情報の取得に成功
	 *                 false => エラー発生
	 */
	function getInfo(&$data, &$image_info)
	{
		//画像タイプにJPEGを設定。
		$image_info['type'] = IMAGETYPE_PNG;
		$image_info['extension'] = 'png';

		//IHDRチャンクを探す
		$ihdr_pos = strpos($data, 'IHDR');
		if(!$ihdr_pos)
		{
			$image_info['error'] = ImageInfo_ERROR_HEADERDATA;
			return false;
		}

		$bin_w = substr($data, $ihdr_pos+4, 4);
		$bin_h = substr($data, $ihdr_pos+8, 4);

		$image_info['w'] = bin2int($bin_w, 'N');
		$image_info['h'] = bin2int($bin_h, 'N');
		return true;
	}


	/**
	 * 有効なJPEGファイルのデータであるかを判定する
	 *
	 * @static
	 * @param string &$data 画像のバイナリデータ
	 * @return boolean true  => PNG画像
	 *                 false => PNG画像ではない
	 */
	function isValid(&$data)
	{
		$bin_buf = substr($data, 0, 8);
		$hex_buf = bin2hex($bin_buf);

		//PNGのシグネイチャは16進数で「89  50  4e  47  0d  0a  1a  0a」でなければならない。
		if( strcasecmp($hex_buf, '89504E470D0A1A0A') != 0 )
		{
			return false;
		}

		return true;
	}
};


/**
 * GIF画像の情報を処理するクラス
 * @access private
 */
class ImageInfo_Gif
{
	/**
	 * バイナリデータからGIF画像の情報を取得して返す
	 *
	 * @param string &$data        画像のバイナリデータ
	 * @param array  &$image_info  取得した画像情報を格納する配列
	 *
	 * @return boolean true  => 情報の取得に成功
	 *                 false => エラー発生
	 */
	function getInfo(&$data, &$image_info)
	{
		//画像タイプにJPEGを設定。
		$image_info['type'] = IMAGETYPE_GIF;
		$image_info['extension'] = 'gif';

		$bin_w = substr($data, 6, 2);
		$bin_h = substr($data, 8, 2);

		$image_info['w'] = bin2int($bin_w, 'v');
		$image_info['h'] = bin2int($bin_h, 'v');
		return true;
	}


	/**
	 * 有効なJPEGファイルのデータであるかを判定する
	 *
	 * @static
	 * @param string &$data 画像のバイナリデータ
	 * @return boolean true  => PNG画像
	 *                 false => PNG画像ではない
	 */
	function isValid(&$data)
	{
		$sig = substr($data, 0, 3);
		$ver = substr($data, 3, 3);

		if(strcmp($sig, 'GIF') != 0)
		{
			return false;
		}

		if((strcmp($ver, '87a') != 0) && (strcmp($ver, '89a') != 0))
		{
			return false;
		}
		return true;
	}
};


//-----------------------------------------------------------------------
// ユーティリティ関数
//-----------------------------------------------------------------------

/**
 * バイナリデータを任意の整数に変換する。(unpack関数のショートカット)
 *
 * @access private
 * @param string $bin    バイナリデータ
 * @param string $format unpack時のフォーマット文字列。デフォルトは「n」
 * @return int 任意の整数
 */
function bin2int($bin, $format='n')
{
	list(,$unpacked) = unpack($format."*", $bin);
	return $unpacked;
}


/**
 * 整数かを判定する
 *
 * @access private
 * @param string $val 判定の対象となる値
 * @return boolean true  => 整数である
 *                 false => 整数ではない
 */
function isInt(&$val)
{
	return ($val !== '' && ctype_digit((string)$val));
}


?>