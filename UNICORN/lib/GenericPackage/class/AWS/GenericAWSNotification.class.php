<?php

// AWS利用クラスの定義
use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\Sns\SnsClient;

class GenericAWSNotification
{
	protected $_initialized = FALSE;

	// Amazon SDKのインスタンス
	protected $_AWS = NULL;

	// ManagementConsoleで登録したアプリ(APNS_SANDBOX:開発用)
	protected $_arnBase = NULL;

	protected function _init(){
		if(FALSE === $this->_initialized){
			$baseArn = NULL;
			$apiKey = NULL;
			$apiSecret = NULL;
			$region = NULL;
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_SNS_ARN_BASE')){
				$baseArn = Configure::AWS_SNS_ARN_BASE;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_KEY')){
				$apiKey = Configure::AWS_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_SNS_API_KEY')){
				$apiKey = Configure::AWS_SNS_API_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_SECRET')){
				$apiSecret = Configure::AWS_SECRET;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_SNS_API_SECRET')){
				$apiSecret = Configure::AWS_SNS_API_SECRET;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_REGION')){
				$region = Configure::AWS_REGION;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AWS_SNS_REGION')){
				$region = Configure::AWS_SNS_REGION;
			}
			elseif(defined('PROJECT_NAME') && strlen(PROJECT_NAME) > 0 && class_exists(PROJECT_NAME . 'Configure')){
				$ProjectConfigure = PROJECT_NAME . 'Configure';
				if(NULL !== $ProjectConfigure::constant('AWS_SNS_ARN_BASE')){
					$baseArn = $ProjectConfigure::AWS_SNS_ARN_BASE;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_KEY')){
					$apiKey = $ProjectConfigure::AWS_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_SNS_API_KEY')){
					$apiKey = $ProjectConfigure::AWS_SNS_API_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_SECRET')){
					$apiSecret = $ProjectConfigure::AWS_SECRET;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_SNS_API_SECRET')){
					$apiSecret = $ProjectConfigure::AWS_SNS_API_SECRET;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_REGION')){
					$region = $ProjectConfigure::AWS_REGION;
				}
				if(NULL !== $ProjectConfigure::constant('AWS_SNS_REGION')){
					$region = $ProjectConfigure::AWS_SNS_REGION;
				}
			}
			$arns = explode('://', $baseArn);
			$this->_arnBase = 'arn:aws:sns:'.$arns[0].':app/%target_pratform%/'.$arns[1];
			$this->_initialized = TRUE;
			if (NULL === $this->_AWS) {
				$this->_AWS = Aws::factory(array(
						'key'    => $apiKey,
						'secret' => $apiSecret,
						//'region' => constant('Region::'.$region)
						'region' => Region::TOKYO
				))->get('sns');
			}
		}
	}

	/**
	 * Push通知先(EndpointArn)を登録
	 */
	public function createPlatformEndpoint($argDevicetoken, $argDeviceType) {
		$this->_init();
		$targetPratform = 'APNS_SANDBOX';
		if(TRUE !== isTest() && TRUE === ('iOS' === $argDeviceType || 'iPhone' === $argDeviceType || 'iPad' === $argDeviceType || 'iPod' === $argDeviceType)){
			// 本番用のiOSPush通知
			$targetPratform = 'APNS';
		}
		else {
			// Android用はココ！
		}
		$arn = str_replace('%target_pratform%', $targetPratform, $this->_arnBase);
		$options = array(
				'PlatformApplicationArn' => $arn,
				'Token'                  => $argDevicetoken,
		);
		try {
			logging('PlatformApplicationArn='.$arn, 'push');
			$res = $this->_AWS->createPlatformEndpoint($options);
		} catch (Exception $e) {
			logging($e->__toString(), 'push');
			return FALSE;
		}
		logging('create endpoint res=', 'push');
		logging($res, 'push');
		return $res;
	}

	/**
	 * 通知
	 */
	public function pushMessage($argDeviceIdentifier, $argDeviceType, $argMessage, $argPadge=1, $argCustomURLScheme=NULL) {
		$message = array('alert'=>$argMessage, 'badge' => $argPadge, 'sound' => 'default');
		if(NULL !== $argCustomURLScheme){
			$message['scm'] = $argCustomURLScheme;
		}
		return $this->pushJson($argDeviceIdentifier, $argDeviceType, $message);
	}

	/**
	 * 通知(JSON)
	 */
	public function pushJson($argDeviceIdentifier, $argDeviceType, $argments) {
		$this->_init();
		$newEndpoint = NULL;
		$deviceEndpoint = $argDeviceIdentifier;
		logging('endpoint='.$deviceEndpoint, 'push');
		if(FALSE === strpos('arn:aws:sns:', $argDeviceIdentifier)){
			// エンドポイント指定では無いので、先ずはAESにEndpoint登録をする
			logging('create endpoint:'.$deviceEndpoint.':'.$argDeviceType, 'push');
			$res = $this->createPlatformEndpoint($argDeviceIdentifier, $argDeviceType);
			logging('pushJson for create endpoint res=', 'push');
			logging($res, 'push');
			if(FALSE !== $res){
				$newEndpoint = $res['EndpointArn'];
				$deviceEndpoint = $newEndpoint;
			}
		}
		try {
			$targetPratform = 'APNS_SANDBOX';
			if(TRUE !== isTest() && TRUE === ('iOS' === $argDeviceType || 'iPhone' === $argDeviceType || 'iPad' === $argDeviceType || 'iPod' === $argDeviceType)){
				// 本番用のiOSPush通知
				$targetPratform = 'APNS';
			}
			else {
				// Android用はココ！
			}
			$json = array('MessageStructure' => 'json', 'TargetArn' => trim($deviceEndpoint));
			$json['Message'] = json_encode(array($targetPratform => json_encode(array('aps' => $argments))));
			logging($json, 'push');
			$res = $this->_AWS->publish($json);
		}
		catch (Exception $e) {
			logging($e->__toString(), 'push');
			return FALSE;
		}
		if(!is_array($res)){
			$res = array('res' => $res);
		}
		if(NULL !== $newEndpoint){
			$res['endpoint'] = $newEndpoint;
		}
		return $res;
	}
}

?>