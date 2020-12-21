<?php


namespace ESign\Traits;


use ESign\Urls;
use ESign\Util\Http;
use GuzzleHttp\Exception\RequestException;

/**
 * 签署服务API
 *
 * @package ESign\Traits
 */
trait Flows
{
    /**
     * 一步发起签署
     *
     * @param array $docs 待签文档信息，请把要签署的文档全部都通过该参数设置上
     *                        fileId 文档id
     *                        fileName 文件名称（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）；
     *                                    注意：该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
     *                        encryption 是否加密，0-不加密，1-加密，默认0
     *                                    设置的时候只有平台自动签署和签署方自动签署场景支持对加密文件盖章，手动签署区不支持
     *                        filePassword 文档密码, 如果encryption值为1, 文档密码不能为空，默认没有密码
     * @param array $flowInfo 流程基本信息
     *                            autoArchive    boolean    否    是否自动归档，默认 false
     *                            autoInitiate    boolean    否    是否自动开启，默认 false
     *                            businessScene    string    是    文件主题
     *                            contractRemind    int32    否    文件到期前，提前多少小时回调提醒续签，小时（时间区间：1小时——15天），默认不提醒
     *                            contractValidity    int64    否    文件有效截止日期,毫秒，默认不失效
     *                            flowConfigInfo    object    否    任务配置信息
     *                                noticeDeveloperUrl    string    否    通知开发者地址
     *                                noticeType    string    否    "通知方式，可选择多种通知方式，逗号分割，1-短信，2-邮件。 默认1
     *                                                            注：短信或者邮件获取到的签署链接，有效期默认30天；如果客户需要不通知，可以设置noticeType="""
     *                                redirectUrl    string    否    签署完成重定向地址
     *                                signPlatform    string    否    签署平台，可选择多种签署平台，逗号分割，1-开放服务h5，2-支付宝签 ，默认值1，2
     *                                redirectDelayTime    int    否    "签署完成重定向跳转延迟时间，默认3。
     *                                                            0-不展示签署完成结果页，签署完成直接跳转重定向地址
     *                                                            3-展示签署完成结果页，倒计时3秒后，自动跳转重定向地址
     *                                                            注：当redirectUrl不传的情况下，该字段无需传入，默认签署完成结果页不跳转"
     *                            initiatorAccountId    string    否    发起方账户id
     *                            initiatorAuthorizedAccountId    string    否    发起方主体id
     *                            remark    string    否    流程备注
     *                            signValidity    int64    否    "签署有效截止时间，毫秒，默认不失效
     *                                                        注：超过签署有效截止时间，则无法继续签署。"
     * @param array $signers 签署方信息
     *                            platformSign    boolean    否    "是否平台自动签署，默认false
     *                                                        false-为对接平台的用户签署
     *                                                        true-平台方自动签署"
     *                            signOrder    int32    否    签署方签署顺序，默认1,且不小于1，顺序越小越先处理
     *                            signerAccount    object    否    签署方账号信息（平台方自动签署时，无需传入该参数）
     *                                signerAccountId    string    否    "签署操作人个人账号标识，即操作本次签署的个人
     *                                                            注：平台用户自动签署时，该参数需要传入签署主体账号id"
     *                                authorizedAccountId    string    否    "签约主体账号标识，即本次签署对应任务的归属方，默认是签署操作人个人
     *                                                                注：平台用户自动签署时，无需传入该参数"
     *                            signfields    array    是    签署文件信息
     *                                autoExecute    boolean    否    是否自动执行，默认false（如果自动签署，必须设置为true）
     *                                actorIndentityType    string    否    "机构签约类别，当签约主体为机构时必传：2-机构盖章（如果是平台方自动签署，该字段必传，传入2）；
     *                                                                注：
     *                                                                1、签署主体是个人时，无需传入该参数；
     *                                                                2、平台用户自动签署时，无需传入该参数"
     *                                fileId    string    是    文件fileId
     *                                sealId    string    否    "印章id 需要注意的是：如果开通了实名签，企业签署这种场景不支持指定印章，个人签署场景是支持的"
     *                                sealType    string    否    签署方式，个人签署时支持多种签署方式，0-手绘签名  ，1-模板印章签名，多种类型时逗号分割，为空不限制
     *                                signType    int32    否    签署类型，0-不限，1-单页签署，2-骑缝签署，默认1
     *                                posBean    object    否    "签署区位置信息 。
     *                                                    signType为0时，本参数无效； signType为1时, 页码和XY坐标不能为空,；
     *                                                    signType为2时, 页码和Y坐标不能为空"
     *                                    posPage    string    否    页码信息，当签署区signType为2时, 页码可以'-'分割指定页码范围, 其他情况只能是数字
     *                                    posX    float    否    x坐标
     *                                    posY    float    否    y坐标
     *                                width    int32    否    签署区的宽度
     *                                signDateBeanType    int 32    否    是否需要添加签署日期，0-禁止 1-必须 2-不限制，默认0
     *                                signDateBean    object    否    签署日期信息
     *                                    fontSize    int32    否    签章日期字体大小
     *                                    format    string    否    签章日期格式，yyyy年MM月dd日
     *                                    posPage    int32    否    页码信息，autoExecute是否自动执行为true时，并且需要展示签署日期，则需要指定日期盖章页码 ，默认当前页
     *                                    posX    float    否    x坐标 ，autoExecute是否自动执行为true时，并且需要展示签署日期，则需要指定日期盖章位置 ，默认为0
     *                                    posY    float    否    y坐标 ，autoExecute是否自动执行为true时，并且需要展示签署日期，则需要指定日期盖章位置 ，默认为0
     *                                thirdOrderNo    string    否    第三方流水号
     * @param array $copiers 抄送人人列表
     *                            copierAccountId 参与人account id
     *                            copierIdentityAccountType 参与主体类型, 0-个人, 1-企业, 默认个人
     *                            copierIdentityAccountId 参与主体账号id
     * @param array $attachments 附件信息
     *                                fileId    附件Id
     *                                attachmentName    附件名称
     *
     * @return false|mixed
     *
     */
    public function CreateFlowOneStep(array $docs, array $flowInfo, array $signers, array $copiers = [], array $attachments = [])
    {
        $data['json'] = [
            'docs'     => $docs,
            'flowInfo' => $flowInfo,
            'signers'  => $signers
        ];
        if (!empty($copiers)) {
            $data['copiers'] = $copiers;
        }
        if (!empty($attachments)) {
            $data['attachments'] = $attachments;
        }

        $uri = Urls::Flows(__FUNCTION__);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    // 签署流程
    // 签署流程创建 POST signflows
    /**
     * @param string $businessScene 文件主题
     * @param bool $autoArchive 是否自动归档，默认false；
     *                             如设置为true，则在调用签署流程开启后，当所有签署人签署完毕，系统自动将流程归档，状态变为“已完成”状态；
     *                             如设置为false，则在调用流程开启后，需主动调用签署流程归档接口，将流程状态变更为“已完成”；已完成的流程才可下载签署后的文件
     * @param array $configInfo 任务配置信息
     * @param int $contractValidity 文件有效截止日期,毫秒，默认不失效；若时间到了该参数设置的时间，则会触发【流程文件过期通知】
     * @param int $contractRemind 文件到期前，提前多少小时回调提醒续签，小时（时间区间：1小时——15天），默认不提醒；若时间到了该参数设置的时间，则会触发【流程文件过期前通知】
     * @param int $signValidity 签署有效截止日期,毫秒，默认不失效；
     *                             注：超过签署有效截止时间，则无法继续签署。
     *                             若时间到了该参数设置的时间，则会触发【流程结束回调通知】
     * @param string $initiatorAccountId 发起人账户id，即发起本次签署的操作人个人账号id；如不传，默认由对接平台发起
     * @param string $initiatorAuthorizedAccountId 发起方主体id，如存在个人代机构发起签约，则需传入机构id；如不传，则默认是对接平台
     *
     * @return mixed
     */
    public function CreateSignFlows(
        string $businessScene,
        bool $autoArchive = false,
        array $configInfo = [],
        int $contractValidity = 0,
        int $contractRemind = 0,
        int $signValidity = 0,
        string $initiatorAccountId = '',
        string $initiatorAuthorizedAccountId = ''
    )
    {
        $data = ['businessScene' => $businessScene];
        if ($autoArchive) {
            $data['autoArchive'] = $autoArchive;
        }
        if ($configInfo) {
            $data['configInfo'] = $configInfo;
        }
        if ($contractValidity) {
            $data['contractValidity'] = $contractValidity;
        }
        if ($contractRemind) {
            $data['contractRemind'] = $contractRemind;
        }
        if ($signValidity) {
            $data['signValidity'] = $signValidity;
        }
        if ($initiatorAccountId) {
            $data['initiatorAccountId'] = $initiatorAccountId;
        }
        if ($initiatorAuthorizedAccountId) {
            $data['initiatorAuthorizedAccountId'] = $initiatorAuthorizedAccountId;
        }

        $uri = Urls::Flows(__FUNCTION__);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 签署流程查询 GET signflows/{flowId}
     * 查询签署流程的详细信息，包括流程配置、签署状态等。
     *
     * @param $flowId
     *
     * @return mixed
     */
    public function GetSignFlows(string $flowId)
    {
        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, $this->requestData);
    }

    /**
     * 签署流程开启 PUT signflows/{flowId}/start
     * 开启签署流程，签署任务会自动按照设置开始流转。
     *
     * @param $flowId
     *
     * @return mixed
     */
    public function StartSignFlows(string $flowId)
    {
        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->put($uri, $this->requestData);
    }

    // 签署流程撤销 PUT signflows/{flowId}/revoke
    public function RevokeSignFlows(string $flowId, string $operatorId = '', string $revokeReason = '')
    {
        $data = [];
        if ($operatorId) {
            $data['operatorId'] = $operatorId;
        }
        if ($revokeReason) {
            $data['revokeReason'] = $revokeReason;
        }

        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->put($uri, $this->requestData);
    }

    /**
     * 签署流程归档 PUT signflows/{flowId}/archive
     * 手动归档签署流程，归档后所有资源均不可修改。归档前签署流程中的所有签署人必须都签署完成。如创建流程时设置了自动归档，则无需调用本接口，签署完成后系统会自动调用。
     *
     * @param string $flowId
     *
     * @return mixed
     */
    public function ArchiveSignFlows(string $flowId)
    {
        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->put($uri, $this->requestData);
    }

    /**
     * 流程签署数据存储凭据 GET /api/v2/signflows/{flowId}/getVoucher
     * 流程归档后，可获取本次签约的数据存储凭据
     *
     * @param string $flowId
     *
     * @return mixed
     */
    public function VoucherSignFlows(string $flowId)
    {
        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, $this->requestData);
    }


    // 流程文档

    /**
     * 流程文档添加 POST signflows/{flowId}/documents
     * 向流程中添加待签署文档，已经开启的流程不能再添加签署文档。文档必须先用文档管理接口创建，创建方式请参见文件管理接口文档。
     *
     * @param string $flowId 流程id
     * @param string $fileId 文档id
     * @param string $fileName 文件名称（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）；
     *                             注意：该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
     * @param int $encryption 是否加密，0-不加密，1-加密，默认0
     *                            注意：只支持编辑加密的PDF文档，且签署区设置的时候只有平台自动签署区和签署方自动签署区支持对加密文件盖章，手动签署区不支持
     * @param string $filePassword 文档密码, 如果encryption值为1, 文档密码不能为空，默认没有密码
     *
     * @return mixed
     */
    public function AddDocuments(string $flowId, string $fileId, string $fileName = '', int $encryption = 0, string $filePassword = '')
    {
        $docs = [
            'fileId' => $fileId,
        ];
        if ($fileName) {
            $docs['fileName'] = $fileName;
        }
        if ($encryption) {
            $docs['encryption'] = $encryption;
        }
        if ($filePassword) {
            $docs['filePassword'] = $filePassword;
        }
        $data['docs'][] = $docs;
        $uri            = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 流程文档删除 DELETE signflows/{flowId}/documents
     * 删除流程中指定的文档，流程开启后不可删除
     *
     * @param string $flowId 流程id
     * @param array $fileIds 文档id列表,多个id使用“，”分隔
     *
     * @return bool
     */
    public function DeleteDocuments(string $flowId, array $fileIds)
    {
        $data = [
            'query' => [
                'fileIds' => implode(',', $fileIds),
            ]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->delete($uri, array_merge($this->requestData, $data));
    }
    //

    /**
     * 流程文档下载 GET signflows/{flowId}/documents
     * 流程归档后，查询和下载签署后的文件。
     *
     * @param string $flowId 流程id
     *
     * @return mixed
     */
    public function GetDocuments(string $flowId)
    {
        $data = [];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, array_merge($this->requestData, $data));
    }

    // 流程附件

    /**
     * 流程附件添加
     * 为流程添加附件, 附件必须先用文档管理接口创建，附件无需签署，只作为签署过程中的参考，比如录音、视频, 图片, 文档等。
     *
     * @param $flowId 流程id
     * @param $name 附件名称，（必须带上文件扩展名，不然会导致后续发起流程校验过不去 示例：合同.pdf ）；
     *                注意：该字段的文件后缀名称和真实的文件后缀需要一致。比如上传的文件类型是word文件，那该参数需要传“xxx.docx”，不能是“xxx.pdf”
     * @param $fileId 附件Id
     *
     * @return bool
     */
    public function CreateAttachments(string $flowId, string $name, string $fileId)
    {
        $data = [
            'attachments' => [
                'attachmentName' => $name,
                'fileId'         => $fileId,
            ]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 流程附件列表 GET signflows/{flowId}/attachments
     * 查询流程关联的所有附件
     *
     * @param string $flowId 流程id
     *
     * @return mixed
     */
    public function GetAttachments(string $flowId, array $fileIds)
    {
        $data = [
            'query' => [
                'fileIds' => implode(',', $fileIds),
            ]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, array_merge($this->requestData, $data));
    }

    /**
     * 流程附件删除
     * 从流程中删除附件，流程开启前可以随意删除，流程开启后只能增加不能删除。
     *
     * @param string $flowId 流程id
     * @param array $fileIds 文档id列表,多个id使用“，”分隔
     *
     * @return bool
     */
    public function DeleteAttachments(string $flowId, array $fileIds)
    {
        $data = [
            'query' => [
                'fileIds' => implode(',', $fileIds),
            ]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->delete($uri, array_merge($this->requestData, $data));
    }

    // 流程签署区

    /**
     * 查询签署区列表 GET signflows/{flowId}/signfields
     * 查询流程签署区列表，可以查询指定指定id或者签署人所属的签署区
     *
     * @param string $flowId 流程id
     * @param string $accountId 账号id，默认所有签署人
     * @param string $signFieldIds 指定签署区id列表，逗号分割，默认所有签署区
     *
     * @return mixed
     */
    public function GetSignFields(string $flowId, string $accountId = '', string $signFieldIds = '')
    {
        $data = [
            'query' => [
                'accountId'    => $accountId,
                'signfieldIds' => $signFieldIds
            ]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, array_merge($this->requestData, $data));
    }

    /**
     * 添加平台自动盖章签署区 POST signflows/{flowId}/signfields/platformSign
     * 向指定流程中创建签署区，每个签署区视为一个任务，系统会自动按照流程流转。 签署区的添加必须在签署文档添加之后, 签署区信息内部包含签署文档信息（平台自动签无需指定签署人信息，默认签署人是对接的企业）。
     * 签署区创建完成，流程开启后，系统将自动完成“对接平台自动盖章签署区”的盖章，对接平台可全程无感完成本次签署。
     *
     * @param string $flowId 流程id
     * @param array $signFields 签署区列表数据
     *
     * @return mixed
     */
    public function SignFieldsPlatformSign(string $flowId, array $signFields)
    {
        $data = [
            'signfields' => $signFields
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 添加签署方自动盖章签署区 POST signflows/{flowId}/signfields/autoSign
     * （1）向指定流程中创建签署区，每个签署区视为一个任务，系统会自动按照流程流转。 签署区的添加必须在签署文档添加之后, 签署区信息内部包含签署人、签署文档信息。签署区创建完成，流程开启后，系统将自动完成“用户自动盖章签署区”的盖章。用户可全程无感完成本次签署。创建签署方自动盖章签署区前，需确定已完成账号静默签署授权 。
     * （2）签署方自动盖章的合同不符合电子签名法中对可靠的要求，仅适用于对法律效力要求不高的场景，或由对接平台方自行校验真实身份和真实意愿。
     * （3）静默签署对于签署方来说是无感知的，需要在签署前，由平台方与签署方签订授权协议，确保法律流程完善（线下线上签都可以，确保法律效力即可）（授权协议可参考https://qianxiaoxia.yuque.com/docs/share/cf2ff0e1-04d3-4bc3-8459-631613848490）。
     * （4）静默签署在出证时，无法直接从官网申请，需要申请人工出证（需要人工出证费用）。
     * （5）添加签署方自动盖章签署区接口适用于签署方用户添加签署区，不适用于平台方添加签署区。
     *
     * @param string $flowId 流程id
     * @param array $signFields 签署区列表数据
     *
     * @return mixed
     */
    public function SignFieldsAutoSign(string $flowId, array $signFields)
    {
        $data = [
            'signfields' => $signFields
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 添加手动盖章签署区 POST signflows/{flowId}/signfields/handSign
     * （1）向指定流程中创建签署区，每个签署区视为一个任务，系统会自动按照流程流转。 签署区的添加必须在签署文档添加之后, 签署区信息内部包含签署文档信息.签署区创建完成，流程开启后，通过获取签署地址接口，可获取用户手动签署链接，通过此链接可打开文件签署页面，进行人工确认签署。
     * （2）添加手动盖章签署区接口适用于签署方用户添加签署区，不适用于平台方添加签署区。
     *
     * @param string $flowId 流程id
     * @param array $signFields 签署区列表数据
     *
     * @return mixed
     */
    public function SignFieldsHandSign(string $flowId, array $signFields)
    {
        $data = [
            'signfields' => $signFields
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 删除签署区 DELETE signflows/{flowId}/signfields
     *
     * @param string $flowId 流程id
     * @param string $signFieldIds 签署区id列表，逗号分割
     *
     * @return bool
     */
    public function DeleteSignFields(string $flowId, string $signFieldIds): bool
    {
        $data = [
            'query' => ['signfieldIds' => $signFieldIds]
        ];
        $uri  = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->delete($uri, array_merge($this->requestData, $data));
    }

    // 流程签署人

    /**
     * 流程签署人列表 GET signflows/{flowId}/signers
     * 查询流程所有签署人列表。
     *
     * @param string $flowId 流程id
     *
     * @return bool|array
     * "signers":[{
     * "signOrder":1,
     * "signStatus":1,
     * "signerAccountId":"24c93459216945468fdf1d899c521910",
     * "signerAuthorizedAccountId":"2c7de24aff3340f5b944ebac49545b8e",
     * "signerAuthorizedAccountName":"深圳天谷信息科技有限公司",
     * "signerAuthorizedAccountRealName":true,
     * "signerAuthorizedAccountType":1,
     * "signerName":"孙中山",
     * "signerRealName":true
     * }]
     */
    public function Signers(string $flowId)
    {
        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, $this->requestData);
    }

    /**
     * 流程签署人催签 PUT signflows/{flowId}/signers/rushsign
     * 向当前轮到签署但还未签署的签署人发送催签提醒, 支持指定签署人发送催签提醒。
     * 被催签人accoutId是非必填的，该参数为空时，系统将向所有待签的签署人发送签署通知。
     *
     * @param string $flowId 流程id，该参数需放在请求地址里面
     * @param string $accountId 催签人账户id
     * @param string $noticeTypes 通知方式，逗号分割，1-短信，2-邮件 3-支付宝 4-钉钉，默认按照走流程设置
     * @param string $rushSignAccountId 被催签人账号id，为空表示：催签当前轮到签署但还未签署的所有签署人
     *
     * @return bool
     */
    public function SignersRushSign(string $flowId, string $accountId = '', string $noticeTypes = '', string $rushSignAccountId = '')
    {
        if ($accountId) {
            $data['accountId'] = $accountId;
        }
        if ($noticeTypes) {
            $data['noticeTypes'] = $noticeTypes;
        }
        if ($rushSignAccountId) {
            $data['rushsignAccountId'] = $rushSignAccountId;
        }

        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->put($uri, array_merge($this->requestData, $data));
    }

    /**
     * 获取签署地址 GET signflows/{flowId}/executeUrl
     * 流程开启后，获取指定签署人的签署链接地址，如仅传入签署人账号id，则获取的签署任务链接仅包含本人的签署任务；
     * 如同时签署人账号id+机构id，则获取的签署任务链接包含企业与个人的签署任务
     *
     * @param string $flowId 流程id，该参数需放在请求地址里面
     * @param string $accountId 签署人账号id
     * @param string $organizeId 默认为空，返回的任务链接仅包含签署人本人需要签署的任务；  传入0，则返回的任务链接包含签署人“本人+所有代签机构”的签署任务；  传入指定机构id，则返回的任务链接包含签署人“本人+指定代签机构”的签署任务
     * @param int $urlType 链接类型: 0-签署链接;1-预览链接;默认0
     * @param string $appScheme app Scheme app内对接必传
     *
     * @return bool|array
     *                shortUrl 短链地址（30天有效）
     *                url 长链地址(永久有效)
     */
    public function SignersExecuteUrl(string $flowId, string $accountId, string $organizeId = '', int $urlType = 0, string $appScheme = '')
    {
        $data = [
            'flowI' => $flowId,
            'query' => [
                'accountId' => $accountId
            ]
        ];
        if ($organizeId) {
            $data['query']['organizeId'] = $organizeId;
        }
        if ($urlType) {
            $data['urlType'] = $urlType;
        }
        if ($appScheme) {
            $data['query']['appScheme'] = $appScheme;
        }

        $uri = Urls::Flows(__FUNCTION__, $flowId);

        return $this->client->get($uri, array_merge($this->requestData, $data));
    }


    // 文本签

    /**
     * 平台方&平台用户文本签 POST dataSign
     * 传入待签文本原文，使用指定账号的数字证书进行加密，如不指定账号id，则使用对接平台数字证书对文本进行加密。
     *
     * @param string $data 待签文本
     * @param string $type 待签文本类型： PLATFORM，平台签; PLATFORM_USER，平台用户签
     * @param string $accountId 如果是需要使用用户的数字证书进行文本签署，则需要传入用户的账号id，如不传，默认使用平台的数字证书进行文本签署
     *
     * @return bool|array
     *                signResult 签署结果，请注意保存，后续可根据该参数进行验签
     *                signlogId 签署记录编号，请注意保存
     */
    public function DataSign(string $data, string $type, string $accountId = '')
    {
        $data = [
            'data' => $data,
            'type' => $type
        ];
        if ($accountId) {
            $data['accountId'] = $accountId;
        }

        $uri = Urls::Flows(__FUNCTION__);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 文本签验签 POST dataSign/verify
     * 传入待签文本原文、文本签署后的签名结果，验证文本是否被篡改、加密数字证书信息。
     *
     * @param string $data 文本签原文
     * @param string $signResult 文本签返回的签署结果
     *
     * @return bool|array
     *            signInfo
     *                cert 证书信息
     *                    owner string 所有者
     *                    serial string 序列号
     *                    startDate string 有效期开始时间
     *                    endDate string 有效期结束时间
     *                    issuerCN string 发布者名称
     *                signature 签名数据
     *                    modify string 是否篡改
     */
    public function DataSignVerify(string $data, string $signResult)
    {
        $data = [
            'data'       => $data,
            'signResult' => $signResult
        ];

        $uri = Urls::Flows(__FUNCTION__);

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * PDF文件验签 GET documents/{fileId}/verify
     * 如果针对本地保存的签署文件进行验签，则通过“上传方式创建文档”接口上传文件，获取fileid，再传入文件id，进行验签；如果是针对某一签署流程对应的某一签署后的文件进行验签，则可直接传入签署流程id+文件id，直接进行验签。
     *
     * @param $fileId 文件id,该参数需放在请求地址上，可参考请求示例
     * @param $flowId 流程id，需对已归档的签署流程进行验签
     *
     * @return bool|array
     *            signInfo
     *                cert 证书信息
     *                    owner string 所有者
     *                    serial string 序列号
     *                    startDate string 有效期开始时间
     *                    endDate string 有效期结束时间
     *                    issuerCN string 发布者名称
     *                signature 签名数据
     *                    modify string 是否篡改
     *                    timeFrom string 签署时间来源
     *                    signDate string 签署时间
     *                sealData string 印章数据
     */
    public function DocumentVerify($fileId, $flowId)
    {
        $data = [
            'query' => [
                'flowId' => $flowId
            ]
        ];

        $uri = Urls::Flows(__FUNCTION__, $fileId);

        return $this->client->get($uri, array_merge($this->requestData, $data));
    }
}