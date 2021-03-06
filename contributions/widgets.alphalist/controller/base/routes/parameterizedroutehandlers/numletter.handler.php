<?php
/**
 * Handle a single lowercase letter or 0-9 as numeric type
 *
 * Use like this: /route/{letter:nl}
 * 
 * @author Gerd Riesselmann
 * @ingroup Alphalist
 */
class NumletterParameterizedRouteHandler implements IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key() {
		return "nl";
	}
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params) {
		return '[0-9a-z]';
	}
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value) {
		return GyroString::to_lower($value);
	}
}
