<?php

class HtmlHelper extends Helper {

	/**
	 * Insert script tag with filemtime extension
	 *
	 * @param string $filename filename
	 */
	public function script($filename) {

		echo '<script src="' . $filename . '?v=' . filemtime('webroot' . $filename) . '"></script>' . "\n";

	}

	/**
	 * Insert link css tag with filemtime extension
	 *
	 * @param string $filename filename
	 */
	public function css($filename) {

		echo '<link href="' . $filename . '?v=' . filemtime('webroot' . $filename) . '" type="text/css" rel="stylesheet"/>' . "\n";

	}

}
