<?php

namespace app\specs;

use row\auth\Session;

abstract class Controller extends \row\Controller {

	protected function _init() {
		// Make the session user always available in every controller:
		$this->user = SessionUser::user();

		// Might come in handy sometimes: direct access to the DBAL:
		$this->db = $GLOBALS['db'];

		// Initialize Output/Views (used in 90% of controller actions):
		$this->tpl = new Output($this);
		$this->tpl->viewLayout = '_layout';
		$this->tpl->assign('app', $this);
	}
	
	public function aclCheckAccess( $zone ) {
		return $this->user->hasAccess($zone);
	}
	
	protected function aclAccessFail( $zone, $action ) {
		if ( $zone === 'not login' ) {
			return $this->_redirect('pages/restricted');
		}

		Session::error('You gotsta be logged for this shit right here');
		$this->_redirect('pages/login');
	}
}


