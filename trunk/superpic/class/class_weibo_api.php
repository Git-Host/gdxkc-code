<?php
/**
 * 微博接口类
 *
 * $Id: class_weibo_api.php 585 2011-07-20 06:25:49Z pp_zhangheng $
 */

/**
 * @see OAuthRequest
 */
require_once SYSTEM_ROOT . '/class/class_oauth.php';


abstract class weibo_api
{
    /**
     * 服务提供方
     */
    public $host = null;

    /**
     * 用户令牌
     */
    public $token = null;

    /**
     * 第三方应用客户端
     */
    public $consumer = null;

    /**
     * 数据格式
     */
    public $format = 'json';

    /**
     * 签名方法
     *
     * @var string
     */
    public $oAuthSignatureMethod = null;


    public function weibo_api()
    {
    }

    public function init($consumerKey = null, $consumerSecret = null, $accessToken = null, $accessSecret = null)
    {
        $this->oAuthSignatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
        $this->consumer = new OAuthConsumer($consumerKey, $consumerSecret);

        if (!empty($accessToken) && !empty($accessSecret)) {
            $this->token = new OAuthConsumer($accessToken, $accessSecret);
        }
    }

    public abstract function getRequestTokenURL(); // 获得临时令牌的URL
    public abstract function getAuthorizeURL(); // 获得授权的URL
    public abstract function getAccessTokenURL(); // 获得访问令牌的URL

    /**
     * 获得临时令牌
     *
     * @param string $callback 回调URL
     * @return string
     */
    public function getRequestToken($callback)
    {
        $request = $this->oAuthRequest($this->getRequestTokenURL(), 'GET', array('oauth_callback' => $callback));
		$token = OAuthUtil::parse_parameters($request);
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);

        return $token;
    }

    /**
     * 设置访问令牌
     *
     * @param string $oAuthToken
     * @param string $oAuthTokenSecret
     * @return void
     */
    public function setAccessToken($oAuthToken, $oAuthTokenSecret)
    {

    }

    /**
     * 获得访问令牌
     *
     * @param string
     */
    public function getAccessToken($oAuthVerifier = false, $oAuthToken = false)
    {
        $parameters = array();
        if (!empty($oAuthVerifier)) {
            $parameters['oauth_verifier'] = $oAuthVerifier;
        }

		$this->token = new OAuthConsumer($oAuthToken['oauth_token'], $oAuthToken['oauth_token_secret']);
		$request = $this->oAuthRequest($this->getAccessTokenURL(), 'GET', $parameters);
		$token = OAuthUtil::parse_parameters($request);
        if (!isset($token['oauth_token']) || !isset($token['oauth_token_secret'])) {
            return false;
        }

        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);

        return $token;
	}

    public function jsonDecode($response, $assoc = true)
    {
		$response  = preg_replace('/[^\x20-\xff]*/', "", $response);
		$jsonArray = json_decode($response, $assoc);
		if (!is_array($jsonArray)) {
            return array();
			// throw new Exception('格式错误!');
		}

        return $jsonArray;
	}

    /**
     * 重新封装的get请求.
     * @return mixed
     */
    public function get($url, $parameters = array())
    {
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json') {
            return $this->jsonDecode($response, true);
		}
        return $response;
	}

	 /**
     * 重新封装的post请求.
     * @return mixed
     */
    public function post($url, $parameters = array() , $multi = false)
    {
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi);
		if ($this->format === 'json') {
            return $this->jsonDecode($response, true);
        }
        return $response;
	}

	 /**
     * DELTE wrapper for oAuthReqeust.
     * @return mixed
     */
    public function delete($url, $parameters = array())
    {
        $response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json') {
            return $this->jsonDecode($response, true);
        }
        return $response;
    }

    /**
     * 发送请求的具体类
     * @return string
     */
    public function oAuthRequest($url, $method, $parameters, $multi = false)
    {
        if (strrpos($url, 'http://') !== false && strrpos($url, 'https://') !== false) {
            $url = "{$this->host}{$url}.{$this->format}";
		}
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
		$request->sign_request($this->oAuthSignatureMethod, $this->consumer, $this->token);
        switch ($method) {
            case 'GET':
                return $this->http($request->to_url(), 'GET');
            default:
                return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi );
        }
	}

    public function http($url, $method, $postfields = NULL , $multi = false)
    {
        //$https = 0;
        //判断是否是https请求
        if (strrpos($url, 'https://') !== false) {
            $port = 443;
            $version = '1.1';
            $host = 'ssl://' . $this->host;
        } else {
            $port = 80;
            $version = '1.0';
            $host = $this->host;
        }

        $header  = "$method $url HTTP/$version\r\n";
        $header .= "Host: {$this->host}\r\n";
        if ($multi) {
            $header .= "Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary . "\r\n";
        } else {
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        }

        if (strtolower($method) == 'post' ) {
            $header .= "Content-Length: ".strlen($postfields)."\r\n";
            $header .= "Connection: Close\r\n\r\n";
            $header .= $postfields;
        } else {
            $header .= "Connection: Close\r\n\r\n";
        }

        $ret = '';

        $fp = fsockopen($host, $port, $errno, $errstr, 10);

        if (!$fp) {
            $error = '建立sock连接失败';
            return $error;
            // throw new Exception($error);
        } else {

            fwrite ($fp, $header);
            while (!feof($fp)) {
                $ret .= fgets($fp, 4096);
            }
            fclose($fp);

            if (strrpos($ret, 'Transfer-Encoding: chunked')) {
                $info = explode("\r\n\r\n", $ret);
                $response = explode("\r\n", $info[1]);
                $t = array_slice($response, 1, -1);
                $returnInfo = implode('',$t);
            } else {
                $response   = explode("\r\n\r\n", $ret);
                $returnInfo = $response[1];
            }

            // 转成utf-8编码
            return iconv("utf-8", "utf-8//IGNORE", $returnInfo);
        }
    }
}