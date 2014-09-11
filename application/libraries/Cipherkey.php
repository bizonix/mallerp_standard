<?php
class MY_Cipherkey
{
    public function generate_key($uri = '')
    {
        $CI = & get_instance();
        $uri = rtrim($uri, '/');
        $uri = trim($uri);
        
        if (empty($uri))
        {
            return '';
        }

        $key = $CI->session->userdata($uri);
        if ($key)
        {
            return $key;
        }
        if (function_exists('sha1')) {
            $key = sha1($uri . time());
        } else {
            $key = md5($uri . time());
        }
        $CI->session->set_userdata($uri, $key);
        
        return $CI->session->userdata($uri);
    }

    public function verify_key($uri, $key)
    {
        $CI = & get_instance() ;
        $uri = rtrim($uri, '/');
        $uri = trim($uri);
        
        $my_key = $CI->session->userdata($uri);

        return ($my_key && $my_key == $key);
    }

    public function get_key($uri)
    {
        $CI = & get_instance() ;
        $uri = rtrim($uri, '/');
        $uri = trim($uri);

        return $CI->session->userdata($uri);
    }
}

// End of Cripherkey.php