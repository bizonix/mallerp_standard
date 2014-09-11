<?php
class Filter
{
    private $CI = NULL;
    private $total_prefix = 'filter_total_';
    private $offset_prefix = 'filter_offset_';
    private $filter_prefix = 'filter_filter_';
    private $limit_prefix = 'filter_limit_';
    private $sort_prefix = 'filter_sort_';
    private $sort_direction_prefix = 'filter_sort_direction';
    
    public function __construct()
    {
        $this->CI = & get_instance();
    }
    
    public function set_total($total, $key = NULL)
    {
        return $this->set($this->total_prefix, $total, $key);
    }

    public function get_total($key = NULL)
    {
        return $this->get($this->total_prefix, $key);
    }

    public function set_offset($offset, $key = NULL)
    {
        return $this->set($this->offset_prefix, $offset, $key);
    }

    public function get_offset($key = NULL)
    {
        return $this->get($this->offset_prefix, $key);
    }

    public function set_limit($limit, $key = NULL)
    {
        return $this->set($this->limit_prefix, $limit, $key);
    }

    public function get_limit($key = NULL)
    {
        return $this->get($this->limit_prefix, $key);
    }

    public function set_sorters($sort, $key = NULL)
    {
        return $this->set($this->sort_prefix, $sort, $key);
    }

    public function get_sorters($key = NULL)
    {
        return $this->get($this->sort_prefix, $key);
    }

    public function set_sorter_direction($direction, $key = NULL)
    {
        return $this->set($this->sort_direction_prefix, $direction, $key);
    }

    public function get_sorter_direction($key = NULL)
    {
        return $this->get($this->sort_direction_prefix, $key);
    }

    public function set_filters($filters, $key = NULL)
    {        
        return $this->set($this->filter_prefix, $filters, $key);
    }

    public function get_filters($key = NULL)
    {
        return $this->get($this->filter_prefix, $key);
    }
    
    private function set($prefix, $data, $key = NULL)
    {
        $key .= fetch_request_uri();
        
        return $this->CI->session->set_userdata($prefix . $key, $data);
    }

    private function get($prefix, $key = NULL)
    {
        $key .= fetch_request_uri();
        
        return $this->CI->session->userdata($prefix . $key);
    }
}

// End of Filter.php