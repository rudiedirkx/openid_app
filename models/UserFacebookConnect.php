<?php

namespace app\models;

use app\specs\Model;

class UserFacebookConnect extends Model {

	static public $chain;
	static public $_table = 'user_facebook_connects';
	static public $_pk = array('user_id', 'facebook_id');
	static public $_getters = array();

	public function __tostring() {
		return 'User # '.$this->user_id.' with fbid '.$this->facebook_id;
	}
}
?>