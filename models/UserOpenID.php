<?php

namespace app\models;

use app\specs\Model;

class UserOpenID extends Model {

	static public $_table = 'user_openids';
	static public $_pk = array('provider_type', 'identity');
	static public $_getters = array();

	public function __tostring() {
		return 'User # ' . $this->user_id . ' at ' . $this->identity;
	}

}


