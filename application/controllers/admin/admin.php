<?php
require_once APPPATH.'controllers/mallerp'.EXT;
class Admin extends Mallerp
{
    const NAME = 'admin';

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
    }
    
    public function index()
    {
        $this->template->write_view('content', 'admin/test');
        $this->template->render();
    }
    
    protected function fetch_all_navs()
    {
        $controller_dir = APPPATH.'controllers/';
        $navs = array();
        $map = directory_map($controller_dir, TRUE);
        foreach ($map as $item)
        {
            if (is_dir($controller_dir.$item))
            {
                $sys_file = $controller_dir.$item.'/'.$item.'.info';
                $nav_file = $controller_dir.$item.'/nav.info';
                if (is_file($sys_file))
                {
                    $sys_result = parse_ini_file($sys_file);
                    $name = $sys_result['name'];
                    if (isset($name))
                    {
                        if (is_file($nav_file))
                        {
                            $nav_result = parse_ini_file($nav_file, TRUE);
                            $navs[$item] = $nav_result;
                        }
                    }
                }
            }
        }

        return $navs;
    }

    protected function fetch_all_enable_subsys_r()
    {
        $all_subsys = $this->fetch_all_subsys();
        $all_enable_subsys = array();
        foreach ($all_subsys as $code => $item)
        {
            if ($item['status'] == 1)
            {
                $all_enable_subsys[$item['name']] = $code;
            }
        }
        return $all_enable_subsys;
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
