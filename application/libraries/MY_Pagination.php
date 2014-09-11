<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class MY_Pagination {

	var $base_url			= ''; // The page we are linking to
	var $total_rows  		= ''; // Total number of items (database results)
	var $per_page	 		= 10; // Max number of items you want shown per page
	var $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page	 		=  0; // The current page being viewed
	var $first_link   		= '&lsaquo; First';
	var $next_link			= '&gt;';
	var $prev_link			= '&lt;';
	var $last_link			= 'Last &rsaquo;';
	var $uri_segment		= 3;
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '&nbsp;<strong>';
	var $cur_tag_close		= '</strong>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '';
	var $page_query_string	= FALSE;
	var $query_string_segment = 'per_page';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	function MY_Pagination($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}

		log_message('debug', "Pagination Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	function create_links($params = array(), $key = NULL, $content_id = NULL)
	{
        $onclick = '';
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Determine the current page number. hack...
		$CI =& get_instance();

		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != 0)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != 0)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		$this->num_links = (int)$this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 0;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/');
		}

  		// And here we go..., hack...
		$output = '';
        $page_num = $CI->filter->get_limit($key);
        $page_num = ! empty($page_num) ? $page_num : 20;
        $options = array(
            '10'    => 10,
            '20'    => 20,
            '50'    => 50,
            '100'   => 100,
            '200'   => 200,
            '500'   => 500,
            '1000'  => 1000,
            '2000'  => 2000,
            '5000'  => 5000,
        );
        $js = "onChange=\"helper.set_page_limit(this.value);$('search_button').click();\" style='margin-right: 10px;'";
        $output .= form_dropdown('page_num', $options, $page_num, $js);
		// Render the "First" link
		if  ($this->cur_page > ($this->num_links + 1))
		{
            $url = site_url($this->base_url, $params);
            if ($content_id)
            {
                $onclick = 'onclick="helper.update_content(\''.$url.'\', {}, \''.$content_id.'\');return false;"';
            }
			$output .= $this->first_tag_open.'<a '.$onclick.' href="'.$url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  ($this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;
			if ($i == 0) $i = '';
            $page = $i != '' ? array('page', $i) : array();
            $url = site_url($this->base_url, array_merge($params, $page));
            if ($content_id)
            {
                $onclick = 'onclick="helper.update_content(\''.$url.'\', {}, \''.$content_id.'\');return false;"';
            }
			$output .= $this->prev_tag_open.'<a '.$onclick.' href="'.$url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;

			if ($i >= 0)
			{
				if ($this->cur_page == $loop)
				{
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
				}
				else
				{
					$n = ($i == 0) ? '' : $i;
                    $page = $n != '' ? array('page', $i) : array();
                    $url = site_url($this->base_url, array_merge($params, $page));
                    if ($content_id)
                    {
                        $onclick = 'onclick="helper.update_content(\''.$url.'\', {}, \''.$content_id.'\');return false;"';
                    }
					$output .= $this->num_tag_open.'<a '.$onclick.' href="'.$url.'">'.$loop.'</a>'.$this->num_tag_close;
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages)
		{
            $url = site_url($this->base_url, array_merge($params, array('page', $this->cur_page * $this->per_page)));
            if ($content_id)
            {
                $onclick = 'onclick="helper.update_content(\''.$url.'\', {}, \''.$content_id.'\');return false;"';
            }
			$output .= $this->next_tag_open.'<a ' . $onclick . '  href="'.$url.'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
            $url = site_url($this->base_url, array_merge($params, array('page', $i)));
            if ($content_id)
            {
                $onclick = 'onclick="helper.update_content(\''.$url.'\', {}, \''.$content_id.'\');return false;"';
            }
			$output .= $this->last_tag_open.'<a ' . $onclick. ' href="'.$url.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}
        $total_info = sprintf(lang('total_pages_and_total_rows'), $num_pages, $this->total_rows);
        $output .="<span style='margin-left: 10px;color: green;'>$total_info</span>";

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}
}
// END Pagination Class

/* End of file MY_Pagination.php */
/* Location: ./system/application/libraries/MY_Pagination.php */