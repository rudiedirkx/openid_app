<?php

namespace app\controllers;

use app\specs\Controller;
use app\openid\OpenID;
use app\openid\FacebookConnect;
use app\models\UserOpenID;
use app\models\UserFacebookConnect;
use app\models\User;
use ErrorException;
use row\database\ModelException;
use row\auth\Session;

class pagesController extends Controller {

	protected function _init() {
		parent::_init();

		$this->aclAdd('login', array('restricted'));
		$this->aclAdd('not login', array('login'));

		$this->tpl->assign('users', User::all());

		FacebookConnect::$appId = '224547814295872';
		FacebookConnect::$appSecret = '09daa206a14ae04854f81b20eed246a4';
	}

	public function setvar($name, $value) {
		$this->user->variable($name, $value);
		print_r($this->user);
	}

	public function page( $page = 'Home' ) {
		$blag = 'tieten';

		$messages = Session::messages();

		return get_defined_vars();
	}

	private function approved ( OpenID $openid )
	{
		// kan ook one zijn, maar dan kunt ge exception krijgen :o
		try
		{
			$userOpenID = UserOpenID::one(array('identity' => $openid->identity));
			$user_id = $userOpenID->user_id;
		}
		catch( ModelException $e )
		{
			// first login of this user! make new user!
			$user_id = User::insert(array('first_login' => time(), 'last_login' => time(), 'last_access' => time()));
			UserOpenID::insert(array('user_id' => $user_id, 'identity' => $openid->identity));
			// might wanne keep on redirecting this fuck and tell him to do something, like fill in a username or have sex with your granny
		}

		try {
			$user = User::get($user_id);
		}
		catch ( Exception $ex )
		{
			// something is batshit wrong, let's just die!
			return $this->page();
		}

		$this->user->login($user);

		return $this->_redirect('pages/restricted');
	}

	public function restricted ( )
	{
		$content = $this->user->user . '! You found my sikrit stash of stashes :(';

		$messages = Session::messages();

		return get_defined_vars();
	}

	public function post_login()
	{
		if ( @$_REQUEST['openid_mode'] == 'cancel' ) {
			Session::success('OpenID login cancelled');
			return $this->login();
		}

		$identity = @$_REQUEST['openid_identity'] ?: @$_REQUEST['identity'];
		if ( $identity ) {
			return $this->handle_openid_login($identity, true);
		}

#		else if ( isset($_POST['openid.identity']) ) {
#			return $this->handle_openid_login($_POST['openid.identity']);
#		}

		$messages = Session::messages();

		return $this->login();
	}

	/*public function post_google () {
		$identity = 'https://www.google.com/accounts/o8/id';
		return $this->handle_openid_login($identity);
	}*/

	public function post_facebook ( )
	{
		$user_info = FacebookConnect::getUserInfo();
		if ( $user_info == null )
			return $this->login();

		// kan ook one zijn, maar dan kunt ge exception krijgen :o
		try
		{
			$userFacebookConnect = UserFacebookConnect::one(array('facebook_id' => $user_info->id));
			$user_id = $userFacebookConnect->user_id;
		}
		catch( ModelException $e )
		{
			// first login of this user! make new user!
			$user_id = User::insert(array('first_login' => time(), 'last_login' => time(), 'last_access' => time()));
			UserFacebookConnect::insert(array('user_id' => $user_id, 'facebook_id' => $user_info->id));
			// might wanne keep on redirecting this fuck and tell him to do something, like fill in a username or have sex with your granny
		}

		try {
			$user = User::get($user_id);
		}
		catch ( Exception $ex )
		{
			// something is batshit wrong, let's just die!
			return $this->page();
		}

		$this->user->login($user);

		return $this->_redirect('pages/restricted');
	}

	private function handle_openid_login ( $identity, $debug = false )
	{
		try {
			$openid = new OpenID();
//$openid->returnUrl = 'http://localhost/_http_server.php';
$openid->log($openid);
			// blag, de optional mag weg eventueel, nu krijgt ge deze gegevens gewoon als de user heeft ingesteld da ge ze moogt krijgen
			$openid->optional = array('namePerson/friendly', 'contact/email', 'namePerson', 'birthDate', 'person/gender', 'contact/postalCode/home', 'contact/country/home', 'pref/language', 'pref/timezone');

			// new
			if ( !$openid->mode ) {
				$openid->identity = $identity;
				$authUrl = $openid->authUrl();
//exit($authUrl);
				return $this->_redirect($authUrl);
			}

			// cancel
			else if ( $openid->mode == 'cancel' ) {
				return $this->page('Login Cancelled');
			}

			// id_res ?
			else {
$openid->log($openid);
				if ( $openid->validate() ) {
$openid->log($openid);
					return $this->approved($openid);
				}

				return $this->page('KUTHOER');
			}
		} catch( ErrorException $e ) {
			return $this->page($e->getMessage());
		}
	}

	public function login ( )
	{
		if ( FacebookConnect::isLoggedIn() ) {
			return $this->post_facebook();
		}

		$messages = Session::messages();

		$google = OpenID::$google;

		return get_defined_vars();
	}

	public function logout ( )
	{
		$this->user->logout();
		return $this->page();
	}
}


