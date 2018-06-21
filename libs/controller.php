<?php

/**
 * Controller class.
 */
class Controller {

	/** @var Request request object */
	public $request = null;

	/** @var array view variables */
	public $viewVars = [];

	/** @var string default layout */
	public $layout = 'default';

	/** @var bool if set to false, don't render view and layouts */
	public $autoRender = true;

	/** @var bool true if view has been rendered */
	public $hasRendered = false;

	/** @var array default models loaded in all actions */
	public $uses = [];

	/** @var array loaded models */
	public $loadedModels = [];

	/** @var array default components loaded in all actions */
	public $components = [];

	/** @var array loaded components */
	public $loadedComponents = [];

	/** @var array default helpers loaded in all views */
	public $helpers = [];


	/**
	 * Renders a view.
	 *
	 * @param string $viewName name of the view ("action" or "controller/action"). if empty, standard view is rendered
	 */
	public function render($viewName = '') {

		if (!$viewName) {
			$viewName = $this->request->controller . '/' . $this->request->action;
		}
		if (strpos($viewName, '/') === false) {
			$viewName = $this->request->controller . '/' . $viewName;
		}

		$view = new View($viewName, $this->viewVars);
		$view->loadHelpers($this->helpers);
		$view->render();
		$this->hasRendered = true;

	}

	/**
	 * Sets one or more view variables.
	 *
	 * @param mixed $mixed name of the variable or an array in compact() format
	 * @param mixed $value the value (in case a variable name is used as first parameter)
	 */
	public function set($mixed, $value = null) {

		if (is_array($mixed)) {
			foreach ($mixed as $key => $val) {
				$this->viewVars[$key] = $val;
			}
		}
		else {
			$this->viewVars[$mixed] = $value;
		}

	}

	/**
	 * Loads all default models.
	 */
	public function loadUses() {

		foreach ($this->uses as $use) {
			$this->loadModel($use);
		}

	}

	/**
	 * Loads a model.
	 *
	 * @param string $model model name
	 */
	public function loadModel($model) {

		if (!in_array($model, $this->loadedModels)) {
			$this->loadedModels[] = $model;
			$this->{$model} = App::load('Model', $model);
		}

	}

	/**
	 * Loads all default components.
	 */
	public function loadComponents() {

		foreach ($this->components as $component) {
			$this->loadComponent($component);
		}

	}

	/**
	 * Loads a component.
	 *
	 * @param string $component component name
	 */
	public function loadComponent($component) {

		if (!in_array($component, $this->loadedComponents)) {
			$this->loadedComponents[] = $component;
			$this->{$component} = App::load('Component', $component);
		}

	}

	/**
	 * Redirect to an URL
	 *
	 * @param string $url URL
	 * @param bool $internal if set to true, perform internal redirect instead of redirect by header
	 */
	public function redirect($url, $internal = false) {

		if ($internal) {
			new App($url);
		} else {
			header('Location: ' . $url);
		}
		exit;

	}

}
