<?php

namespace app\models;

use app\specs\Model;

class User extends Model {

	static public $chain;
	static public $_table = 'users';
	static public $_pk = 'id';
	static public $_getters = array();

	public function __tostring() {
		return 'User number '.$this->id;
	}
}
?>