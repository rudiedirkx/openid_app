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

		$this->Facebook = new FacebookConnect(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET, array(
			'redirect_uri' => FACEBOOK_REDIRECT_URI,
		));
		$this->tpl->assign('Facebook', $this->Facebook);

		$this->tpl->assign('users', User::all());
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

	public function restricted() {
		$content = $this->user->user . '! You found my sikrit stash of stashes :(';

		$messages = Session::messages();

		return get_defined_vars();
	}

	public function post_login() {
		if ( @$_REQUEST['openid_mode'] == 'cancel' ) {
			Session::success('OpenID login cancelled');

			return $this->_redirect('pages/login');
		}

		$identity = @$_REQUEST['openid_identity'] ?: @$_REQUEST['identity'];
		if ( $identity ) {
			return $this->handle_openid_login($identity);
		}

		$messages = Session::messages();

		return $this->_redirect('pages/login');
	}

	public function openid() {
		
	}

	public function facebook() {
		$auth = $this->Facebook->validate(); // get CODE from REQUEST

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

	private function handle_openid_login( $identity ) {
		try {
			$openid = new OpenID();

			// new
			if ( !$openid->mode ) {
				return redirect($openid->login($identity));
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
			}
		}
		catch( ErrorException $e ) {}

		Session::warning('Something went awry...');

		return redirect('pages/index');
	}

	public function login() {
		$messages = Session::messages();

		$google = OpenID::$providers['google'];

		return get_defined_vars();
	}

	public function logout() {
		$this->user->logout();

		return $this->_redirect('pages');
	}

}


