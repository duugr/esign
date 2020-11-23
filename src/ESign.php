<?php

namespace ESign;

use ESign\Traits\Accounts;
use ESign\Traits\Organizations;

//use ESign\Traits\Notify;
//use ESign\Traits\Position;
//use ESign\Traits\Seals;
//use ESign\Traits\SignFlows;
use ESign\Traits\Token;

//use ESign\Traits\Upload;

//use ESign\Service\Accounts;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ESign
{
	use Token;
	use Accounts;
	use Organizations;

	//测试环境请求地址
	private $hostDev = "https://smlopenapi.esign.cn/v1/";
	//测试环境请求地址
	private $hostProd = "https://openapi.esign.cn/v1/";

	private $host        = "https://openapi.esign.cn/v1/";
	private $urlGetToken = "oauth2/access_token";

	//appId
	private $appId = "";
	//secret
	private $secret = "";

	private   $token;
	protected $client;
	protected $requestData;
	protected $errCode;
	protected $errMessage;

	public function __construct($appId, $secret, $env = 'prod') {
		if ($env == 'dev') {
			$this->host = $this->hostDev;
		}
		$this->appId  = $appId;
		$this->secret = $secret;

		$this->client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->host,
			// You can set any number of default request options.
			//			'timeout'  => 5.0,

		]);

		$this->GetToken();

		$this->requestData = [
			'headers' => [
				'X-Tsign-Open-App-Id' => $this->appId,
				'X-Tsign-Open-Token'  => $this->token,
				'Content-Type'        => 'application/json; charset=UTF-8'
			]
		];
	}

	public function go() {
		//		//创建个人账户
		//		$this->GeneratePersonAccountID();
		//		//创建企业账户
		//		$this->GenerateOrganizeAccountID();
		//
		//
		//		//创建签署流程
		//		$SignFlows = $this->GenerateSignFlows();
		//
		//		$filePath  = "pdf/test.pdf";
		//		$this->UpLoadFile($this->upsloadUrl, $filePath);
		//
		//		//流程文档添加
		//		$SignFlows->addDocumnet($addaDocumnet, $flowid, $fileId);
		//		//添加平台自动盖章签署区
		//		echo "\n";
		//		$res = $SignFlows->addPlatformSign($addPlatformSign, $flowid, $fileId);
		//		//添加手动盖章签署区
		//		$res = $SignFlows->addHandSign($addHandSign, $flowid, $fileId, $accountId);
		//		echo "流程文本域添加结果\n";
	}

	public function getErrorCode() {
		return $this->errCode;
	}

	public function getErrorMessage() {
		return $this->errMessage;
	}

	protected function getUri($path, $delimiter = '/'): string {
		return preg_replace_callback('/(^.*?)(?=[A-Z])([A-Z])/u', function ($matches) use ($delimiter) {
			return lcfirst($matches[1]).$delimiter.lcfirst($matches[2]);
		}, $path);
	}
}