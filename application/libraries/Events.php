<?php

class Events {
	const REMOVE_HANDLER = 'remove';

	private $map;
	var $CI;
	private $param_counts;

	/**
	 * The constructor for the event system.
	 *
	 * @return void
	 * @author Rich Cavanaugh
	 **/
	function __construct () {
		$this->CI =& get_instance();
		$this->map = $this->initialize_map();
		$this->param_counts = array();
	}

	/**
	 * Send a message through the event system.
	 *
	 * @return void
	 * @author Rich Cavanaugh
	 **/
	function trigger ($message, $object=NULL) {
		// return early if we have nothing to do
		if (empty($this->map[$message])) return;

		$remove = array();
		foreach ($this->map[$message] as $i => $val) {
			// extract and format the values from this event handler mapping
			list($type, $library, $method, $args) = $this->values_for_handler($val);

			if (!isset($this->CI->$library)) {
                $this->CI->load->$type($library);
            }

			// extract the appropriate amount of parameters to be passed
			// to the handler method
			$params = array($object, $args, $message);

			// call the handler method with the parameters
			$rv = call_user_func_array(array($this->CI->$library, $method), $params);

			if ($rv == Events::REMOVE_HANDLER) {
                $remove[] = $i;
            }
		}

		foreach ($remove as $key) unset($this->map[$message][$key]);
	}

	/**
	 * Load the configuration file and initialize
	 * some internal data structures from the values.
	 *
	 * @return array
	 * @author Rich Cavanaugh
	 **/
	protected function initialize_map () {
		$this->CI->config->load('events', true);
		return $this->CI->config->config['events'];
	}

	/**
	 * Sets up the handler values array. If there's no fourth item
	 * it creates one with a NULL value.
	 *
	 * @return array
	 * @author Rich Cavanaugh
	 **/
	protected function values_for_handler ($val) {
		if (count($val) == 3) array_push($val, NULL);
		return $val;
	}
}