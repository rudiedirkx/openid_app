<?php

namespace app\controllers;

use app\specs\Controller;
use row\auth\openid\OpenID;
use row\auth\openid\FacebookConnect;
use app\models\UserOpenID;
use app\models\User;
use ErrorException;
use row\database\ModelException;
use row\auth\Session;

class pagesController extends Controller {

	protected function _init() {
		parent::_init();

		$this->aclAdd('login', array('restricted', 'accounts'));

		$this->tpl->assign('users', User::all());

		$facebook = new FacebookConnect(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET, array(
			'redirect_uri' => FACEBOOK_REDIRECT_URI,
		));

		FacebookConnect::$appId = FACEBOOK_APP_ID;
		FacebookConnect::$appSecret = FACEBOOK_APP_SECRET;
		FacebookConnect::$redirectURI = FACEBOOK_REDIRECT_URI;
	}

	public function setvar($name, $value) {
		$this->user->variable($name, $value);
		print_r($this->user);
	}

	public function index( $page = 'Home' ) {
		$blag = 'tieten';

		$messages = Session::messages();

		return get_defined_vars();
	}

	public function accounts() {
		$accounts = UserOpenID::all(array('user_id' => $this->user->userID()));

		return get_defined_vars();
	}

	/**
	private function DEPRECATED_approved ( OpenID $openid )
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
			return $this->index();
		}

		$this->user->login($user);

		return $this->_redirect('pages/restricted');
	}
	/**/

	public function restricted()
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

		$messages = Session::messages();

		return $this->login();
	}

	public function facebook() {
		$auth = FacebookConnect::authenticate();

		if ( !$auth ) {
			exit('Something failed... Probably you?');
		}

		$id = $auth->id;
		$conditions = array(
			'provider_type' => 'facebook',
			'identity' => $id,
		);

		return $this->approved($conditions, $auth);
	}

	private function approved($conditions, $params = array()) {
		try {
			// existing openid user
			$openid = UserOpenID::one($conditions);

			if ( $this->user->isLoggedIn() /*&& $this->user->userID() == $openid->user_id*/ ) {
				Session::warning('That account is already connected.');

				return $this->_redirect('pages/restricted');
			}

			// get user object
			$user = User::get($openid->user_id);
		}
		catch ( ModelException $ex ) {
			// remove potential openid user
			UserOpenID::_delete($conditions);

			// new user
			if ( !$this->user->isLoggedIn() ) {
				// insert
				$user = array(
					'signed_up_at' => time(),
					'first_login' => time(),
				);
				$uid = User::insert($user);

				// get user object
				$user = User::get($uid);
			}
			// current user
			else {
				$user = $this->user->user;
			}

			// new openid user
			$conditions['user_id'] = $user->user_id;
			$conditions['params'] = json_encode($params);
			UserOpenID::insert($conditions);
		}

		if ( !$this->user->isLoggedIn() ) {
			$this->user->login($user);
		}
		else {
			Session::success('Account connected!');
		}

		return $this->_redirect('pages/restricted');
	}

	/**
	public function DEPRECATED_post_facebook ( )
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
			return $this->index();
		}

		$this->user->login($user);

		return $this->_redirect('pages/restricted');
	}
	/**/

	private function handle_openid_login ( $identity, $debug = false )
	{
		try {
			$openid = new OpenID();
			// blag, de optional mag weg eventueel, nu krijgt ge deze gegevens gewoon als de user heeft ingesteld da ge ze moogt krijgen
			$openid->optional = array('namePerson/friendly', 'contact/email', 'namePerson', 'birthDate', 'person/gender', 'contact/postalCode/home', 'contact/country/home', 'pref/language', 'pref/timezone');

			// new
			if ( !$openid->mode ) {
				$openid->identity = $identity;
				$authUrl = $openid->authUrl();
				return $this->_redirect($authUrl);
			}

			// cancel
			else if ( $openid->mode == 'cancel' ) {
				return $this->index('Login Cancelled');
			}

			// id_res ?
			else {
				if ( $openid->validate() ) {
					$conditions = array(
						'provider_type' => 'openid',
						'identity' => $openid->identity,
					);

					return $this->approved($conditions, $openid->data);
				}

				return $this->index('KUTHOER');
			}
		} catch( ErrorException $e ) {
			return $this->index($e->getMessage());
		}
	}

	public function login ( )
	{
#		if ( FacebookConnect::isLoggedIn() ) {
#			return $this->post_facebook();
#		}

		$messages = Session::messages();

		$google = OpenID::$google;

		return get_defined_vars();
	}

	public function logout() {
		$this->user->logout();

		return $this->_redirect('pages');
	}
}


