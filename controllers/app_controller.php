<?php

/**
 * App controller.
 */
class AppController extends Controller {

	/** @var array default models */
	public $uses = [];

	/** @var array default components */
	public $components = [];

	/** @var array default helpers */
	public $helpers = ['Form', 'Html'];

	/**
	 * beforeFilter hook.
	 */
	public function beforeFilter() {

		$this->set('page_title', $_SERVER['HTTP_HOST'] . ' - ' . $this->request->controller . '/' . $this->request->action);

		if (!$this->isAuthorized()) {
			throw new Exception('Access denied', 404);
		}

	}

	/**
	 * Authorization
	 *
	 * @return bool true, if authorized
	 */
	public function isAuthorized() {

		// implement auth here
		return true;

	}

}
