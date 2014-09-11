<?php
    function product_can_read($key)
    {
        $CI =& get_instance();
        if ($CI->is_super_user())
        {
            return true;
        }
        if ( ! isset($key))
        {
            return false;
        }
        $CI->load->model('product_permission_model');
        $group_ids = $CI->get_current_user_group_ids();
        
        return $CI->product_permission_model->product_can_read($key, $group_ids);
    }

    function product_can_write($key)
    {
        $CI =& get_instance();
        if ($CI->is_super_user())
        {
            return true;
        }
        if ( ! isset($key))
        {
            return false;
        }
        $CI->load->model('product_permission_model');
        $group_ids = $CI->get_current_user_group_ids();

        return $CI->product_permission_model->product_can_write($key, $group_ids);
    }
?>
