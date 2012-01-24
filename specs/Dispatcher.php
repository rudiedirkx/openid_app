<?php

namespace app\specs;

class Dispatcher extends \row\http\Dispatcher {

	public $cache = false;

	public function getOptions() {
		$options = parent::getOptions();

		$options->action_name_postfix = '';

		return $options;
	}

}


