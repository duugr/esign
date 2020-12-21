<?php


namespace ESign\Traits;

use ESign\Urls;

/**
 * 印章服务API
 *
 * @package ESign\Traits
 */
trait Seals
{
    /**
     * 创建个人模板印章
     *
     * @param string $accountId
     * @param string $color 印章颜色
     *                        （1）RED-红色
     *                        （2）BLUE-蓝色
     *                        （3）BLACK-黑色
     * @param string $type 模板类型
     *                        SQUARE 正方形印章
     *                        RECTANGLE 矩形印章
     *                        BORDERLESS 无框矩形印章
     * @param int $height 印章高度, 默认95px
     * @param int $width 印章宽度, 默认95px
     * @param string $alias 印章别名
     *
     * @return bool|array
     *                fileKey 印章fileKey
     *                sealId 印章id
     *                url 印章下载地址, 有效时间1小时
     *                height 印章高度, 默认95px
     *                width 印章宽度, 默认95px
     */
    public function CreatePersonal(string $accountId, string $color, string $type, int $height = 0, int $width = 0, string $alias = '')
    {
        $uri  = Urls::Seals(__FUNCTION__, $accountId);
        $data = [
            'color' => $color,
            'type'  => $type,
        ];

        if ($height && $width) {
            $data['height'] = $height;
            $data['width']  = $width;
        }
        if ($alias) {
            $data['alias'] = $alias;
        }

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 创建机构模板印章
     *
     * @param string $orgId
     * @param string $color 印章颜色
     *                        （1）RED-红色
     *                        （2）BLUE-蓝色
     *                        （3）BLACK-黑色
     * @param string $type 模板类型 说明：以下印章样式支持长度为2-60个字符的机构名称
     *                        TEMPLATE_ROUND 圆章
     *                        TEMPLATE_OVAL 椭圆章
     * @param string $central 中心图案类型
     *                            STAR 圆形有五角星
     *                            NONE 圆形无五角星
     * @param int $height 印章高度, 默认95px
     * @param int $width 印章宽度, 默认95px
     * @param string $htext 横向文，可设置0-8个字，企业名称超出25个字后，不支持设置横向文
     * @param string $qtext 下弦文，可设置0-20个字，企业企业名称超出25个字后，不支持设置下弦文
     * @param string $alias 印章别名
     *
     * @return bool|array
     *                fileKey 印章fileKey
     *                sealId 印章id
     *                url 印章下载地址, 有效时间1小时
     *                height 印章高度, 默认95px
     *                width 印章宽度, 默认95px
     */
    public function CreateOfficial(string $orgId, string $color, string $type, string $central, int $height = 0, int $width = 0, string $htext = '', string $qtext = '', string $alias = '')
    {
        $uri  = Urls::Seals(__FUNCTION__, $orgId);
        $data = [
            'color'   => $color,
            'type'    => $type,
            'central' => $central,
        ];

        if ($height && $width) {
            $data['height'] = $height;
            $data['width']  = $width;
        }
        if ($alias) {
            $data['alias'] = $alias;
        }

        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    /**
     * 创建个人/机构图片印章
     *
     * @param string $accountId
     * @param string $data 印章数据，base64格式字符串，不包含格式前缀
     * @param int $height 印章高度, 个人默认95px, 机构默认159px
     * @param int $width 印章宽度, 个人默认95px, 机构默认159px
     * @param string $alias 印章别名
     * @param bool $transparentFlag 是否对图片进行透明化处理，默认 false 。
     *                                    对于有背景颜色的图片，建议进行透明化处理，否则可能会遮挡文字
     *
     * @return bool|array
     *                fileKey 印章fileKey
     *                sealId 印章id
     *                url 印章下载地址, 有效时间1小时
     *                height 印章高度, 默认95px
     *                width 印章宽度, 默认95px
     */
    public function CreateImage(string $accountId, string $data, int $height = 0, int $width = 0, string $alias = '', bool $transparentFlag = true)
    {
        $data = [
            'type' => 'BASE64', // 印章数据类型，BASE64：base64格式
            'data' => $data, // 印章数据，base64格式字符串，不包含格式前缀
        ];

        if ($height && $width) {
            $data['height'] = $height;
            $data['width']  = $width;
        }
        if ($alias) {
            $data['alias'] = $alias;
        }
        if ($transparentFlag) {
            $data['transparentFlag'] = $transparentFlag;
        }
        $uri = Urls::Seals(__FUNCTION__, $accountId);
        return $this->client->post($uri, array_merge($this->requestData, $data));
    }

    //查询个人印章
    public function GetPersonal(string $accountId, int $offset, int $size)
    {
        $data = [
            'query' => [
                'offset' => $offset,
                'size'   => $size,
            ]
        ];

        $uri = Urls::Seals(__FUNCTION__, $accountId);
        return $this->client->get($uri, array_merge($this->requestData, $data));
    }

    //查询机构印章
    public function GetOfficial(string $orgId, int $offset, int $size)
    {
        $data = [
            'query' => [
                'offset' => $offset,
                'size'   => $size,
            ]
        ];

        $uri = Urls::Seals(__FUNCTION__, $orgId);
        return $this->client->get($uri, array_merge($this->requestData, $data));
    }

    //删除个人印章
    public function RemovePersonal($accountId, $sealId)
    {
        $uri = Urls::Seals(__FUNCTION__, [$accountId, $sealId]);
        return $this->client->delete($uri, $this->requestData);
    }

    //删除机构印章
    public function RemoveOfficial($accountId, $sealId)
    {
        $uri = Urls::Seals(__FUNCTION__, [$accountId, $sealId]);
        return $this->client->delete($uri, $this->requestData);
    }
}