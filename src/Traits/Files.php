<?php

namespace ESign\Traits;

use ESign\Service\File;
use ESign\Urls;

/**
 * 文件模板API
 *
 * @package ESign\Traits
 */
trait Files
{
	/**
	 * 通过上传方式创建文件
	 *
	 * @param  string  $contentMd5  先计算文件md5值，在对该md5值进行base64编码
	 * @param  string  $contentType  目标文件的MIME类型，支持：
	 *                                        （1）application/octet-stream
	 *                                        （2）application/pdf
	 *                                注意，后面文件流上传的Content-Type参数要和这里一致，不然就会有403的报错
	 * @param  bool  $convert2Pdf  是否转换成pdf文档，默认false，代表不做转换。转换是异步行为，如果指定要转换，需要调用查询文件信息接口查询状态，转换完成后才可使用。
	 *                                注意：如果本身就是PDF文件，该参数必须传false，否则在【通过模板创建文件】的时候不能填充内容
	 * @param  string  $fileName  文件名称（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）
	 *                                注意：
	 *                                （1）该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
	 *                                （2）该字段建议直接传入pdf文件，其他类型文件建议本地自行转换成pdf，避免通过接口格式转换引起的格式错误、耗时久等问题。
	 * @param  int  $fileSize  文件大小，单位byte
	 * @param  string  $accountId  所属账号id，即个人账号id或机构账号id，如不传，则默认属于对接平台
	 *
	 * @return bool|array
	 *                fileId 文件Id
	 *                uploadUrl 文件直传地址, 可以重复使用，但是只能传一样的文件，有效期一小时
	 */
	public function GetUploadUrl(string $contentMd5, string $contentType, bool $convert2Pdf, string $fileName, int $fileSize, string $accountId = '') {
		$data = [
			'contentMd5'  => $contentMd5,
			'contentType' => $contentType,
			'convert2Pdf' => $convert2Pdf,
			'fileName'    => $fileName,
			'fileSize'    => $fileSize,
		];
		if ($accountId) {
			$data['accountId'] = $accountId;
		}

		$uri = Urls::Files(__FUNCTION__);

		return $this->client->post($uri, array_merge($this->requestData, $data));
	}

	public function UploadFile(string $filePath, string $contentType, bool $convert2Pdf, string $accountId = '') {
		$filePath = realpath($filePath);
		$contentMd5 = File::Base64Md5($filePath);
		$fileName   = basename($filePath);
		$fileSize   = filesize($filePath);

		$result = $this->GetUploadUrl($contentMd5, $contentType, $convert2Pdf, $fileName, $fileSize, $accountId);
		if (!$result) {
			return false;
		}

		if ($this->client->putContent($result['uploadUrl'], $contentMd5, $filePath)) {
			return $result['fileId'];
		}
		return false;
	}

	/**
	 * 通过上传方式创建模板
	 *
	 * @param  string  $contentMd5  先计算文件md5值，在对该md5值进行base64编码
	 * @param  string  $contentType  目标文件的MIME类型，支持：
	 *                                        （1）application/octet-stream
	 *                                        （2）application/pdf
	 *                                注意，后面文件流上传的Content-Type参数要和这里一致，不然就会有403的报错
	 * @param  bool  $convert2Pdf  是否转换成pdf文档，默认false，代表不做转换。转换是异步行为，如果指定要转换，需要调用查询文件信息接口查询状态，转换完成后才可使用。
	 *                                注意：如果本身就是PDF文件，该参数必须传false，否则在【通过模板创建文件】的时候不能填充内容
	 * @param  string  $fileName  文件名称（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）
	 *                                注意：
	 *                                （1）该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
	 *                                （2）该字段建议直接传入pdf文件，其他类型文件建议本地自行转换成pdf，避免通过接口格式转换引起的格式错误、耗时久等问题。
	 *
	 * @return bool|array
	 *                fileId 文件Id
	 *                uploadUrl 文件直传地址, 可以重复使用，但是只能传一样的文件，有效期一小时
	 */
	public function DocCreateByUploadUrl(string $contentMd5, string $contentType, bool $convert2Pdf, string $fileName, int $fileSize, string $accountId) {
		$data = [
			'contentMd5'  => $contentMd5,
			'contentType' => $contentType,
			'convert2Pdf' => $convert2Pdf,
			'fileName'    => $fileName,
			'fileSize'    => $fileSize,
			'accountId'   => $accountId,
		];

		$uri = Urls::Files(__FUNCTION__);

		return $this->client->post($uri, array_merge($this->requestData, $data));
	}

	/**
	 * 添加输入项组件
	 *
	 * @param $templateId
	 * @param $componentId
	 * @param $componentKey
	 * @param $componentType
	 * @param $context
	 *
	 * @return bool|array 添加/编辑的输入项组件id列表
	 */
	public function DocCreateComponents($templateId, $componentId, $componentKey, $componentType, $context) {
		$data = [
			'structComponent' => [
				'type'    => $componentType,
				'context' => $context
			]
		];
		if ($componentId) {
			$data['structComponent']['id'] = $componentId;
		}
		if ($componentId) {
			$data['structComponent']['key'] = $componentKey;
		}

		$uri = Urls::Files(__FUNCTION__, $templateId);

		return $this->client->post($uri, array_merge($this->requestData, $data));
	}
	//

	/**
	 * 删除输入项组件
	 *
	 * @param $templateId
	 * @param $ids
	 *
	 * @return mixed
	 */
	public function DocDeleteComponents($templateId, $ids) {
		$uri = Urls::Files(__FUNCTION__, [$templateId, $ids]);

		return $this->client->delete($uri, $this->requestData);
	}

	/**
	 * 查询模板详情/下载模板
	 *
	 * @param $templateId
	 *
	 * @return mixed
	 */
	public function DocTemplates($templateId) {
		$uri = Urls::Files(__FUNCTION__, $templateId);

		return $this->client->get($uri, $this->requestData);
	}

	/**
	 * 查询模板文件上传状态
	 *
	 * @param $templateId
	 *
	 * @return bool|array
	 *                templateId    模板id
	 *                templateName    模板名称
	 *                templateFileStatus    "模板文件上传状态。
	 *                                0-未上传
	 *                                1-未转换成PDF
	 *                                2-已上传成功
	 *                                3-已转换成PDF"
	 *                fileSize    模板文件大小
	 *                createTime    创建时间
	 *                updateTime    更新时间
	 */
	public function DocGetBaseInfo($templateId) {
		$uri = Urls::Files(__FUNCTION__, $templateId);

		return $this->client->get($uri, $this->requestData);
	}

	/**
	 * 通过模板创建文件
	 *
	 * @param $templateId 模板编号
	 *                    （1）正式环境可通过e签宝网站->企业模板下创建和查询
	 *                    （2）通过上传方式方式创建模板接口获取模板id和上传链接，文件流上传文件成功之后，模板id可用于这里
	 * @param $name 文件名称（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）；
	 *                注意：该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
	 * @param $ids 输入项填充内容，key:value 传入；可使用输入项组件id+填充内容，也可使用输入项组件key+填充内容方式填充
	 *                注意：E签宝官网获取的模板id，在通过模板创建文件的时候只支持输入项组件id+填充内容
	 *
	 * @return bool|array
	 *                downloadUrl 文件下载地址，有效期一小时
	 *                fileId 文件id
	 *                fileName 文件名称
	 */
	public function CreateByTemplate($templateId, $name, $ids) {
		$data = [
			'name'             => $name,
			'simpleFormFields' => $ids,
			'templateId'       => $templateId,
		];
		$uri  = Urls::Files(__FUNCTION__);

		return $this->client->post($uri, array_merge($this->requestData, $data));
	}

	/**
	 * 查询文件详情/下载文件
	 * 查询文件详情，包括文件名称、大小、下载地址等
	 * /v1/files/{fileId}
	 *
	 * @param $fileId
	 *
	 * @return bool|array
	 *                fileId    文件id
	 *                name    文件名称
	 *                size    文件大小，单位byte
	 *                status    "文件上传状态
	 *                        0-文件未上传；
	 *                        1-文件上传中 ；
	 *                        2-文件上传已完成,；
	 *                        3-文件上传失败 ；
	 *                        4-文件等待转pdf ；
	 *                        5-文件已转换pdf ；
	 *                        6-加水印中；
	 *                        7-加水印完毕；
	 *                        8-文件转换中；
	 *                        9-文件转换失败"
	 *                downloadUrl    下载地址，一般有效期为1个小时
	 *                pdfTotalPages    pdf文件总页数,仅当文件类型为pdf时有值
	 */
	public function GetFiles($fileId) {
		$uri = Urls::Files(__FUNCTION__, $fileId);

		return $this->client->get($uri, $this->requestData);
	}

	/**
	 * 文件添加数字水印
	 * 批量文件添加数字水印，一份文件一次仅可添加一种样式的数字水印，建议文件中不要存在大量图片，因数字水印图片默认放在文件图片下方，使用数字水印APP扫描时，图片区域会看不到水印信息。 （如数字水印图片放在图片上方，则文件打印后，图片上方会显示出数字水印图片，起不到防伪作用，因此默认放在文件图片下方），打印时必须彩打，对硬件的要求：彩色打印机精度越高越好，推荐最高分辨率1200*1200dpi以上；纸张质量越好，效果越好；彩色打印机墨盒尽可能保证原墨；打印时，选项中必须勾选”打印背景和图像”。
	 * /v1/files/batchAddWatermark
	 *
	 * @param  array  $files  文件信息开始
	 *                         fileId    string    是    body
	 *                         watermarkInfo    object    是    水印信息
	 *                            contentType    int    是    "1：原图fileId ；2：原图base64；3：文字"
	 *                            content    string    是    水印内容：原图fileId/原图base64/文字， 1：文字最高32个字符；2：当传入内容为图片时，建议不超过200K，图片越大，添加时间越长；
	 *                            fontSize    int    否    当content传入的是文字时，可指定文字大小，范围10-50号， 默认40
	 *                            fontName    string    否    当content传入的是文字时，可指定字体，支持如下字体，黑体 simhei 、普惠体 puhuiti、 宋体 simsun，默认黑体
	 *                            imageHeight    int    否    当content传入的是文字时，可以指定生成的水印图片固定高，300-600，默认根据文字大小自适应水印图片高
	 *                            imageWidth    int    否    当content传入的是文字时，可以指定生成的水印图片固定宽，300-600，默认根据文字大小自适应水印图片宽
	 *                            rotationAngle    int    否    当content传入的是文字时，可指定生成的水印图片旋转角度 ，正数为顺时针旋转，负数为逆时针旋转
	 *                            scaling    int    否    当content传入的是图片时，可以缩放生成的水印图片比例， 默认是100%，原尺寸大小
	 *                            vmModel    int    否    生成的水印图片渲染模式： 1描边 、2实心 ，默认实心， 当content传入的是文字时，字体推荐：黑体、宋体
	 *                            lineWidths    int    否    生成的水印图片在描边模式下，选择描边的粗细。范围为1~4，默认为3
	 *                            alpha    int    否    生成的水印图片透明度，1-255 值越大，打印出的文件越容易肉眼辨识水印图片，默认135
	 *                            strength    int    否    生成的水印图片强度，240-253 ，值越大，打印出的文件越难肉眼辨识水印图片，默认240
	 *                         posBean    object    否    水印图片位置信息（不传默认所有页平铺，传posPage不传x，y在指定页平铺，传x，y指定位置）
	 *                            posPage    string    否    页码信息，默认全部文件，可以','或'-'分割
	 *                            posX    float    否    默认平铺全部
	 *                            posY    float    否    默认平铺全部
	 * @param  string  $notifyUrl  水印图片全部添加完成回调地址
	 * @param  string  $thirdOrderNo  三方流水号（唯一），有回调必填
	 *
	 * @return mixed
	 */
	public function BatchAddWatermark(array $files, string $notifyUrl = '', string $thirdOrderNo = '') {
		$data = [
			'files' => $files
		];
		if ($notifyUrl) {
			$data['notifyUrl'] = $notifyUrl;
		}
		if ($thirdOrderNo) {
			$data['thirdOrderNo'] = $thirdOrderNo;
		}

		$uri = Urls::Files(__FUNCTION__);

		return $this->client->post($uri, array_merge($this->requestData, $data));
	}
}