<?php


namespace ESign\Service;


class File
{
	/**
	 * 获取文件的Content-MD5
	 * 原理：1.先计算MD5加密的二进制数组（128位）
	 *      2.再对这个二进制进行base64编码（而不是对32位字符串编码）
	 */
	static function Base64Md5($filePath)
	{
		//获取文件MD5的128位二进制数组
		$md5file = md5_file($filePath, true);
		//计算文件的Content-MD5
		return base64_encode($md5file);;
	}
}