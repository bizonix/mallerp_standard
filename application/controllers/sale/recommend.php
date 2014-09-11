<?php
require_once APPPATH.'controllers/sale/sale'.EXT;

class Recommend extends Sale
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('quality_testing_model');
        $this->load->model('order_model');

        $this->load->library('form_validation');
    }

    public function manage()
    {
        $this->enable_search('recommend');
        $this->enable_sort('recommend');

        $this->render_list('sale/recommend_management', 'edit');
    }

    public function drop_recommend()
    {
        $recommend_id = $this->input->post('id');
        $this->quality_testing_model->drop_recommend($recommend_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $recommends = $this->quality_testing_model->fetch_all_recommends();
        $data = array(
            'recommends' => $recommends,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function instant_save_recommend_order()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
            $user_name = get_current_login_name();
            $this->quality_testing_model->instant_save_order($id, $type, $value, $user_name);
            if($type == 'status' || $type == 'cause' || $type == 'finish_status' )
            {
                $value = lang($value);
            }
            echo $this->create_json(1, lang('ok'),$value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}

?>
