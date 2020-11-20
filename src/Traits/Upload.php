<?php


namespace ESign\Traits;

/**
 * 文件模板API
 *
 * @package ESign\Traits
 */
trait Upload
{
	//文件直传创建待签署文件
	public function getUploadUrl($upsloadUrl, $filePath)
	{
		$fileName    = basename($filePath);
		$fileSize    = filesize($filePath);
		$contentType = 'application/pdf';
		$contentMd5  = $this->getContentBase64Md5($filePath);
		$arr         = ['fileName' => $fileName, 'fileSize' => $fileSize, 'contentType' => $contentType, 'contentMd5' => $contentMd5];
		$data        = json_encode($arr);
		echo $data;
		$until          = new Until();
		$return_content = $until->doPost($upsloadUrl, $data, $this->appId, $this->token);
		var_dump("获取文件fileId以及文件上传地址：" . $return_content);
		$result = (array) json_decode($return_content, true);
		$data2  = $result['data'];
		echo "\n";
		echo '--------';
		return $data2;
	}

	public function UpLoadFile($uploadUrl, $filePath) {

	}
}