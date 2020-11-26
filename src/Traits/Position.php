<?php


namespace ESign\Traits;


use ESign\Urls;

trait Position
{
	/**
	 * 搜索关键字坐标
	 * 搜索关键字坐标，以关键字左下角为原点去查询坐标
	 *
	 * @param  string  $fileId  文档id
	 * @param  string  $keywords  关键字列表，逗号分割；
	 *                            注意要英文的逗号，不能中文逗号；
	 *                            关键字建议不要设置特殊字符，因Adobe无法识别部分符号，某些特殊字符会因解析失败从而导致搜索不到
	 *
	 * @return mixed
	 */
	public function SearchWords(string $fileId, string $keywords) {
		$data = [
			'query' => [
				'keywords' => $keywords,
			]
		];
		$uri  = Urls::Flows(__FUNCTION__, $fileId);

		return $this->client->get($uri, array_merge($this->requestData, $data));
	}

}