<?php


namespace ESign\Traits;


use ESign\Config;
use ESign\Util\Http;
use GuzzleHttp\Exception\RequestException;

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

	/**
	 * 一步发起签署
	 *
	 * @param  array  $docs
	 * @param  array  $flowInfo
	 * @param  array  $signers
	 * @param  array  $copiers
	 * @param  array  $attachments
	 *
	 * @return false|mixed
	 *
	 */
	public function createFlowOneStep(array $docs, array $flowInfo, array $signers, array $copiers = [], array $attachments = []) {
		$data = [
			'docs'=>$docs,
			'flowInfo'=>$flowInfo,
			'signers'=>$signers
		];
		if (!empty($copiers)) {
			$data['copiers'] = $copiers;
		}
		if (!empty($attachments)) {
			$data['attachments'] = $attachments;
		}

		try {
			$uri      = Config::Signflows(__FUNCTION__);

			$response = $this->client->post($uri, array_merge($this->requestData, $data));
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['flowId'])) {
				return $result['data']['flowId'];
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