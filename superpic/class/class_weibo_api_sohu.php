<?php
/**
 * 搜狐微博API
 *
 * $Id$
 */

/**
 * @see weibo_api
 */
require_once SYSTEM_ROOT . '/class/class_weibo_api.php';

/**
 * 搜狐微博API操作类
 */
class weibo_api_sohu extends weibo_api
{
    /**
     * 搜狐微博API host
     *
     * @var string
     */
    public $host = 'api.t.sohu.com';


    /**
     * 单例模式
     *
     * @return tencent
     */
    public function &instance()
    {
		static $object;

		if (empty($object)) {
			$object = new self();
		}

		return $object;
	}

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $url = 'http://' . $this->host . '/users/show.' . $this->format;

	 	return $this->get($url, array('format' => $this->format));
    }

	/**
	 * 发布一条微博信息
	 *
	 * @param array $params
	 * @return array
	 */
    public function add($params = array())
    {
        $url = "http://{$this->host}/statuses/update.{$this->format}";

        $data = array('status' => urlencode($params['content']));

		return $this->post($url, $data);
    }

    /**
     * 发布一条图片微博
     */
    public function addPic($params = array())
    {
        $url = "http://{$this->host}/statuses/upload.{$this->format}";

        $data = array('status' => urlencode($params['content']),
                      'pic' => $params['pic'],
                );

		return $this->post($url, $data, true);
    }


    /**
     * 返回错误信息说明
     *
     * 官方说明地址
     * http://open.t.sina.com.cn/wiki/index.php/Help/error
     *
     * @param array $result
     * @return array
     */
    public function getErrorMessage(&$result)
    {
        // 状态码返回值说明
        $stateArray = array(200 => '执行成功', 304 => '没有数据返回', 400 => '请求数据不合法或者超过请求频率限制', 401 => '没有进行身份验证', 402 => '没有开通微博',
                            403 => '没有权限访问对应的资源', 404 => '请求的资源不存在', 500 => '服务器内部错误', 502 => '微博接口API关闭或正在升级', 503 => '服务端资源不可用');
        // 发表接口错误字段errcode说明
        $errorCodeArray = array(40028 => '内部接口错误(如果有详细的错误信息，会给出更为详细的错误提示)', 40033 => 'source_user或者target_user用户不存在',
                                40031 => '调用的微博不存在', 40036 => '调用的微博不是当前用户发布的微博', 40034 => '不能转发自己的微博',40038 => '不合法的微博',
                                40037 => '不合法的评论', 40015 => '该条评论不是当前登录用户发布的评论', 40017 => '不能给不是你粉丝的人发私信', 40019 => '不合法的私信',
                                40021 => '不是属于你的私信', 40022 => 'source参数(appkey)缺失', 40007 => '格式不支持，仅仅支持XML或JSON格式', 40009 => '图片错误，请确保使用multipart上传了图片',
                                40011 => '私信发布超过上限', 40012 => '内容为空', 40016 => '微博id为空', 40018 => 'ids参数为空', 40020 => '评论ID为空', 40023 => '用户不存在',
                                40024 => 'ids过多，请参考API文档', 40025 => '不能发布相同的微博', 40026 => '请传递正确的目标用户uid或者screen name', 40045 => '不支持的图片类型',
                                40008 => '图片大小错误，上传的图片大小上限为5M', 40001 => '参数错误，请参考API文档', 40002 => '不是对象所属者，没有操作权限', 40010 => '私信不存在',
                                40013 => '微博太长，请确认不超过140个字符', 40039 => '地理信息输入错误', 40040 => 'IP限制，不能请求该资源', 40041 => 'uid参数为空', 40042 => 'token参数为空',
                                40043 => 'domain参数错误', 40044 => 'appkey参数缺失', 40029 => 'verifier错误', 40027 => '标签参数为空', 40032 => '列表名太长，请确保输入的文本不超过10个字符',
                                40030 => '列表描述太长，请确保输入的文本不超过70个字符',40035 => '列表不存在', 40053 => '权限不足，只有创建者有相关权限',40054 => '参数错误，请参考API文档',
                                40059 => '插入失败，记录已存在', 40060 => '数据库错误，请联系系统管理员', 40061 => '列表名冲突', 40062 => 'id列表太长了', 40063 => 'urls是空的', 40064 => 'urls太多了',
                                40065 => 'ip是空值', 40066 => 'url是空值', 40067 => 'trend_name是空值', 40068 => 'trend_id是空值', 40069 => 'userid是空值', 40070 => '第三方应用访问api接口权限受限制',
                                40071 => '关系错误，user_id必须是你关注的用户', 40072 => '授权关系已经被删除', 40073 => '目前不支持私有分组', 40074 => '创建list失败', 40075 => '需要系统管理员的权限',
                                40076 => '含有非法词', 40084 => '提醒失败，需要权限', 40082 => '无效分类!', 40083 => '无效状态码', 40084 => '目前只支持私有分组',
                                40101 => 'Oauth版本号错误', 40102 => 'Oauth缺少必要的参数', 40103 => 'Oauth参数被拒绝', 40104 => 'Oauth时间戳不正确', 40105 => 'Oauth nonce参数已经被使用',
                                40106 => 'Oauth签名算法不支持', 40107 => 'Oauth签名值不合法', 40108 => 'Oauth consumer_key不存在', 40109 => 'Oauth consumer_key不合法', 40110 => 'Oauth Token已经被使用',
                                40111 => 'Oauth Token已经过期', 40112 => 'Oauth Token不合法', 40113 => 'Oauth Token不合法', 40114 => 'Oauth Pin码认证失败',
                                40301 => '已拥有列表上限', 40302 => '认证失败', 40303 => '已经关注此用户', 40304 => '发布微博超过上限', 40305 => '发布评论超过上限',
                                40306 => '用户名密码认证超过请求限制', 40307 => '请求的HTTP METHOD不支持', 40308 => '发布微博超过上限', 40309 => '密码不正确', 40314 => '该资源需要appkey拥有更高级的授权',
                    );

        $result['error_status']    = empty($result) ? 2 : 0;
        $result['error_message']   = '';
        $result['errcode_message'] = '';

        if (isset($result['code']) && isset($result['error']) && $result['error']) {
            $result['error_status']  = 1;
            $result['error_message'] = isset($stateArray[$result['code']]) ? $stateArray[$result['code']] : $result['code'];
            $result['errcode_message'] = $result['error'];
            $errorCode = explode(':', $result['error']);
            if (isset($errorCodeArray[$errorCode[0]])) {
                $result['errcode_message'] = $errorCodeArray[$errorCode[0]];
            }
        }

        return $result;
    }

    /**
     * 应用请求临时令牌URL
     *
     * @return string
     */
    public function getRequestTokenURL()
    {
        return 'http://' . $this->host . '/oauth/request_token';
    }

    /**
     * 用户请求授权令牌URL
     *
     * @return string
     */
    public function getAuthorizeURL($oAuthToken = null, $oAuthTokenSecret = null, $oAuthCallback = null)
    {
        $url = 'http://' . $this->host . '/oauth/authorize?oauth_token=' . $oAuthToken;

        if (!empty($oAuthCallback)) {
            $url .= '&oauth_callback=' . urlencode($oAuthCallback);
        }

        return $url;
    }

    /**
     * 服务提供方授权访问令牌URL
     *
     * @return string
     */
    public function getAccessTokenURL()
    {
        return 'http://' . $this->host . '/oauth/access_token';
    }
}