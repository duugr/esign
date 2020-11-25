<?php


namespace ESign;

class Urls
{
	const AccessToken = '/v1/oauth2/access_token';

	public static function Accounts($path, $delimiter = '/') {
		switch ($path) {
			case 'accountsGetByAccountId':
			case 'accountsUpdateByAccountId':
			case 'accountsDeleteByAccountId':
				return str_replace('{accountId}', $delimiter, 'accounts/{accountId}');
			default:
				return static::getUri($path, $delimiter);
		}
	}

	public static function Org($path, $delimiter = '/') {
		switch ($path) {
			case 'organizationsGetByOrgId':
			case 'organizationsUpdateByOrgId':
			case 'organizationsDeleteByOrgId':
				return str_replace('{orgId}', $delimiter, 'organizations/{orgId}');
			default:
				return static::getUri($path, $delimiter);
		}
	}

	public static function Seals($path, $delimiter = '/') {
		switch ($path) {
			case 'CreatePersonal':
				return str_replace('{accountId}', $delimiter, 'accounts/{accountId}/seals/personaltemplate');
			case 'CreateOfficial':
				return str_replace('{orgId}', $delimiter, 'organizations/{orgId}/seals/officialtemplate');
			case 'CreateImage':
				return str_replace('{accountId}', $delimiter, 'accounts/{accountId}/seals/image');
			case 'GetPersonal':
				return str_replace('{accountId}', $delimiter, 'accounts/{accountId}/seals');
			case 'GetOfficial':
				return str_replace('{accountId}', $delimiter, 'organizations/{orgId}/seals');
			case 'RemovePersonal':
				return str_replace(['{accountId}', '{sealId}'], $delimiter, 'accounts/{accountId}/seals/{sealId}');
			case 'RemoveOfficial':
				return str_replace(['{accountId}', '{sealId}'], $delimiter, 'organizations/{orgId}/seals/{sealId}');
			default:
				return static::getUri($path, $delimiter);
		}
	}

	public static function Files($path, $delimiter = '/') {
		switch ($path) {
			case 'GetUploadUrl':
				return 'files/getUploadUrl';
			case 'DocCreateByUploadUrl':
				return 'docTemplates/createByUploadUrl';
			case 'DocCreateComponents':
				return str_replace('{templateId}', $delimiter, 'docTemplates/{templateId}/components');
			case 'DocDeleteComponents':
				return str_replace(['{templateId}', '{ids}'], $delimiter, 'docTemplates/{templateId}/components/{ids}');
			case 'DocTemplates':
				return str_replace('{templateId}', $delimiter, 'docTemplates/{templateId}');
			case 'DocGetBaseInfo':
				return str_replace('{templateId}', $delimiter, 'docTemplates/{templateId}/getBaseInfo');
			case 'CreateByTemplate':
				return 'files/createByTemplate';
			case 'GetFiles':
				return str_replace('{fileId}', $delimiter, 'files/{fileId}');
			case 'BatchAddWatermark':
				return 'files/batchAddWatermark';
			default:
				return static::getUri($path, $delimiter);
		}
	}

	public static function Flows($path, $delimiter = '/') {
		switch ($path) {
			case 'CreateFlowOneStep':
				return '/api/v2/signflows/createFlowOneStep';

			case 'CreateSignFlows':
				return 'signflows';
			case 'GetSignFlows':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}');
			case 'StartSignFlows':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}/start');
			case 'RevokeSignFlows':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}/revoke');
			case 'ArchiveSignFlows':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}/archive');
			case 'VoucherSignFlows':
				return str_replace('{flowId}', $delimiter, '/api/v2/signflows/{flowId}/getVoucher');

			case 'CreateDocuments':
			case 'DeleteDocuments':
			case 'GetDocuments':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}/documents');

			case 'CreateAttachments':
			case 'GetAttachments':
			case 'DeleteAttachments':
				return str_replace('{flowId}', $delimiter, 'signflows/{flowId}/attachments');
		}
	}

	static function getUri($path, $delimiter = '/'): string {
		return preg_replace_callback('/(^.*?)(?=[A-Z])([A-Z])/u', function ($matches) use ($delimiter) {
			return lcfirst($matches[1]).$delimiter.lcfirst($matches[2]);
		}, $path);
	}
}
