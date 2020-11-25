<?php

namespace ESign;

use ESign\Service\Http;

use ESign\Traits\Accounts;
use ESign\Traits\Flows;
use ESign\Traits\Organizations;
use ESign\Traits\Seals;
use ESign\Traits\Token;
use ESign\Traits\Files;

class ESign
{
	use Token;
	use Accounts;
	use Organizations;
	use Seals;
	use Files;
	use Flows;

	//测试环境请求地址
	private $hostDev = "https://smlopenapi.esign.cn/v1/";
	//测试环境请求地址
	private $hostProd = "https://openapi.esign.cn/v1/";

	private $host        = "https://openapi.esign.cn/v1/";

	//appId
	private $appId = "";
	//secret
	private $secret = "";

	private   $token;
	protected $client;
	protected $requestData;

	public function __construct($appId, $secret, $env = 'prod') {
		if ($env == 'dev') {
			$this->host = $this->hostDev;
		}
		$this->appId  = $appId;
		$this->secret = $secret;
		$this->client = new Http($this->host);

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
		//		$Flows = $this->GenerateSignFlows();
		//
		//		$filePath  = "pdf/test.pdf";
		//		$this->UpLoadFile($this->upsloadUrl, $filePath);
		//
		//		//流程文档添加
		//		$Flows->addDocumnet($addaDocumnet, $flowid, $fileId);
		//		//添加平台自动盖章签署区
		//		echo "\n";
		//		$res = $Flows->addPlatformSign($addPlatformSign, $flowid, $fileId);
		//		//添加手动盖章签署区
		//		$res = $Flows->addHandSign($addHandSign, $flowid, $fileId, $accountId);
		//		echo "流程文本域添加结果\n";
	}

	public function getErrorCode() {
		return $this->client->errCode;
	}

	public function getErrorMessage() {
		return $this->client->errMessage;
	}
}