<?php

namespace ESign\Traits;

use ESign\Config;
use GuzzleHttp\Exception\RequestException;

/**
 * 签署方账户API
 *
 * @package ESign\Traits
 */
trait Accounts
{
	/**
	 * 证件类型，默认CRED_PSN_CH_IDCARD
	 * （1）CRED_PSN_CH_IDCARD 大陆身份证，默认值
	 * （2）CRED_PSN_CH_TWCARD 台湾来往大陆通行证
	 * （3）CRED_PSN_CH_MACAO 澳门来往大陆通行证
	 * （4）CRED_PSN_CH_HONGKONG 香港来往大陆通行证
	 * （5）CRED_PSN_FOREIGN 外籍证件
	 * （6）CRED_PSN_PASSPORT 护照
	 * （7）CRED_PSN_CH_SOLDIER_IDCARD 军官证
	 * （8）CRED_PSN_CH_SSCARD 社会保障卡
	 * （9）CRED_PSN_CH_ARMED_POLICE_IDCARD 武装警察身份证件
	 * （10）CRED_PSN_CH_RESIDENCE_BOOKLET 户口簿
	 * （11）CRED_PSN_CH_TEMPORARY_IDCARD 临时居民身份证
	 * （12）CRED_PSN_CH_GREEN_CARD 外国人永久居留证
	 * （13）CRED_PSN_SHAREHOLDER_CODE 股东代码证
	 * （14）CRED_PSN_POLICE_ID_CARD 警官证
	 * （15）CRED_PSN_UNKNOWN 未知类型
	 */

	/**
	 * 个人账户创建
	 * 接口描述
	 * （1）对接方调用本接口在e签宝平台中创建个人账号，后续有关该用户的所有操作都需使用该用户的accountId。如提供用户证件信息，则将根据提供的用户证件信息申请数字证书。
	 * （2）创建账户的同时会生成一个默认的个人印章，默认印章可通过查询个人印章接口查到，默认印章样式如下：
	 */
	public function accountsCreateByThirdPartyUserId($userUniqueId, $name, $idNumber, $mobile, $email, $idType = 'CRED_PSN_CH_IDCARD') {
		$data = [
			"thirdPartyUserId" => $userUniqueId,
			"name"             => $name,
			"idType"           => $idType,
			"idNumber"         => $idNumber,
			"mobile"           => $mobile,
			"email"            => $email
		];

		$uri = Config::Accounts(__FUNCTION__);
		return $this->client->post($uri, array_merge($this->requestData, $data));
	}

	//个人账户修改(按照账号ID修改)
	public function accountsUpdateByAccountId($accountId, $name, $idNumber, $mobile, $email, $idType = 'CRED_PSN_CH_IDCARD') {
		$data = [
			"name"     => $name,
			"idType"   => $idType,
			"idNumber" => $idNumber,
			"mobile"   => $mobile,
			"email"    => $email
		];

		$uri = Config::Accounts(__FUNCTION__, $accountId);

		return $this->client->put($uri, array_merge($this->requestData, $data));
	}

	//个人账户修改(按照第三方用户ID修改)
	public function accountsUpdateByThirdId($userUniqueId, $name, $idNumber, $mobile, $email, $idType = 'CRED_PSN_CH_IDCARD') {
		$data = [
			'query'    => ["thirdPartyUserId" => $userUniqueId],
			"name"     => $name,
			"idType"   => $idType,
			"idNumber" => $idNumber,
			"mobile"   => $mobile,
			"email"    => $email
		];
		$uri  = Config::Accounts(__FUNCTION__);

		return $this->client->put($uri, array_merge($this->requestData, $data));
	}

	//查询个人账户（按照账户ID查询）
	public function accountsGetByAccountId($accountId) {
		$uri = Config::Accounts(__FUNCTION__, $accountId);

		return $this->client->get($uri, $this->requestData);
	}

	//查询个人账户（按照第三方用户ID查询）
	public function accountsGetByThirdId($thirdPartyUserId) {
		$data = ['query' => ['thirdPartyUserId' => $thirdPartyUserId]];
		$uri  = Config::Accounts(__FUNCTION__);

		return $this->client->get($uri, array_merge($this->requestData, $data));
	}

	//注销个人账户（按照账号ID注销）
	public function accountsDeleteByAccountId($accountId) {
		$uri = Config::Accounts(__FUNCTION__, $accountId);

		$response = $this->client->delete($uri, $this->requestData);
		return $response;
	}

	//注销个人账户（按照第三方用户ID注销）
	public function accountsDeleteByThirdId($thirdPartyUserId) {
		$data     = ['query' => ['thirdPartyUserId' => $thirdPartyUserId]];
		$uri      = Config::Accounts(__FUNCTION__);
		$response = $this->client->delete($uri, array_merge($this->requestData, $data));
		return $response;
	}

	//设置签署密码
	public function accountsSetSignPwd($accountId, $password) {
		$uri = Config::Accounts(__FUNCTION__, '/'.$accountId.'/');

		$data = ["password" => md5($password)];
		return $this->client->post($uri, array_merge($this->requestData, $data));
	}

	/**
	 * 设置静默签署/撤销静默签署
	 *
	 * @param $accountId
	 * @param $deadline
	 *
	 * @return false|mixed
	 */
	public function accountsSignAuth($accountId, $deadline) {
		$data = empty($deadline) ? [] : ['deadline' => $deadline];
		$uri  = Config::Accounts(__FUNCTION__, '/'.$accountId);

		return $this->client->delete($uri, array_merge($this->requestData, $data));
	}

}