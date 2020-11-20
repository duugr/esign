<?php

namespace ESign\Traits;

/**
 * 签署方账户API
 *
 * @package ESign\Traits
 */
trait Account
{
	//个人账户创建
	//个人账户修改(按照账号ID修改)
	//个人账户修改(按照第三方用户ID修改)

	//查询个人账户（按照账户ID查询）
	//查询个人账户（按照第三方用户ID查询）

	//注销个人账户（按照账号ID注销）
	//注销个人账户（按照第三方用户ID注销）

	//设置签署密码
	//机构账号创建
	//机构账号修改（按照账号ID修改）
	//机构账号修改（按照第三方机构ID修改）

	//查询机构账号（按照账号ID查询）
	//查询机构账号（按照第三方机构ID查询）

	//注销机构账号（按照账号ID注销）
	//注销机构账号（按照第三方机构ID注销）

	//设置静默签署
	//撤销静默签署

	/***
	证件类型，默认CRED_PSN_CH_IDCARD
	（1）CRED_PSN_CH_IDCARD 大陆身份证，默认值
	（2）CRED_PSN_CH_TWCARD 台湾来往大陆通行证
	（3）CRED_PSN_CH_MACAO 澳门来往大陆通行证
	（4）CRED_PSN_CH_HONGKONG 香港来往大陆通行证
	（5）CRED_PSN_FOREIGN 外籍证件
	（6）CRED_PSN_PASSPORT 护照
	（7）CRED_PSN_CH_SOLDIER_IDCARD 军官证
	（8）CRED_PSN_CH_SSCARD 社会保障卡
	（9）CRED_PSN_CH_ARMED_POLICE_IDCARD 武装警察身份证件
	（10）CRED_PSN_CH_RESIDENCE_BOOKLET 户口簿
	（11）CRED_PSN_CH_TEMPORARY_IDCARD 临时居民身份证
	（12）CRED_PSN_CH_GREEN_CARD 外国人永久居留证
	（13）CRED_PSN_SHAREHOLDER_CODE 股东代码证
	（14）CRED_PSN_POLICE_ID_CARD 警官证
	（15）CRED_PSN_UNKNOWN 未知类型
	 ***/
	// 设置创建个人信息
	public function setPersonAccount($userUniqueId, $name, $idNumber, $mobile,$idType = 'CRED_PSN_CH_IDCARD'){
		$arr = [
			'thirdPartyUserId' => "101111",
			'name'             => "xx",
			'idNumber'         => "xx",
			'idType'           => 'CRED_PSN_CH_IDCARD',
			'mobile'           => "xxx"
		];
	}
	// 设置创建企业信息
	public function setOrganizeAccount(){

	}

	public function GeneratePersonAccountID() {
	}

	public function GenerateOrganizeAccountID() {
	}

}