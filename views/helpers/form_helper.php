<?php

/**
 * Form helper
 */
class FormHelper extends Helper {

	/**
	 * Creates script to show validation errors.
	 *
	 * @return string validation errors script
	 */
	public function validateErrors() {

		$validateErrors = isset($this->view->viewVars['validate_errors']) ? $this->view->viewVars['validate_errors'] : [];
		$content = '
			<script>
				var validateErrors=' . json_encode($validateErrors) . ';
				for (var model in validateErrors) {
					for (var field in validateErrors[model]) {
						if (typeof validateErrors[model][field] == "object") {
							for (var subfield in validateErrors[model][field]) {
								var $div = $(\'[name="\'+model+"["+field+\'][\' + subfield + \']"]\').parent();
								$div.addClass("has-error").append(\'<div class="errormessage">\'+validateErrors[model][field][subfield]+\'</div>\');
							}
						} else {
							var $div = $(\'[name="\'+model+"["+field+\']"]\').parent();
							$div.addClass("has-error").append(\'<div class="errormessage">\'+validateErrors[model][field]+\'</div>\');
						}
					}
				}
			</script>
		';
		return $content;

	}

	/**
	 * Creates paginator.
	 *
	 * @param int $count total results
	 * @param int $page current page
	 * @param int $resultsperpage results per page
	 * @return string paginator html
	 */
	public function paginator($count, $page, $resultsperpage) {

		$content = '<nav><ul class="pagination">';
		$url = Request::url();
		$params = $_REQUEST;
		unset($params['url']);
		if ($page > 1) {
			$content .= '<li><a href="' . $url . '?' . http_build_query(array_merge($params, ['page' => $page - 1])) . '">&laquo;</a>';
		} else {
			$content .= '<li class="disabled"><a href="#">&laquo;</a>';
		}
		$pages = [1];
		$totalpages = ceil($count / $resultsperpage);
		for ($i = $page - 2; $i <= $page + 2; $i++) {
			if ($i >= 1 && $i <= $totalpages && !in_array($i, $pages)) {
				$pages[] = $i;
			}
		}
		if (!in_array($totalpages, $pages) && $totalpages > 0) {
			$pages[] = $totalpages;
		}
		$lastpage = 0;
		foreach ($pages as $i) {
			if ($i > $lastpage + 1) {
				$content .= '<li><a href="' . $url . '?' . http_build_query(array_merge($params, ['page' => min($i - 1, $lastpage + 10)])) . '">...</a></li>';
			}
			if ($i == $page) {
				$content .= '<li class="active"><a href="#">' . $i . '</a></li>';
			} else {
				$content .= '<li><a href="' . $url . '?' . http_build_query(array_merge($params, ['page' => $i])) . '">' . $i . '</a></li>';
			}
			$lastpage = $i;
		}
		if ($page < $totalpages) {
			$content .= '<li><a href="' . $url . '?' . http_build_query(array_merge($params, ['page' => $page + 1])) . '">&raquo;</a>';
		} else {
			$content .= '<li class="disabled"><a href="#">&raquo;</a>';
		}
		$content .= '</ul></nav>';
		return $content;

	}

	/**
	 * Generates sorting links.
	 *
	 * @param string $field field to sort for sql use
	 * @param string $label display label
	 * @return string sort link
	 */
	public function sort($field, $label) {

		$url = Request::url();
		$params = $_REQUEST;
		unset($params['url']);
		$dir = '';
		if (isset($params['order']) && $params['order'] == $field) {
			if (isset($params['dir']) && $params['dir'] == 'desc') {
				$dir = ' <span class="glyphicon glyphicon-triangle-top"></span>';
				unset($params['order']);
				unset($params['dir']);
			} else {
				$dir = ' <span class="glyphicon glyphicon-triangle-bottom"></span>';
				$params['order'] = $field;
				$params['dir'] = 'desc';
			}
		} else {
			$params['order'] = $field;
			$params['dir'] = 'asc';
		}

		if (empty($params)) {
			return '<a href="' . $url . '">' . $label . $dir . '</a>';
		} else {
			return '<a href="' . $url . '?' . http_build_query($params) . '">' . $label . $dir . '</a>';
		}
	}

}
