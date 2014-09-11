<?php

    function make_number($num, $default = 0)
    {        
        if (empty($num))
        {
            return $default;
        }
        $num = preg_replace('/[^\d\.]/', '', $num);
        if (strpos($num, '.') !== FALSE)
        {
            $num = floatval($num);
        }
        
        return $num;
    }

    function to_utc_format($date)
    {
     
        if (empty($date))
        {
            return '0000-00-00T00:00:00Z';
        }
        if (strlen($date) == 6)
        {
            $date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2)  . '-' . substr($date, 4, 2) . '00:00:00';
        }
        
        $date = date('Y-m-d\TH:i:s\Z', strtotime($date));
        
        return $date;
    }
    
?>
