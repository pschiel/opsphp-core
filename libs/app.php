<?php

/**
 * App class.
 */
class App {

	/**
	 * App constructor.
	 *
	 * @param string $url url for console calls
	 * @throws Exception
	 */
	public function __construct($url = null) {

		/** @var Request $request */
		$request = new Request($url);

		/** @var Controller $controller */
		$controller = self::load('Controller', ucfirst($request->controller));
		$controller->request = $request;
		$controller->set('request', $request);

		// load default models from $uses and components from $components
		$controller->loadUses();
		$controller->loadComponents();

		// start output buffering (not for CLI)
		if (PHP_SAPI != 'cli') {
			ob_start();
		}

		// beforeFilter hook
		if (method_exists($controller, 'beforeFilter') || method_exists(get_parent_class($controller), 'beforeFilter')) {
			$controller->beforeFilter();
		}

		// invoke controller action
		if (!method_exists($controller, $request->action)) {
			print_r($controller);
			throw new Exception('Action not found: ' . $request->controller . '/' . $request->action);
		}
		call_user_func_array(array($controller, $request->action), $request->params);

		// render view and layout
		if ($controller->autoRender) {
			if (!$controller->hasRendered) {
				if (Request::isJson()) {
					foreach (View::$layoutVars as $layoutVar) {
						unset($controller->viewVars[$layoutVar]);
					}
					echo json_encode($controller->viewVars);
				} else {
					$format = Request::gpvar('format');
					if ($format == 'csv' || $format == 'xls') {
						$controller->render($request->action . '_csv');
						$content = ob_get_clean();
						if ($format == 'csv') {
							header('Content-Type: application/csv; charset=utf-8');
						} elseif ($format == 'xls') {
							header('Content-Type: application/vnd.ms-excel; charset=utf-8');
						}
						header('Content-Disposition: attachment; filename=' . $request->controller . '_' . $request->action . '_' . date('Ymd_Hi') . '.' . $format);
						echo "\xEF\xBB\xBF";
						echo $content;
						return;
					}
					$controller->render();
				}
			}
			$contentForLayout = ob_get_clean();
			if (Request::isAjax() || Request::isJson()) {
				echo $contentForLayout;
				return;
			}
			ob_start();
			$layoutVars = ['content' => $contentForLayout];
			foreach (View::$layoutVars as $layoutVar) {
				if (isset($controller->viewVars[$layoutVar])) $layoutVars[$layoutVar] = $controller->viewVars[$layoutVar];
			}
			$layout = new View('layouts/' . $controller->layout, $layoutVars);
			$layout->loadHelpers($controller->helpers);
			$layout->render();
		}

		$content = ob_get_clean();
		echo $content;

	}

	/**
	 * Loads controller, model, component or helper and returns instance.
	 *
	 * @param string $type "Controller", "Model", "Component" or "Helper"
	 * @param string $name name of class
	 * @param array $interfaces one or more interfaces the loaded class has to implement
	 * @return mixed class instance
	 * @throws Exception
	 */
	public static function load($type, $name, $interfaces = []) {

		$camelCaseName = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
		if ($type == 'Controller') {
			$filename = 'controllers/' . strtolower($name) . '_controller.php';
			$classname = $camelCaseName . 'Controller';
		}
		elseif ($type == 'Model') {
			$filename = 'models/' . strtolower($name) . '.php';
			$classname = $camelCaseName;
		}
		elseif ($type == 'Component') {
			$filename = 'controllers/components/' . strtolower($name) . '_component.php';
			$classname = $camelCaseName . 'Component';
		}
		elseif ($type == 'Helper') {
			$filename = 'views/helpers/' . strtolower($name) . '_helper.php';
			$classname = $camelCaseName . 'Helper';
		}
		else {
			throw new Exception('Unknown type in App::load: ' . $type);
		}
		if (!file_exists($filename)) {
			throw new Exception($type . ' not found: ' . $filename);
		}
		require_once $filename;
		if (!class_exists($classname)) {
			throw new Exception('Class not found: ' . $classname);
		}
		$instance = new $classname;
		if (!empty($interfaces)) {
			$instance_interfaces = class_implements($instance);
			foreach ($interfaces as $interface) {
				if (!isset($instance_interfaces[$interface])) {
					throw new Exception('Interface constraint violation: ' . $classname . ' (must implement: ' . $interface . ')');
				}
			}
		}
		return $instance;

	}

}
