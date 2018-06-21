<?php

/**
 * View class.
 */
class View {

	/** @var array loaded helpers */
	public $loadedHelpers = [];

	/** @var string view filename */
	public $filename = null;

	/** @var array view variables */
	public $viewVars = [];

	/** @var array variable names used in layout (excluded in json) */
	public static $layoutVars = [
		'page_title',
		'meta_title',
		'meta_description',
		'meta_keywords',
		'meta_robots',
		'authuser',
		'request',
		'theme',
		'body_classes'
	];

	/**
	 * View constructor.
	 *
	 * @param string $viewName name of view
	 * @param array $viewVars view variables
	 */
	public function __construct($viewName, $viewVars) {

		$this->filename = 'views/' . $viewName . '.php';
		$this->viewVars = $viewVars;

	}

	/**
	 * Renders a view.
	 *
	 * @param string $viewName name of view (optional)
	 * @param bool $returnOutput returns output instead of outputting, if true
	 * @return string output if $returnOutput is true
	 * @throws Exception
	 */
	public function render($viewName = null, $returnOutput = false) {

		if ($viewName) {
			$filename = 'views/' . $viewName . '.php';
		} else {
			$filename = $this->filename;
		}
		extract($this->viewVars);
		if (!file_exists($filename)) {
			throw new Exception('View not found: ' . $filename);
		}
		if ($returnOutput) {
			ob_start();
		}
		require $filename;
		if ($returnOutput) {
			$output = ob_get_clean();
			return $output;
		}

	}

	/**
	 * Renders an element.
	 *
	 * @param string $elementName name of element
	 */
	public function element($elementName) {

		$this->render('elements/' . $elementName);

	}

	/**
	 * Loads a helper.
	 *
	 * @param string $helper helper name
	 * @throws Exception
	 */
	public function loadHelper($helper) {

		if (!in_array($helper, $this->loadedHelpers)) {
			$this->loadedHelpers[] = $helper;
			$this->{$helper} = App::load('Helper', $helper);
			$this->{$helper}->view = &$this;
		}

	}

	/**
	 * Loads multiple helpers.
	 *
	 * @param array $helpers helper names
	 */
	public function loadHelpers($helpers) {

		foreach ($helpers as $helper) {
			$this->loadHelper($helper);
		}

	}

}