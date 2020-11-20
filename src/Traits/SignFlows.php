<?php


namespace ESign\Traits;


use ESign\Util\Http;

/**
 * 签署服务API
 *
 * @package ESign\Traits
 */
trait SignFlows
{
	//创建签署流程
	public function GenerateSignFlows($upsloadUrl) {
		$aar            = [
			'businessScene' => "test",
			'configInfo'    => [
				'noticeType' => '1'
			]
		];
		$data           = json_encode($aar);
		$until          = new Until();
		$return_content = Http::DoPost($upsloadUrl, $data, $this->appId, $this->token);
		var_dump("创建签署流程：".$return_content);
		$result = (array) json_decode($return_content);
		$data2  = $result['data'];
		//flowId作为$data2的第一个关键字
		$flowId = $data2->flowId;
		echo $flowId;
		return $flowId;
	}
}