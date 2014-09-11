<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Template extends Admin
{
    private $shipped_notification_filename;
    public function  __construct()
    {
        parent::__construct();

        $this->load->library('parser');
        $this->shipped_notification_filename = realpath(APPPATH) . '/views/local/english/template/email/order_shipped_notification' . EXT;
    }

    public function shipped_notification()
    {
        $data = array(
            'shipped_notification_filename' => $this->shipped_notification_filename,
        );

        $this->template->write_view('content', 'admin/template/shipped_notification', $data);
        $this->template->add_js('static/js/ajax/admin.js');
        $this->template->render();
    }

    public function view_shipped_notification()
    {
        $data = array(
            'buyer_name'        => 'lion',
            'item_no'           => 'No-5',
            'shipped_date'      => '2012',
            'item_list_entries' => array(
                array(
                    'item_name' => 'toy',
                    'sku'       => 'A23',
                    'qty'       => 34,
                ),
                array(
                    'item_name' => 'toy2',
                    'sku'       => 'A232',
                    'qty'       => 342,
                ),
            ),
            'weight'            => 23.43,
            'shipping_address'  => 'USA<BR/>NY',
            'track_number'      => 'LXDDFDE',
            'track_url'         => 'www.mallerp.com',
            'shipping_method'   => 'DHL2',
            'email'             => 'john@mallerp.com'

        );
        $view = 'local/english/template/email/order_shipped_notification';
        $this->parser->parse($view, $data);
    }

    public function edit_shipped_notification()
    {
        $template_content = $this->input->post('template_content');

        try
        {
            // backup content.
            $backup_filename = $this->shipped_notification_filename . ".backup";
            file_put_contents($backup_filename, file_get_contents($this->shipped_notification_filename));

            // save content.
            file_put_contents($this->shipped_notification_filename, $template_content);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        
    }

}

?>
