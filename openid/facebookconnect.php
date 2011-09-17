<?php
namespace app\openid;
 
class FacebookConnect {

	static public $appId;
	static public $appSecret;
	
	/**
	 * Returns the cookie data as an assoc. Returns <null> if no valid cookie was found.
	 */
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
	 */
	static public function getUserInfo( )
	{
		$cookie = static::getCookie();
		if ( $cookie == null )
			return null;

		$userinfo = file_get_contents('https://graph.facebook.com/me?access_token='.$cookie['access_token']);
		if ( $userinfo == false )
			return null;
		
		return json_decode($userinfo);
	}

}