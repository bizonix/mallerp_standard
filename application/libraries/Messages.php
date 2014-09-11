<?php

class Messages {
	private $map;

	public function __construct ()
    {
		$this->CI =& get_instance();
		$this->map = $this->initialize_map();
	}

    public function load($type = NULL)
    {
        $map = $this->map;
        if (isset($type))
        {
            return $map[$type];
        }
        return $this->map;
    }

    public function push($event)
    {
        $message_type = $event['type'];
        $message_click_url = $event['click_url'];
        $message_content = $event['content'];
        $message_owner_id = $event['owner_id'];

        if (!isset($this->CI->Message_model))
        {
            $this->CI->load->model('Message_model');
        }
        $this->CI->Message_model->push(
            $message_type,
            $message_click_url,
            $message_content,
            $message_owner_id
        );
    }
	/**
	 * Load the configuration file and initialize
	 * some internal data structures from the values.
	 *
	 * @return array
	 **/
	protected function initialize_map ()
    {
		$this->CI->config->load('message', true);
		return $this->CI->config->config['message'];
	}
}