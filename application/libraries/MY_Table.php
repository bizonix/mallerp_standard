<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Table extends CI_Table
{
	/**
	 * Generate the table
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	function generate($table_data = NULL, $sort_config = array())
	{
		// The table data can optionally be passed to this function
		// either as a database result object or an array
		if ( ! is_null($table_data))
		{
			if (is_object($table_data))
			{
				$this->_set_from_object($table_data);
			}
			elseif (is_array($table_data))
			{
				$set_heading = (count($this->heading) == 0 AND $this->auto_heading == FALSE) ? FALSE : TRUE;
				$this->_set_from_array($table_data, $set_heading);
			}
		}

		// Is there anything to display?  No?  Smite them!
		if (count($this->heading) == 0 AND count($this->rows) == 0)
		{
			return 'Undefined table data';
		}

		// Compile and validate the template date
		$this->_compile_template();

		// set a custom cell manipulation function to a locally scoped variable so its callable
		$function = $this->function;

		// Build the table!

		$out = $this->template['table_open'];
		$out .= $this->newline;

		// Add any caption here
		if ($this->caption)
		{
			$out .= $this->newline;
			$out .= '<caption>' . $this->caption . '</caption>';
			$out .= $this->newline;
		}

		// Is there a table heading to display?
		if (count($this->heading) > 0)
		{
			$out .= $this->template['thead_open'];
			$out .= $this->newline;
			$out .= $this->template['heading_row_start'];
			$out .= $this->newline;

            // hacked by lion weng starts here
            $i = 0;
            $base_url = base_url();
            $CI = & get_instance();
            $head_id = NULL;
            // hacked by lion weng ends here

			foreach($this->heading as $heading)
			{
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val)
				{
					if ($key != 'data')
					{
						//$temp = str_replace('<th', "<th $key='$val'", $temp);
					}
                    // hacked by lion weng starts here
                    else {
                        if (is_array($val))
                        {
                            $sort_url = site_url(fetch_request_uri());
                            if (isset($val['sort_url']))
                            {
                                $sort_url = $val['sort_url'];
                            }
                        }
                        if (is_array($val) && isset($val['sort_key']))
                        {
                            $out .= "<th onclick='helper.sort_table(\"$sort_url\", \"{$val['sort_key']}\")'>";
                        }
                        else if (isset ($sort_config[$i]))
                        {
                            $out .= "<th sort='{$sort_config[$i]}'>";
                        }
                        else {
                            $out .= $this->template['heading_cell_start'];
                        }
                        $i++;

                        if (is_array($val))
                        {
                            $direction = '';
                            $sorter = '';
                            if ($head_id || isset($val['id']))
                            {
                                $head_id = isset($val['id']) ? $val['id'] : $head_id;
                                $direction =  $CI->filter->get_sorter_direction($head_id);
                                $sorters = $CI->filter->get_sorters($head_id) ? $CI->filter->get_sorters($head_id) : array();
                                $sorter = implode(' ', $sorters);
                                $sorter = str_replace(array(' desc', ' asc',), '', $sorter);
                            }
                            if ($direction && isset($val['sort_key']) && $val['sort_key'] == $sorter)
                            {
                                if ($direction == 'desc')
                                {
                                    $class = 'desc';
                                }
                                else if ($direction == 'asc') {
                                    $class = 'asc';
                                }
                                $heading_text =<<< HEAD_TEXT
        <span class='sortable-base sortable-bg'>
            <span class="sortable-$class">
            {$val['text']}
            </span>
        </span>
HEAD_TEXT;
                            }
                            else if (isset($val['sort_key']))
                            {
                                $heading_text =<<< HEAD_TEXT
            <span class='sortable-base'>
            {$val['text']}
            </span>
HEAD_TEXT;
                            }
                            else
                            {
                                $heading_text = $val['text'];
                            }
                        }
                        else
                        {
                            $heading_text = $val;
                        }
                        $temp = $heading_text;
                    }
                    // hacked by lion weng ends here
				}

				$out .= $temp;
                // hacked by lion weng starts here
				// $out .= isset($heading['data']) ? $heading['data'] : '';
                // hacked by lion weng ends here
				$out .= $this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_row_end'];
			$out .= $this->newline;
			$out .= $this->template['thead_close'];
			$out .= $this->newline;
		}

		// Build the table rows
		if (count($this->rows) > 0)
		{
			$out .= $this->template['tbody_open'];
			$out .= $this->newline;

			$i = 1;
			foreach($this->rows as $row)
			{
				if ( ! is_array($row))
				{
					break;
				}

				// We use modulus to alternate the row colors
				$name = (fmod($i++, 2)) ? '' : 'alt_';

				$out .= $this->template['row_'.$name.'start'];
				$out .= $this->newline;

				foreach($row as $cell)
				{
					$temp = $this->template['cell_'.$name.'start'];

					foreach ($cell as $key => $val)
					{
						if ($key != 'data')
						{
							$temp = str_replace('<td', "<td $key='$val'", $temp);
						}
					}

					$cell = isset($cell['data']) ? $cell['data'] : '';
					$out .= $temp;

					if ($cell === "" OR $cell === NULL)
					{
						$out .= $this->empty_cells;
					}
					else
					{
						if ($function !== FALSE && is_callable($function))
						{
							$out .= call_user_func($function, $cell);
						}
						else
						{
							$out .= $cell;
						}
					}

					$out .= $this->template['cell_'.$name.'end'];
				}

				$out .= $this->template['row_'.$name.'end'];
				$out .= $this->newline;
			}

			$out .= $this->template['tbody_close'];
			$out .= $this->newline;
		}

		$out .= $this->template['table_close'];

		return $out;
	}

	// --------------------------------------------------------------------

}
?>
