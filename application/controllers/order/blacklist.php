<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Blacklist extends Order
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('blacklist_model');
        $this->load->library('form_validation');
    }
    
    public function add_edit($id = NULL)
    {
        $this->template->add_js('static/js/ajax/keyword.js');

        $blacklist = NULL;
        if ($id)
        {
            $blacklist = $this->blacklist_model->fetch_blacklist($id);
        }

        $data = array(
            'blacklist'   => $blacklist,
        );
        $this->template->write_view('content', 'order/blacklist/add_edit', $data);
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('blacklist');
        $this->enable_sort('blacklist');

        $this->render_list('order/blacklist/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('blacklist');
        $this->enable_sort('blacklist');

        $this->render_list('order/blacklist/management', 'view');
    }


    public function view($id)
    {
        $blacklist = $this->blacklist_model->fetch_blacklist($id);
        $data = array(
            'blacklist'   => $blacklist,
        );
        $this->template->write_view('content', 'order/blacklist/view_detail', $data);
        $this->template->render();
    }


    public function save_blacklist()
    {

        $buyer_id           = $this->input->post('buyer_id');
        $email              = $this->input->post('email');
        
        if($this->input->post('blacklist_id') < 0 )
        {
            if (( ! empty($email) && $this->blacklist_model->check_exists('customer_black_list', array('email' => $email))) ||  ( ! empty($buyer_id) && $this->blacklist_model->check_exists('customer_black_list', array('buyer_id' => $buyer_id))))
            {
                echo $this->create_json(0, lang('buyer_id_or_email_exists'));
                return;
            }
        }
        else
        {
            if ( ! empty($buyer_id) && $buyer_id !== $this->blacklist_model->get_one('customer_black_list', 'buyer_id', array('id' => $this->input->post('blacklist_id'))))
            {
                if ($this->blacklist_model->check_exists('customer_black_list', array('buyer_id' => $buyer_id)))
                {
                    echo $this->create_json(0, lang('buyer_id_or_email_exists'));
                    return;
                }
            }
            if ( ! empty($email) && $email !== $this->blacklist_model->get_one('customer_black_list', 'email', array('id' => $this->input->post('blacklist_id'))))
            {
                if ($this->blacklist_model->check_exists('customer_black_list', array('email' => $email)))
                {
                    echo $this->create_json(0, lang('buyer_id_or_email_exists'));
                    return;
                }
            }
        }

        $platform           = $this->input->post('platform');
        $name       = $this->input->post('name');
        $remark             = $this->input->post('remark');

        if(!$buyer_id  && !$email)
        {
            echo $this->create_json(0, lang('buyer_id_and_email_select_one'));
            return;
        }
        
        $data = array(
            'blacklist_id'                  => $this->input->post('blacklist_id'),
            'platform'                      => $platform,
            'buyer_id'                      => $buyer_id,
            'email'                         => $email,
            'name'                          => $name,
            'remark'                        => trim($this->input->post('remark')),
            'creator_id'                    => get_current_user_id(),
        );

        try
        {
            $blacklist_id = $this->blacklist_model->save_blacklist($data);

            echo $this->create_json(1, lang('save_blacklist_successed'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_blacklist()
    {
        $blacklist_id = $this->input->post('id');
        $this->blacklist_model->drop_blacklist($blacklist_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $blacklists = $this->blacklist_model->fetch_all_blacklists();

        $data = array(
            'blacklists' => $blacklists,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
