<?php


namespace ESign\Traits;


use ESign\Config;
use GuzzleHttp\Exception\RequestException;

trait Organizations
{
	/**
	 * 机构账号创建
	 *
	 * @param  string  $thirdPartyUserId  机构唯一标识，可传入第三方平台机构id、企业证件号、企业邮箱等如果设置则作为账号唯一性字段，相同id不可重复创建。（个人用户与机构的唯一标识不可重复）
	 * @param  string  $creator  创建人个人账号id（调用个人账号创建接口返回的orgId）
	 * @param  string  $name  机构名称
	 * @param  string  $idType  证件类型，默认CRED_ORG_USCC
	 * （1）CRED_ORG_USCC统一社会信用代码，默认值
	 * （2）CRED_ORG_CODE组织机构代码证
	 * （3）CRED_ORG_REGCODE工商注册号
	 * （4）CRED_ORG_BUSINESS_REGISTTATION_CODE工商登记证
	 * （5）CRED_ORG_TAX_REGISTTATION_CODE税务登记证
	 * （6）CRED_ORG_LEGAL_PERSON_CODE法人代码证
	 * （7）CRED_ORG_ENT_LEGAL_PERSON_CODE事业单位法人证书
	 * （8）CRED_ORG_SOCIAL_REG_CODE社会团体登记证书
	 * （9）CRED_ORG_PRIVATE_NON_ENT_REG_CODE民办非机构登记证书
	 * （10）CRED_ORG_FOREIGN_ENT_REG_CODE外国机构常驻代表机构登记证
	 * （11）CRED_ORG_GOV_APPROVAL政府批文
	 * （12）CODE_ORG_CUSTOM自定义
	 * （13）CRED_ORG_UNKNOWN未知证件类型
	 * @param  string  $idNumber  证件号
	 * @param  string  $orgLegalIdNumber  企业法定代表人证件号
	 * @param  string  $orgLegalName  企业法定代表人名称
	 *
	 * @return false|mixed
	 */
	public function organizationsCreateByThirdPartyUserId($thirdPartyUserId, $creator, $name, $idType = 'CRED_ORG_USCC', $idNumber, $orgLegalIdNumber, $orgLegalName) {
		$data = [
			'thirdPartyUserId' => $thirdPartyUserId,
			'creator'          => $creator,
			'name'             => $name,
			'idType'           => $idType,
			'idNumber'         => $idNumber,
			'orgLegalIdNumber' => $orgLegalIdNumber,
			'orgLegalName'     => $orgLegalName,
		];
		try {
			$uri      = $this->getUri(__FUNCTION__);
			$response = $this->client->post($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['orgId'])) {
				return $result['data']['orgId'];
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
	 * 机构账号修改（按照账号ID修改）
	 *
	 * @param  string  $orgId  机构账号id，该参数需放在请求地址里面，可以参考【请求示例】
	 * @param  string  $name  机构名称
	 * @param  string  $idType  证件类型，默认CRED_ORG_USCC
	 * （1）CRED_ORG_USCC统一社会信用代码，默认值
	 * （2）CRED_ORG_CODE组织机构代码证
	 * （3）CRED_ORG_REGCODE工商注册号
	 * （4）CRED_ORG_BUSINESS_REGISTTATION_CODE工商登记证
	 * （5）CRED_ORG_TAX_REGISTTATION_CODE税务登记证
	 * （6）CRED_ORG_LEGAL_PERSON_CODE法人代码证
	 * （7）CRED_ORG_ENT_LEGAL_PERSON_CODE事业单位法人证书
	 * （8）CRED_ORG_SOCIAL_REG_CODE社会团体登记证书
	 * （9）CRED_ORG_PRIVATE_NON_ENT_REG_CODE民办非机构登记证书
	 * （10）CRED_ORG_FOREIGN_ENT_REG_CODE外国机构常驻代表机构登记证
	 * （11）CRED_ORG_GOV_APPROVAL政府批文
	 * （12）CODE_ORG_CUSTOM自定义
	 * （13）CRED_ORG_UNKNOWN未知证件类型
	 * @param  string  $idNumber  证件号
	 * @param  string  $orgLegalIdNumber  企业法定代表人证件号
	 * @param  string  $orgLegalName  企业法定代表人名称
	 *
	 * @return false|mixed
	 */
	public function organizationsUpdateByOrgId($orgId, $name, $idType = 'CRED_ORG_USCC', $idNumber, $orgLegalIdNumber, $orgLegalName) {
		$data = [
			"name"             => $name,
			"idType"           => $idType,
			"idNumber"         => $idNumber,
			"orgLegalIdNumber" => $orgLegalIdNumber,
			"orgLegalName"     => $orgLegalName
		];

		try {

			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $orgId);

			$response = $this->client->put($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['orgId'])) {
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

	/**
	 * 机构账号修改（按照第三方机构ID修改）
	 *
	 * @param  string  $thirdPartyUserId  机构账号id，该参数需放在请求地址里面，可以参考【请求示例】
	 * @param  string  $name  机构名称
	 * @param  string  $idType  证件类型，默认CRED_ORG_USCC
	 * （1）CRED_ORG_USCC统一社会信用代码，默认值
	 * （2）CRED_ORG_CODE组织机构代码证
	 * （3）CRED_ORG_REGCODE工商注册号
	 * （4）CRED_ORG_BUSINESS_REGISTTATION_CODE工商登记证
	 * （5）CRED_ORG_TAX_REGISTTATION_CODE税务登记证
	 * （6）CRED_ORG_LEGAL_PERSON_CODE法人代码证
	 * （7）CRED_ORG_ENT_LEGAL_PERSON_CODE事业单位法人证书
	 * （8）CRED_ORG_SOCIAL_REG_CODE社会团体登记证书
	 * （9）CRED_ORG_PRIVATE_NON_ENT_REG_CODE民办非机构登记证书
	 * （10）CRED_ORG_FOREIGN_ENT_REG_CODE外国机构常驻代表机构登记证
	 * （11）CRED_ORG_GOV_APPROVAL政府批文
	 * （12）CODE_ORG_CUSTOM自定义
	 * （13）CRED_ORG_UNKNOWN未知证件类型
	 * @param  string  $idNumber  证件号
	 *
	 * @return false|mixed
	 */
	public function organizationsUpdateByThirdId($thirdPartyUserId, $name, $idType = 'CRED_ORG_USCC', $idNumber) {
		$data = [
			'query'    => ["thirdPartyUserId" => $thirdPartyUserId],
			"name"     => $name,
			"idType"   => $idType,
			"idNumber" => $idNumber
		];

		try {
			$uri = $this->getUri(__FUNCTION__);

			$response = $this->client->put($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['orgId'])) {
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

	/**
	 * 查询机构账号（按照账号ID查询）
	 *
	 * @param $orgId
	 *
	 * @return false|mixed
	 */
	public function organizationsGetByOrgId($orgId) {
		try {
			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $orgId);

			$response = $this->client->get($uri, $this->requestData);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['orgId'])) {
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
	//查询机构账号（按照第三方机构ID查询）
	public function organizationsGetByThirdId($thirdPartyUserId) {
		try {
			$data = ['query' => ['thirdPartyUserId' => $thirdPartyUserId]];
			$uri  =$this->getUri(__FUNCTION__);

			$response = $this->client->get($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['orgId'])) {
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

	//注销机构账号（按照账号ID注销）
	public function organizationsDeleteByOrgId($orgId) {
		try {
			$uri = sprintf("%s/%s", $this->getUri(str_replace('ESign\Traits\\', '', __TRAIT__)), $orgId);

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
	//注销机构账号（按照第三方机构ID注销）
	public function organizationsDeleteByThirdId($thirdPartyUserId) {
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



}