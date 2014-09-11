<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    /**
     * Is a Positive natural numeric, but not a zero  (1.3,23.3,3, etc.)
     *
     * @access	public
     * @param	string
     * @return	bool
     */
	function positive_numeric($str)
    {
    	return $this->numeric($str) && $str > 0;
    }

    /**
     * Is a Positive natural numeric, but not a zero  (1.3,23.3,3, etc.)
     *
     * @access	public
     * @param	string
     * @return	bool
     */
	function positive_zero_numeric($str)
    {
    	return $this->numeric($str) && $str >= 0;
    }

    /**
     * Is a Positive natural numeric, but not a zero  (0, 1.3,23.3,3, etc.)
     *
     * @access	public
     * @param	string
     * @return	bool
     */
	function is_url($str)
    {
    	return  (bool)preg_match("/^http(:?s)?:\/\/[\S]+\.[\S]+$/i", $str);
    }

    /**
     * Is a Positive natural numeric, but not a zero  (1.3,23.3,3, etc.)
     *
     * @access	public
     * @param	string
     * @return	bool
     */
	function between_0_and_1($value)
    {
    	return $value >= 0 && $value <= 1;
    }
}
?>