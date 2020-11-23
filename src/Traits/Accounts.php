<?php

namespace ESign\Traits;

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

		try {
			$uri      = $this->getUri(__FUNCTION__);
			$response = $this->client->post($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['accountId'])) {
				return $result['data']['accountId'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
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

		try {

			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $accountId);

			$response = $this->client->put($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['accountId'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
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

		try {
			$uri = $this->getUri(__FUNCTION__);

			$response = $this->client->put($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['accountId'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

	//查询个人账户（按照账户ID查询）
	public function accountsGetByAccountId($accountId) {
		try {
			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $accountId);

			$response = $this->client->get($uri, $this->requestData);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['accountId'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

	//查询个人账户（按照第三方用户ID查询）
	public function accountsGetByThirdId($thirdPartyUserId) {
		try {
			$data = ['query' => ['thirdPartyUserId' => $thirdPartyUserId]];
			$uri  = $this->getUri(__FUNCTION__);

			$response = $this->client->get($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['accountId'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

	//注销个人账户（按照账号ID注销）
	public function accountsDeleteByAccountId($accountId) {
		try {
			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $accountId);

			$response = $this->client->delete($uri, $this->requestData);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0) {
				return true;
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

	//注销个人账户（按照第三方用户ID注销）
	public function accountsDeleteByThirdId($thirdPartyUserId) {
		try {
			$data = ['query' => ['thirdPartyUserId' => $thirdPartyUserId]];
			$uri  = $this->getUri(__FUNCTION__);

			$response = $this->client->get($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data'])) {
				return true;
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

	//设置签署密码
	public function accountsSetSignPwd($accountId, $password) {
		try {
			$uri = $this->getUri(__FUNCTION__, '/'.$accountId.'/');

			$data = ["password" => md5($password)];

			$response = $this->client->get($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data'])) {
				return true;
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
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
		try {

			$data = empty($deadline) ? [] : ['deadline' => $deadline];
			$uri  = $this->getUri(__FUNCTION__, '/'.$accountId);

			$response = $this->client->delete($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data'])) {
				return true;
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}

}