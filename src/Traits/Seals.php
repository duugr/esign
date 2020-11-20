<?php


namespace ESign\Traits;

/**
 * 印章服务API
 *
 * @package ESign\Traits
 */
trait Seals
{
	//创建个人模板印章
	public function personalTemplate(){}
	//创建机构模板印章
	public function officialTemplate(){}
	//创建个人/机构图片印章
	public function image(){}

	//查询个人印章
	public function GetPersonal(){}
	//查询机构印章
	public function GetOfficial(){}

	//删除个人印章
	public function RemovePersonal(){}
	//删除机构印章
	public function RemoveOfficial(){}
}