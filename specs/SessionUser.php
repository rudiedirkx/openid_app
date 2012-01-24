<?php

namespace app\specs;

use row\auth\Session;
use app\models;
use \Exception;

class SessionUser extends \row\auth\SessionUser {

	public function login( \row\database\Model $user ) {
		$login = parent::login($user);
		extract($login);

		$login['user_id'] = $user->id;
		Session::$session['logins'][] = $login;
		$this->user = $user;
		
		$user->update(array('last_login' => time(), 'last_access' => time(), 'unicheck' => $login['unicheck']));

		return true; // Why would this ever be false??
	}

	public function validate() {
		$login = parent::validate();
		if ( is_array($login) && isset($login['user_id'], $login['salt']) ) {
			try {
				$this->user = models\User::get($login['user_id'], array('unicheck' => $login['unicheck']));
				$this->salt = $login['salt'];
				$this->user->update(array('last_access' => time()));
			}
			catch ( Exception $ex ) { 
				// no result, not logged in!
			}
		}
	}

	public function hasAccess( $zone ) {
		if ( $zone === 'login' )
			return $this->isLoggedIn();
		if ( $zone === 'not login' )
			return !$this->isLoggedIn();
		return false;
	}

	public function logout() {
		if ( parent::logout() ) {
			Session::success('You are now logged out.');
		}
	}

	public function displayName() {
		return $this->isLoggedIn() ? (string)$this->user : 'Anonymous';
	}

	public function userID() {
		return $this->isLoggedIn() ? (int)$this->user->id : 0;
	}
}

SessionUser::$class = 'app\specs\SessionUser';
