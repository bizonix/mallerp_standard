<?php
    function site_url($uri = '', $args = array())
    {
        $CI =& get_instance();
        $key = $CI->cipherkey->generate_key($uri);

        $url = $CI->config->site_url($uri);
        $url .= '/' . implode('/', $args);
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        if($CI->config->item('enable_secret_key'))
		{
			$url .= $key;
		}
        
        return  $url;
    }

    function site_url_no_key($uri = '', $args = array())
    {
        $CI =& get_instance();
        $url = $CI->config->site_url($uri);
        $url .= '/' . implode('/', $args);
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        
        return  $url;
    }

    function site_key($uri)
    {
        $CI =& get_instance();
        return $CI->cipherkey->generate_key($uri);
    }

    function fetch_request_uri()
    {
        $CI =& get_instance();
        $dir = $CI->router->fetch_directory();
        $class =  $CI->router->fetch_class();
        $method = $CI->router->fetch_method();

        $dir = trim($dir, '/');
        $uri = implode('/', array($dir, $class, $method));
        $uri = trim($uri, '/');
        
        return $uri;
    }

    function request_uri_count()
    {
        return count(explode('/', fetch_request_uri()));
    }
?>
