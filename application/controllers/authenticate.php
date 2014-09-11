<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Authenticate extends Mallerp
{
    public function __construct() {
        parent::__construct();
        
        $this->load->library('encrypt');
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('user_group_model');
        $this->load->model('mixture_model');
    }
    
    public function login()
    {
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));
        $data = array();

        // start vefiry
        $rules = array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required|xss_clean',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|xss_clean',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            return $this->_login_page(validation_errors());
        }
        if ( ! $this->_verify_user($username, $password))
        {
            return $this->_login_page(lang('username_or_password_dont_exist'));
        }
        // end of verify

        // save user session
        $session = array();

        // super user
        if ($this->user_model->is_super_user($username))
        {
            $user_id = $this->user_model->fetch_user_id_by_name($username);
            $session['account'] = array(
                'name'          => $username,
                'login_name'    => 'admin',
                'id'            => $user_id,
            );
            $session['default_system'] = 'admin';
            $this->account->set_account($session);
        }
        else
        {
            $groups = $this->user_group_model->fetch_all_groups_by_username($username);

            foreach ($groups as $group)
            {
                if ( ! isset($session['default_system']))
                {
                    $session['default_system'] = $group->system_code;
                }
                $session['groups'][] = array(
                    'name'      => $group->g_name,
                    'id'        => $group->group_id,
                );
            }
        }
        $user = $this->user_model->fetch_user_info($username);
        if ($user)
        {
            $user_id = $user->id;
            $session['account'] = array(
                'name'          => $user->name,
                'login_name'    => $user->login_name,
                'id'            => $user_id,
            );
        }

        $this->account->set_account($session);
        
        $session['account']['system_code'] = $this->user_model->fetch_current_system_codes(get_current_user_id());
        $this->account->set_account($session);

        $user_ip_address = $this->input->ip_address();
        $user_agent = $this->input->user_agent();
        $this->mixture_model->save_user_login_log($user_id, $user_ip_address, $user_agent);

        redirect(site_url("mallerp/home"));
    }

    public function logout()
    {
        $this->account->renew_account();
        redirect('');
    }

    private function _verify_user($username, $password)
    {
        if ($this->user_model->check_exists(
            'user',
            array('login_name' => $username, 'password' => md5($password)))
        )
        {
            return TRUE;
        }
        // first time use this system? check superuser.install.
        if (is_file(APPPATH.'controllers/admin/superuser.install') && $this->user_model->count('user') == 0)
        {
            $result = parse_ini_file(APPPATH.'controllers/admin/superuser.install');
            
            if ($result['name'] == $username && $result['password'] == $password)
            {
                $data = array(
                    'name'      => $username,
                    'password'  => md5($password),
                    'role'      => 1,
                    'level'     => 1,
                );
                $this->user_model->add_user($data);
                
                return TRUE;
            }
        }
        return FALSE;
    }
}

?>
