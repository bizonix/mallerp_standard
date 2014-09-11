<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Packing extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_packing_model');
        $this->load->library('form_validation');
    }
    
    public function add()
    {
        $this->template->write_view('content', 'pi/add_packing');
        $this->template->render();
    }
    
    public function add_save()
    {
        $rules = array(
            array(
                'field' => 'name_cn',
                'label' => lang('chinese_name'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'name_en',
                'label' => lang('english_name'),
                'rules' => 'trim|required',
            ),
             array(
                'field' => 'image_url',
                'label' => lang('image_url'),
                'rules' => 'trim|is_url',
            ),
            array(
                'field' => 'length',
                'label' => lang('length'),
                'rules' => 'trim|required|positive_numeric',
            ),
            array(
                'field' => 'width',
                'label' => lang('width'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'height',
                'label' => lang('height'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'weight',
                'label' => lang('weight'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'content',
                'label' => lang('content'),
                'rules' => 'trim|required|positive_numeric',
            ),
            array(
                'field' => 'cost',
                'label' => lang('packing_cost'),
                'rules' => 'trim|required|positive_numeric',
            ),
         );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        // check if the chinese name and the english name exists ?
        if ($this->product_packing_model->check_exists('product_packing', array('name_cn' => $this->input->post('name_cn'))))
        {
            echo $this->create_json(0, lang('product_packing_exists'));

            return;
        }
            
       $data = array(
            'name_cn'           => trim($this->input->post('name_cn')),
            'name_en'           => trim($this->input->post('name_en')),
            'image_url'         => trim($this->input->post('image_url')),
            'length'            => trim($this->input->post('length')),
            'width'             => trim($this->input->post('width')),
            'height'            => trim($this->input->post('height')),
            'weight'            => trim($this->input->post('weight')),
            'content'           => trim($this->input->post('content')),
            'cost'              => price(trim($this->input->post('cost'))),
        );

        try
        {
            $this->product_packing_model->add_a_new_packing($data);
            echo $this->create_json(1, lang('product_packing_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }     
    }

    public function edit($id = NULL)
    {
        $this->edit_view_packing('pi/edit_packing','edit',$id);
    }

    public function edit_save()
    {
        $rules = array(
            array(
                'field' => 'name_cn',
                'label' => lang('chinese_name'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'name_en',
                'label' => lang('english_name'),
                'rules' => 'trim|required',
            ),
             array(
                'field' => 'image_url',
                'label' => lang('image_url'),
                'rules' => 'trim|is_url',
            ),
            array(
                'field' => 'length',
                'label' => lang('length'),
                'rules' => 'trim|required|positive_numeric',
            ),
            array(
                'field' => 'width',
                'label' => lang('width'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'height',
                'label' => lang('height'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'weight',
                'label' => lang('weight'),
                'rules' => 'trim|required|positive_numeric',
            ) ,
            array(
                'field' => 'content',
                'label' => lang('content'),
                'rules' => 'trim|required|positive_numeric',
            ),
            array(
                'field' => 'cost',
                'label' => lang('packing_cost'),
                'rules' => 'trim|required|positive_numeric',
            ),
         );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

       $data = array(
            'name_cn'           => trim($this->input->post('name_cn')),
            'name_en'           => trim($this->input->post('name_en')),
            'image_url'         => trim($this->input->post('image_url')),
            'length'            => trim($this->input->post('length')),
            'width'             => trim($this->input->post('width')),
            'height'            => trim($this->input->post('height')),
            'weight'            => trim($this->input->post('weight')),
            'content'           => trim($this->input->post('content')),
            'cost'              => price(trim($this->input->post('cost'))),
        );

        try
        {
            $id = $this->input->post('packing_id');
            $this->product_packing_model->update_product_packing($id,$data);
            echo $this->create_json(1, lang('product_packing_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_product_packing()
    {
        $packing_id = $this->input->post('id');
        $this->product_packing_model->drop_product_packing($packing_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function manage()
    {
        $this->render_list('pi/packing_management', 'edit');
    }

    private function render_list($url, $action)
    {
        $product_packings = $this->product_packing_model->fetch_all_product_packing();

        $data = array(
            'product_packings'  => $product_packings,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    private function edit_view_packing($url, $action, $id)
    {
        $product_packing = NULL;
        if ($id)
        {
            $product_packing = $this->product_packing_model->fetch_product_packing($id);
        }
        $data = array(
            'product_packing'  => $product_packing,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function get_packing_by_id()
    {
        $packing_id = $this->input->post('id');

        if( ! $packing_id)
        {
            return;
        }
        $product_packing = $this->product_packing_model->fetch_product_packing($packing_id);
        
        $str = "<a target='_blank' href='$product_packing->image_url'><img src='$product_packing->image_url' height='100'/></a>";
        $str .= lang('length').' : '.$product_packing->length .', '.lang('width').' : '.$product_packing->width.', '.  lang('height').' ：'.$product_packing->height . ', '.  lang('weight').' ：' . $product_packing->weight;
        
        echo $str;
    }

}
?>