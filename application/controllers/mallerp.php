<?php
class Mallerp extends CI_Controller
{
    protected $nav;
    const NAME = 'void';
    private $referrer_url;
	private $return_array;
    
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set(DEFAULT_TIMEZONE);
        
        $this->lang->load('mallerp', $this->get_current_language());
        
        // check user session
        $current_uri = fetch_request_uri();
        if ( ! in_array($current_uri, $this->never_verify_list()))
        {
            if ( ! $this->account->get_account()
                && $current_uri != 'mallerp/login'
                && $current_uri != 'authenticate/login'
            )
            {
                if ($this->input->is_post())
                {
                    echo $this->create_json(0, lang('session_timeout_login_again'));
                    die('');
                }
                redirect(site_url('mallerp/login'));
            }
            // end of check user session

            // start verify secret key
            if (uri_string() && $this->config->item('enable_secret_key')) {
                if (strpos($this->uri->uri_string(), $current_uri.'/'.'popup'))
                {
                    $current_uri = $current_uri.'/'.'popup';
                }
                $total_segments = $this->uri->total_segments();
                if ( ! $this->cipherkey->verify_key($current_uri, $this->uri->segment($total_segments))) {
                    if ($this->input->is_post())
                    {
                        echo $this->create_json(0, lang('session_timeout_login_again'));
                        die('');
                    }
                    redirect(site_url('mallerp/login'));
                }
            }
        }
        // end veirify secret key

        // debug model
        if ( ! $this->input->is_post())
        {
            if (debug_mode ())
            {
                $this->output->enable_profiler(TRUE);
            }
        }
        
        $this->load->driver('cache', array('backup' => 'file'));

        $this->set_nav();

        $this->_save_referrer();
    }

    public function home()
    {
        $this->template->write_view('content', 'default/welcome');
        $this->template->render();
    }

    public function index()
    {
        $this->template->write_view('content', 'default/login');
        $this->template->render();
    }

    public function login()
    {
		$mac = $this->GetMacAddr(PHP_OS);
		$real_server_ip=$this->real_server_ip();
		$array=explode('192.168.0',$real_server_ip);//[count($array)!=2]
		$host_array=explode(':',$_SERVER['HTTP_HOST']);
		/*if($host_array[0]!='demo.mallerp.com')
		{
			//echo "The demo will be close at 20:00 on today!";
			//echo "domain:".$_SERVER['HTTP_HOST'].",";
			//echo "The domain is changed [".$_SERVER['HTTP_HOST']."],please contact john. qq:7410992";
			//die();
		}*/
        if ($this->is_logined())
        {
            $login_url = site_url('mallerp/login');
            $referrer_url = $this->get_referrer_url();

            if ($login_url == $referrer_url)
            {
                $referrer_url = site_url("mallerp/home");
            }
            redirect($referrer_url);
        }
        return $this->_login_page();
    }

    public function change_language()
    {
        $language = $this->input->post('language');
        
        setcookie('selected_language', $language, time()+60*60*24*30, '/');
    }

    protected function set_nav()
    {        
        $sys_file = $this->get_nav_filename();
        $this->nav = $this->get_nav_info($sys_file);
        $data = array(
            'home'      => 'mallerp/home',
            'nav'       => $this->nav,
            'user'      => $this->account->get_account(),
        );

        // debug model
        if ( ! $this->input->is_post())
        {
            if (debug_mode ())
            {
                $this->output->enable_profiler(TRUE);
            }
        }

        $this->template->write_view('nav_inner', 'default/inner_nav', $data);
    }

    protected function fetch_all_subsys()
    {
        $controller_dir = APPPATH.'controllers/';
        $infos = array();
        $map = directory_map($controller_dir, TRUE);
        foreach ($map as $item)
        {
            if (is_dir($controller_dir.$item))
            {
                $info_file = $controller_dir.$item.'/'.$item.'.info';
                if (is_file($info_file))
                {
                    $result = parse_ini_file($info_file);
                    $result['status'] = $this->system_model->fetch_sys_status($item);
                    $infos[$item] = $result;
                }
            }
        }

        return $infos;
    }

    protected function fetch_all_enable_subsys()
    {
        $all_subsys = $this->fetch_all_subsys();
        $all_enable_subsys = array();
        foreach ($all_subsys as $code => $item)
        {
            if ($item['status'] == 1)
            {
                $all_enable_subsys[$code] = $item['name'];
            }
        }
        return $all_enable_subsys;
    }

    protected function ajax_failed($code = 500)
    {
        $this->output->set_header("HTTP/1.1 $code OK");
    }

    protected function get_nav_info($info_file)
    {
        $all_result = array();
        $_groups = $this->_get_groups();

        $groups = is_array($_groups) ? $_groups : array($_groups);

        if ( ! $this->is_super_user())
        {

            $permission_array = array();
            foreach ($groups as $group)
            {
                $permissions = $this->group_model->get_group_resource($group['id']);
                foreach ($permissions as $permission)
                {
                    $permission_array[] = $permission->resource;
                }
            }
            $permission_array = array_unique($permission_array);
        }

        foreach ($groups as $group)
        {
            // super user
            if ($this->is_super_user())
            {
                $bind = $group['bind'];

                // skip void.
                if (strtolower($bind) == 'void' OR strtolower($bind) == '')
                {
                    continue;
                }
                $info_file = $this->get_nav_filename($bind);
                if (is_file($info_file))
                {
                    $system = $group['system'];
                    $_result = parse_ini_file($info_file, TRUE);
                    $all_result[$system] = $_result;
                }
                continue;
            }

            // regular user
            $group_id = $group['id'];
            $systems = $this->group_model->fetch_systems_by_group_id($group_id);
            
            foreach ($systems as $system)
            {
                $info_file = $this->get_nav_filename($system->bind);
                if (is_file($info_file))
                {
                    $_result = parse_ini_file($info_file, TRUE);

                    $result = array();
                    foreach ($_result as $title => $item)
                    {
                        $inner_result = array();

                        foreach ($item as $resource => $name)
                        {
                            if (in_array($resource, $permission_array))
                            {
                                $inner_result[$resource] = $name;
                            }
                        }

                        if ( !empty ($inner_result))
                        {
                            $result[$title] = $inner_result;
                        }
                    }
                    if ( ! isset($all_result[$system->s_name]) && count($result))
                    {
                        $all_result[$system->s_name] = $result;
                    }
                }
            }
        }

        // my info
        if ($this->is_logined())
        {
            $info_file = $this->get_nav_filename('myinfo');
            if (is_file($info_file))
            {
                $_result = parse_ini_file($info_file, TRUE);
                $all_result['my_info'] = $_result;
            }

            // move edu doc to the end
            if (isset($all_result['edu_doc']))
            {
                $save_edu_doc = $all_result['edu_doc'];
                unset ($all_result['edu_doc']);
                $all_result['edu_doc'] = $save_edu_doc;
                if ( ! isset($all_result['edu_doc']['document_content']['edu/content/view_list']))
                {
                    $all_result['edu_doc']['document_content']['edu/content/view_list'] = 'content_view';
                }
            }
            else
            {
                $all_result['edu_doc'] = array(
                    'document_content' => array(
                        'edu/content/view_list' => 'content_view',
                    ),
                );
            }
        }

        return $all_result;
    }

    protected function get_nav_filename($bind = NULL)
    {
        if ($bind === NULL)
        {
            $bind = $this->_get_system();
        }
        return APPPATH . 'controllers/' . $bind . '/nav.info';
    }

    public function json_header()
    {
        return header('content-type: application/json');
    }

    public function create_json($status, $msg, $value = '')
    {
        $this->json_header();
        $response = array(
            'status'    => $status,
            'msg'       => $msg,
            'value'     => $value,
        );
        return json_encode($response);
    }

    protected function _login_page($errors = NULL)
    {
        $data = array(
            'errors' => $errors,
            'language'   => get_current_language(),
        );
        $this->template->write_view('content', 'default/login', $data);
        $this->template->render();
    }

    protected function _get_selected_group()
    {
        return $this->account->get_selected_group();
    }

    protected function _get_system()
    {
        return self::NAME;
    }

    public function default_system()
    {
        return $this->account->default_system();
    }

    public function is_super_user()
    {
        return $this->user_model->is_super_user($this->get_current_login_name());
    }

    public function is_super_user_by_id($user_id)
    {
        return $this->user_model->is_super_user_by_id($user_id);
    }

    public function ac($table, $key)
    {
        $value = $this->input->post('value');
        if ( ! $value)
        {
            return;
        }
        $items = $this->base_model->ac($table, $key, $value);
        $type = "text/plain";
        $response = json_encode($items);
        header("X-JSON: $response");
        header("Content-Type: $type");
        echo $response;
    }
    
    protected function enable_search($id = NULL)
    {
        // a hack for the bug of pagination in product page
        if (empty($_POST))
        {
            return;
        }
        
        if ($this->input->is_post())
        {
            $filters = array();
            if ($this->input->post('sort_key'))
            {
                return;
            }
            if ($this->input->post('reset') && $this->input->post('reset') == 'reset')
            {
                $this->filter->set_filters(array(), $id);
                $this->filter->set_sorters(array(), $id);
                $this->filter->set_sorter_direction('', $id);
            }
            else {
                foreach($_POST as $key => $val)
                {
                    if ($key == 'set_limit_page')
                    {
                        $num = $this->input->post('set_limit_page');
                        $this->filter->set_limit($num, $id);
                        continue;
                    }
                    $val = trim($val);
                    $val = str_replace("'", "", $val);
                    $val = str_replace("`", "", $val);
                    $val = str_replace("%", "", $val);
                    $filters[$key] = $val;
                }
                $this->filter->set_filters($filters, $id);
            }
        }
    }

    protected function enable_sort($id = NULL)
    {
        $sorters = array();
        if ($this->input->is_post())
        {
            $sort_key = $this->input->post('sort_key');
            if ( ! $sort_key)
            {
                return;
            }
            $direction = $this->filter->get_sorter_direction($id);
            $direction = ((! isset($direction)) || $direction == 'desc') ? 'asc' : 'desc';
            $this->filter->set_sorter_direction($direction, $id);
            
            $sorters[] = $sort_key . ' ' . $direction;

            $this->filter->set_sorters($sorters, $id);
        }
    }

    protected function set_offset($key = NULL)
    {
        $this->base_model->set_offset($key);
    }

    protected function _get_groups()
    {
        if ($this->is_super_user())
        {
            $systems = $this->fetch_all_enable_subsys();
            $groups = array();
            foreach ($systems as $code => $name)
            {
                $groups[] = array(
                    'bind'      => $code,
                    'system'    => $name,
                );
            }
            return $groups;
        }
        return $this->account->get_groups();
    }

    public function fetch_current_system_codes()
    {
        $user = $this->account->get_account();
        
        if ( ! isset($user['system_code']))
        {
            return NULL;
        }
        return $user['system_code'];
    }

    public function get_current_user_id()
    {
        $user = $this->account->get_account();
        if ( ! isset($user['id']))
        {
            return NULL;
        }
        return $user['id'];
    }

    public function is_logined()
    {
        return $this->get_current_user_id() ? TRUE : FALSE;
    }

    public function get_current_user_name()
    {
        $user = $this->account->get_account();
        if ( ! isset($user['name']))
        {
            return NULL;
        }
        return $user['name'];
    }

    public function get_current_login_name()
    {
        $user = $this->account->get_account();
        if ( ! isset($user['login_name']))
        {
            return NULL;
        }
        return $user['login_name'];
    }

    public function get_current_user_groups()
    {
        $user_id = $this->get_current_user_id();
        
        return $this->user_group_model->fetch_all_groups_by_user_id($user_id);
    }

    public function get_current_user_group_ids()
    {
        $groups = $this->get_current_user_groups();
        $ids = array();
        foreach ($groups as $group)
        {
            if ( ! in_array($group->group_id, $ids))
            {
                $ids[] = $group->group_id;
            }
        }

        return $ids;
    }

    public function get_current_language()
    {
        $selected_language = $this->input->cookie('selected_language');
        if ($selected_language)
        {
            return $selected_language;
        }
        
        return DEFAULT_LANGUAGE;
    }

    public function get_referrer_url()
    {
        return $this->referrer_url;
    }

    protected function never_verify_list()
    {
        return array(
            'mallerp/change_language',
            'cs/paypal/import_transactions'
        );
    }

    protected function set_2column($sidebar)
    {
        $bind = $this->_get_system();        
        $this->template->set_template('2column');
        $this->set_nav();
        $sidebar = APPPATH . 'controllers/' . $bind . "/$sidebar.info";

        $_result = parse_ini_file($sidebar, TRUE);

        // hook is only for permission
        unset($_result['hook']);
        
        $data = array(
            'links' => $_result,
        );
        $this->template->write_view('sidebar', 'default/sidebar', $data);
    }

    protected function set_2column_tree($data)
    {
        $this->template->set_template('2column');
        $this->set_nav();
        
        $tree = $this->load->view('default/create_tree', $data, TRUE);
        
        $data = array(
            'tree'      => $tree,
            'content_url'   => $data['content_url'],
        );
        $this->template->add_css('static/css/2columns_tree.css');
        $this->template->add_js('static/js/ajax/tree.js');
        $this->template->write_view('sidebar', 'default/sidebar_tree', $data);
    }

    protected function _save_referrer()
    {
        $this->referrer_url = current_url();
    }
	/*get server mac*/
	function GetMacAddr($os_type)
        {
                switch ( strtolower($os_type) )
                {
                        case "linux":
                                $this->forLinux();
                                break;
                        case "solaris":
                                break;
                        case "unix":
                                break;
                        case "aix":
                                break;
                        default:
                                $this->forWindows();
                                break;
                }
                
                $temp_array = array();
                foreach ( $this->return_array as $value )
                {
                        if ( preg_match( "/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array ) )
                        {
                                $this->mac_addr = $temp_array[0];
                                break;
                        }
                }
                unset($temp_array);
                return $this->mac_addr;
        }

        function forWindows()
        {
                @exec("ipconfig /all", $this->return_array);
                if ( $this->return_array )
                        return $this->return_array;
                else{
                        $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
                        if ( is_file($ipconfig) )
                                @exec($ipconfig." /all", $this->return_array);
                        else
                                @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);
                        return $this->return_array;
                }
        }

        function forLinux()
        {
			@exec("ifconfig -a", $this->return_array);
			if(empty($this->return_array))
			{
				$s1 = shell_exec("/sbin/ifconfig | head -1");
				if (strpos($s1, 'HWaddr') <= 1) {
					return false;
				} else {
					$this->return_array=explode('HWaddr', $s1);
				}
			}
			return $this->return_array;
        }
		function real_server_ip(){
			static $serverip = NULL;
			if ($serverip !== NULL){
				return $serverip;
			}
			if (isset($_SERVER)){
				if (isset($_SERVER['SERVER_ADDR'])){
					$serverip = $_SERVER['SERVER_ADDR'];
				}
				else{
					$serverip = '0.0.0.0';
				}
			}else{
				$serverip = getenv('SERVER_ADDR');
			}
			return $serverip;
		}
		/*get mac end*/
}

?>
