<?php

namespace app\openid;

use row\Output;

class FacebookConnect {

	static public $appId;
	static public $appSecret;
	static public $redirectURI;

	static public function requestURL($type, $params = array()) {
		$O = Output::$class;

		switch ( $type ) {
			// login -- no params
			case 'login':
				$params['client_id'] = static::$appId;
				$params['redirect_uri'] = $O::url(static::$redirectURI, array('absolute' => true));
				break;

			// authenticate -- params: `code`
			case 'authenticate':
				$params['client_id'] = static::$appId;
				$params['client_secret'] = static::$appSecret;
				$params['redirect_uri'] = $O::url(static::$redirectURI, array('absolute' => true));
				break;

			// user -- params: `access_token`
			case 'user':
				break;

			default:
				return '';
		}

		$urls = array(
			'login' => 'https://www.facebook.com/dialog/oauth',
			'authenticate' => 'https://graph.facebook.com/oauth/access_token',
			'user' => 'https://graph.facebook.com/me',
		);

		return $urls[$type] . '?' . http_build_query($params);
	}

	static public function user($params) {
		if ( is_string($params) ) {
			// params = access token
			$params = array(
				'access_token' => $params,
			);
		}

		// facebook url
		$url = static::requestURL(__FUNCTION__, $params);

		// request
		$user = @file_get_contents($url);

		// response
		if ( $user ) {
			// format: json
			$user = json_decode($user);

			// valid json
			if ( $user ) {
				// stdClass object
				return $user;
			}
		}
	}

	static public function authenticate($code = null) {
		// code from global request?
		if ( !$code ) {
			// no code!?
			if ( empty($_GET['code']) ) {
				return;
			}

			// default location from facebook redirect
			$code = $_GET['code'];
		}

		// facebook url
		$url = static::requestURL(__FUNCTION__, array(
			'code' => $code,
		));

		// request
		$auth = @file_get_contents($url);

		// response
		if ( $auth ) {
			// format: query string
			parse_str($auth, $response);

			// param: access_token
			if ( $response && isset($response['access_token']) ) {
				// get user info
				return static::user($response);
			}
		}
	}

	static public function login( $redirect = true ) {
		$url = static::requestURL(__FUNCTION__);

		if ( $redirect ) {
			$app = $GLOBALS['Application'];
			return $app->_redirect($url);
		}

		return $url;
	}

	/**
	 * Returns the cookie data as an assoc. Returns <null> if no valid cookie was found.
	 *
	static private function getCookie ( )
	{
		$args = array();
		$cookieName = 'fbs_' . static::$appId;
		if ( !isset($_COOKIE[$cookieName]) )
			return null;

		parse_str(trim($_COOKIE[$cookieName], '\\"'), $args);
		ksort($args);
		$payload = '';
		foreach ($args as $key => $value) {
			if ( $key != 'sig' )
			{
				$payload .= $key . '=' . $value;
			}
		}
		if ( !isset($args['sig']) )
			return null;

		if ( md5($payload . static::$appSecret) != $args['sig'] )
			return null;

		if ( !isset($args['access_token']) )
			return null;

		return $args;
	}

	static public function isLoggedIn ( )
	{
		return static::getCookie() == null;
	}

	/**
	 * Returns the information of the currently logged in user.
	 * If no user is logged in, null will be returned.
	 *
	static public function getUserInfo( )
	{
print_r($_COOKIE);
		$cookie = static::getCookie();
var_dump($cookie);
		if ( $cookie == null )
			return null;

		$userinfo = file_get_contents('https://graph.facebook.com/me?access_token='.$cookie['access_token']);
		if ( $userinfo == false )
			return null;

		return json_decode($userinfo);
	}
	/**/

}