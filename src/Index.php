<?php


namespace ESign;


use ESign\Traits\Account;
use ESign\Traits\Notify;
use ESign\Traits\Position;
use ESign\Traits\Seals;
use ESign\Traits\SignFlows;
use ESign\Traits\Token;
use ESign\Traits\Upload;

class Index
{
	//测试环境请求地址
	private $hostDev = "https://smlopenapi.esign.cn";
	//测试环境请求地址
	private $hostProd = "https://openapi.esign.cn";

	private $host = "https://openapi.esign.cn";

	//获取鉴权Token
	private $getToken = "%s/v1/oauth2/access_token?%s";
	//创建个人账户
	private $addPersonAccountID = "%s/v1/accounts/createByThirdPartyUserId";
	//创建企业账户
	private $addOrganizeAccountID = "%s/v1/organizations/createByThirdPartyUserId";
	//文件直传创建带签署文件
	private $upsloadUrl = "%s/v1/files/getUploadUrl";
	//创建签署流程，返回flowID
	private $creataFlow = "%s/v1/signflows";
	//流程文档添加
	private $addaDocumnet = "%s/v1/signflows/{flowId}/documents";
	//流程签名域添加
	private $addPlatformSign = "%s/v1/signflows/{flowId}/signfields/platformSign";
	private $addHandSign     = "%s/v1/signflows/{flowId}/signfields/handSign";
	//签署流程开启
	private $startSign = "%s/v1/signflows/{flowId}/start";
	//签署流程归档
	private $archiveSign = "%s/v1/signflows/{flowId}/archive";
	//签署流程文档下载
	private $downloadDocument = "%s/v1/signflows/{flowId}/documents";

	//appId
	public $appId = "";
	//secret
	public $secret = "";

	private $token;

	use Token, Account, SignFlows, Upload, Position, Seals, Notify;


	public function __construct($appId, $secret, $env = 'prod') {
		if ($env == 'dev') {
			$this->host = $this->hostDev;
		}
		$this->appId = $appId;
		$this->secret = $secret;

		$this->GetToken();
	}

	public function go(){
		//创建个人账户
		$this->GeneratePersonAccountID();
		//创建企业账户
		$this->GenerateOrganizeAccountID();


		//创建签署流程
		$SignFlows = $this->GenerateSignFlows();

		$filePath  = "pdf/test.pdf";
		$this->UpLoadFile($this->upsloadUrl, $filePath);

		//流程文档添加
		$SignFlows->addDocumnet($addaDocumnet, $flowid, $fileId);
		//添加平台自动盖章签署区
		echo "\n";
		$res = $SignFlows->addPlatformSign($addPlatformSign, $flowid, $fileId);
		//添加手动盖章签署区
		$res = $SignFlows->addHandSign($addHandSign, $flowid, $fileId, $accountId);
		echo "流程文本域添加结果\n";
	}
}