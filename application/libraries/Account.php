<?php
class MY_Account
{
    public function set_account($data)
    {
        $CI = & get_instance();
        $CI->session->set_userdata($data);
    }

    public function get_account()
    {
        $CI = & get_instance();
        $user = $CI->session->userdata('account');

        return $user;
    }

    public function default_system()
    {
        $CI = & get_instance();
        $user = $CI->session->userdata('default_system');

        return $user;
    }

    public function get_groups()
    {
        $CI = & get_instance();

        return $CI->session->userdata('groups');
    }

    public function get_selected_group()
    {
        $CI = & get_instance();

        return $CI->session->userdata('selected_group');
    }

    public function renew_account()
    {
        $CI = & get_instance();

        return $CI->session->sess_destroy();
    }
}

// End of Cripherkey.php
