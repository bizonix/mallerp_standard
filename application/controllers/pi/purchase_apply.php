<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Purchase_apply extends Pi
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->library('form_validation');
    }

    public function manage()
    {
        $this->enable_search('purchase_apply');
        $this->render_list('pi/purchase_apply/management', 'edit');
    }

    public function view($id)
    {
        $this->template->add_js('static/js/ajax/purchase.js');

        $apply = $this->purchase_model->fetch_purchase_apply_by_id($id);
        
        $data = array(
            'apply'             => $apply,
            'action'            => 'view',
        );
        $this->template->write_view('content', 'pi/purchase_apply/view_edit', $data);
        $this->template->render();
    }

    public function edit($id)
    {
        $this->template->add_js('static/js/ajax/purchase.js');
        
        $apply = $this->purchase_model->fetch_purchase_apply_by_id($id);

        $data = array(
            'apply'             => $apply,
            'action'            => 'edit',
        );
        $this->template->write_view('content', 'pi/purchase_apply/view_edit', $data);
        $this->template->render();
    }

    public function delete_apply($id)
    {
        $this->purchase_model->drop_apply($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }


    private function render_list($url, $action)
    {
        $applys = $this->purchase_model->fetch_editor_purchase_apply();

        $data = array(
            'applys'    => $applys,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
