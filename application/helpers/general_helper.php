<?php

    function gmt_to_pdt($gmt, $get)
    {
        $gmt=str_replace("T"," ",$gmt);
        $gmt=str_replace("Z","",$gmt);
        $gmt=date("Y-m-d H:i:s",mktime(date("H",strtotime($gmt))-7,date("i",strtotime($gmt)),date("s",strtotime($gmt)),date("m",strtotime($gmt)),date("d",strtotime($gmt)),date("Y",strtotime($gmt))));

        if($get=="D"){
            return date("Y-m-d",strtotime($gmt));
        }
        if($get=="T"){
            return date("H:i:s",strtotime($gmt));
        }
    }

    function get_current_utc_time()
    {
        $time_zone = date('e');
        if (substr_count($time_zone, 'Asia'))
        {
            $utc_time = time() - 8 * 60 * 60;
        } 
        else if (substr_count($time_zone, 'America'))
        {
            $utc_time = time() + 7 * 60 * 60;
        }

        return date('Y-m-d\TH:i:s\Z', $utc_time);
    }

    function get_utc_time($strtotime)
    {
        $time_zone = date('e');
        $time = strtotime($strtotime);
        if (substr_count($time_zone, 'Asia'))
        {
            $utc_time = $time - 8 * 60 * 60;
        } 
        else if (substr_count($time_zone, 'America'))
        {
            $utc_time = $time + 7 * 60 * 60;
        }

        return date('Y-m-d\TH:i:s\Z', $utc_time);
    }
	function get_utc_time_b2c($strtotime)
    {
        $time_zone = date('e');
        $time = strtotime($strtotime);
		$utc_time = $time - 13 * 60 * 60;
        return date('Y-m-d\TH:i:s\Z', $utc_time);
    }

    function fetch_sale_status_icon($status)
    {
        return '<img src="/static/img/sale_status/' . $status . '.gif"' . '/>';
    }

    function fetch_icon($img_name)
    {
        $base_url = base_url();
        return '<img src="' . $base_url . 'static/images/icons/' . $img_name . '"/>';
    }

    function is_local_networking()
    {
        $server_name = $_SERVER['SERVER_NAME'];
        if (strpos($server_name, 'dev') !== false)
        {
            return true;
        }
        if (strpos($server_name, '192.168') === false)
        {
            return false;
        }
        return true;
    }

    function price($val, $decimals = 2)
    {
        return number_format($val, $decimals, '.', '');
    }

    function sale_quota($val, $decimals = 4)
    {
        return number_format($val, $decimals, '.', '');
    }

    function positive_numeric($val)
    {
        if (is_numeric($val) && $val > 0)
        {
            return TRUE;
        }

        return FALSE;
    }

    /*
     * change one-dimensional array to multi-dimensional array,
     * i.e, $a = array(1,3,4) => $a[1][3][4] = array()
     */
    function flat_to_multi($flat, & $multi = array())
    {
        $temp  =& $multi;
        foreach ($flat as $item)
        {
            if ( ! isset($temp[$item]))
            {
                $temp[$item] = NULL;
            }
            $temp =& $temp[$item];
        }
        
        return $multi;
    }

    /*
     * change multi-dimensional array to hash, used for form select
     * i.e, $a['1:x'] = array('2:y') => $a = array(1 => 'x', 2 => '  y');
     */
    function multi_to_selection($multi)
    {
        echo '<pre>';
        var_dump($multi);
        foreach ($multi as $key => $value)
        {
            
        }
    }

    function to_js_array($array)
    {
        $collection = "[";
        $count = count($array);
        $i = 0;
        foreach ($array as $key => $name) {
            $i++;
            if ($i < $count)
            {
                $collection .= "['$key', '$name'],";
            }
            else
            {
                $collection .= "['$key', '$name']";
            }
        }
        $collection .= "]";

        return $collection;
    }

    function is_positive($value)
    {
        return preg_match('/^[1-9]\d*$/', $value);
    }

    function secs_to_readable($secs)
    {
        $time = array();
        $mins = floor($secs / 60);
        $secs = $mins % 60;

        if ($mins >= 60)
        {
            $hours = floor($mins / 60);
            $mins = $mins % 60;
        }
        if (isset($hours) && $hours >= 24)
        {
            $days = floor($hours / 24);
            $hours = $hours % 24;
        }

        $time['days'] = isset($days) ? $days : 0;
        $time['hours'] = isset($hours) ? $hours : 0;
        $time['mins'] = $mins;
        $time['secs'] = $secs;
        return $time;
    }

    function get_current_language()
    {
        $CI = & get_instance();

        return $CI->get_current_language();
    }

    function get_current_user_id()
    {
        $CI = & get_instance();

        return $CI->get_current_user_id();
    }

    function get_current_login_name()
    {
        $CI = & get_instance();
        return $CI->get_current_login_name();
    }
    
    function get_current_user_name()
    {
        $CI = & get_instance();

        return $CI->get_current_user_name();
    }

    function fetch_current_system_codes()
    {
        $CI = & get_instance();

        return $CI->fetch_current_system_codes();
    }

    function get_current_time()
    {
        return date('Y-m-d H:i:s');
    }

    function no_space($str)
    {
        return preg_replace('/\s+/', '', $str);
    }

    function get_relevant($needle, $haystack, $values)
    {
        if (is_string($haystack))
        {
            $haystack = explode(',', $haystack);
            $values = explode(',', $values);
        }
        $i = 0;
        foreach ($haystack as $key => $item)
        {
            if ($item == $needle)
            {
                return $values[$i];
            }
            $i++;
        }
        return FALSE;
    }

    function append_if_not_empty($str, $str_append)
    {
        if ( ! empty($str))
        {
            return $str . $str_append;
        }
        return $str;
    }

    function plus( & $left, $right)
    {
        if (isset($left))
        {
            $left += $right;
        }
        else
        {
            $left = $right;
        }
    }

    function split_time_scope($begin_time, $end_time)
    {
        $scope = array();
        $begin_timestamp = strtotime($begin_time);
        $end_timestamp = strtotime($end_time);
        while ($begin_timestamp < $end_timestamp)
        {
            $new_begin_timestamp = strtotime('+1 day', $begin_timestamp);
            $new_end_time = $end_time;
            if ($new_begin_timestamp < $end_timestamp)
            {
                $new_end_time = date('Y-m-d 23:59:59', $begin_timestamp);
            }
            
            $scope[] = array(
                'begin_time'    => $begin_timestamp == strtotime($begin_time) ? $begin_time : date('Y-m-d 00:00:00', $begin_timestamp),
                'end_time'      => $new_end_time,
            );
            
            $begin_timestamp = $new_begin_timestamp;
        }

        return $scope;
    }

    function statistics_word_count($tag_strs)
    {
        $strs = strip_tags($tag_strs);
        $count = str_word_count($strs);
        
        return $count;
    }

    function formate_to_number($number_str)
    {
        if(strpos($number_str, ',') !== FALSE)
        {
           $number = str_replace(',', '', $number_str);
        }
        else
        {
            $number = $number_str;
        }
        
        return $number;
    }

    function iso8601_to_readable($duration)
    {
        $duration = str_replace('P', '', $duration);
        $duration = str_replace('DT', ' date(s) ', $duration);
        $duration = str_replace('H', ' hour(s)', $duration);
        $duration = str_replace('M', ' min(s)', $duration);
        $duration = str_replace('S', ' sec(s)', $duration);

        return $duration;
    }

    function string_limiter($str, $start, $len)
    {
        $str = substr($str, $start, $len);
        $str .= '...';

        return $str;
    }

    function wrap_color($text, $color, $condition = TRUE)
    {
        if ($condition)
        {
            return "<span style='color: $color'> $text </span>";
        }
    
        return $text;
    }

    function generate_ajax_view_link($url,$title)
    {
        return anchor($url, $title, 'onclick="helper.ajax_toggle_content(this.href, {}, \'main-content-detail\', \'main-content\'); return false"');
    }

     /**
     * 去除字符串右侧可能出现的乱码
     *
     * @param   string      $str        字符串
     *
     * @return  string
     */
    function trim_right($str)
    {
        $len = strlen($str);
        /* 为空或单个字符直接返回 */
        if ($len == 0 || ord($str{$len-1}) < 127)
        {
            return $str;
        }
        /* 有前导字符的直接把前导字符去掉 */
        if (ord($str{$len-1}) >= 192)
        {
           return substr($str, 0, $len-1);
        }
        /* 有非独立的字符，先把非独立字符去掉，再验证非独立的字符是不是一个完整的字，不是连原来前导字符也截取掉 */
        $r_len = strlen(rtrim($str, "\x80..\xBF"));
        if ($r_len == 0 || ord($str{$r_len-1}) < 127)
        {
            return sub_str($str, 0, $r_len);
        }

        $as_num = ord(~$str{$r_len -1});
        if ($as_num > (1<<(6 + $r_len - $len)))
        {
            return $str;
        }
        else
        {
            return substr($str, 0, $r_len-1);
        }
    }

?>
