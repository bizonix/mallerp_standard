<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Product extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('shipping_code_model');
        $this->load->model('product_catalog_model');
        $this->load->model('product_packing_model');
        $this->load->model('purchase_model');
        $this->load->library('form_validation');
		$this->load->library('excel');
        $this->load->helper('product_permission');
    }

    public function add_edit($id = NULL)
    {
        $this->template->add_js('static/js/ajax/product.js');
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_js('static/js/upload/ajaxupload.js');
        $this->template->add_css('static/css/accordion.css');
        
        $parent_catalog = $this->product_catalog_model->fetch_all_product_catalog();
        $parent_catalog = $this->_make_tree($parent_catalog);
        $product_packing = $this->product_packing_model->fetch_all_product_packing();
        $all_purchase_users = $this->user_model->fetch_users_by_system_code('purchase');
		$all_users = $this->user_model->fetch_all_users();

        $apply_tag =  $this->input->post('apply_tag');
        $apply_id =  $this->input->post('apply_id');

        $data = array(
            'catalogs'              => $parent_catalog,
            'apply_tag'             => $apply_tag ? $apply_tag : '0',
            'language'              =>  $this->get_current_language(),
        );
        
        if ($id)
        {
            $product_catalog = $this->product_model->fetch_product_catalog($id);
            
            {
                $catalog_id = $this->product_model->get_one('product_basic', 'catalog_id', array('id' => $id));
                $coefficient = array();

                $temp_id = $catalog_id;
                do
                {
                    $catalog_obj = $this->product_catalog_model->fetch_product_catalog($temp_id);
                    if ( ! $catalog_obj)
                    {
                        break;
                    }
                    $coefficient[] = $catalog_obj;
                    $temp_id = $catalog_obj->parent;

                }
                while ($catalog_obj->parent > 0);
            }

            $product = $this->product_model->fetch_product($id);

            $dev_name = $this->product_model->get_one('user', 'name', array('id' => $product->product_develper_id));

            $providers = $this->product_model->fetch_product_providers($id);
            $sku = $this->product_model->fetch_product_sku($id);
            $path = $this->_get_upload_path($sku);
            $uploaded_images = directory_map($path);
            if (empty($uploaded_images))
            {
                $uploaded_images = array();
            }

            $ebay_path = $this->_get_upload_path($sku, 'gallery');
			$upload_file_path = $this->_get_upload_path($sku, 'uploads');
            $ebay_uploaded_images = directory_map($ebay_path);
            if (empty($ebay_uploaded_images))
            {
                $ebay_uploaded_images = array();
            }
			$ad_code_uploaded = directory_map($upload_file_path);
			if (empty($ad_code_uploaded))
            {
                $ad_code_uploaded = array();
            }
            
            $data = array(
                'product'               => $product,
                'product_catalog'       => $product_catalog,
                'catalogs'              => $parent_catalog,
                'product_packing'       => $product_packing,
                'providers'             => $providers,
                'product_id'            => $id,
                'all_purchase_users'    => $all_purchase_users,
                'uploaded_images'       => $uploaded_images,
				'ad_code_uploaded'      => $ad_code_uploaded,
                'ebay_uploaded_images'  => $ebay_uploaded_images,
                'uploaded_path'         => $path,
                'ebay_uploaded_path'    => $ebay_path,
				'upload_file_path'   	=> $upload_file_path,
                'coefficient'           => $coefficient,
                'dev_name'              => $dev_name,
                'stock_code'            => $this->shipping_code_model->fetch_stock_codes_status(),
                'all_out_packing'       => $this->product_model->fetch_statuses('out_packing'),
				'all_users'				=> $all_users,
            );

            $this->template->write_view('content', 'pi/add_edit', $data);
            $this->template->render();

            return ;
        }
        else
        {
            if($this->input->post('apply_tag') == '1')
            {
                $data['product_name']                      =  trim($this->input->post('product_name'));
                $data['product_image_url']                 =  trim($this->input->post('product_image_url'));
                $data['product_description']               =  trim($this->input->post('product_description'));
                $data['apply_id']                          =  trim($this->input->post('apply_id'));
            }
            $this->template->write_view('content', 'pi/create', $data);
            $this->template->render();
        }
    }

   public function manage()
    {
        $this->enable_sort('product');

        
        if ($this->input->is_post())
        {
			$cat_id = $this->input->post('id');
            if ($cat_id)
            {
                $this->session->set_userdata('current_catalog_id', $cat_id);
            }
			if(!isset($_POST['id']))
			{
				$cat_id = $this->session->userdata('current_catalog_id');
			}
            unset($_POST['id']);
            unset($_POST['action']);
            
            $this->enable_search('product');
            $products = $this->product_model->fetch_all_product($cat_id);
            
            $product_abroad_skus = $this->product_model->fetch_abroad_skus();

            $data = array(
                'can_delete'     => $this->product_model->has_delete_permission(get_current_user_id()),
                'products'      => $products,
                'action'        => 'edit',
                'abroad_skus'        => $product_abroad_skus,
            );
            $this->load->view('pi/management', $data);
            return ;
        }else{
			if($this->session->userdata('current_catalog_id')>0)
			{
			}else{
				$this->session->set_userdata('current_catalog_id', -1);
			}
			
		}
        $this->enable_search('product');

        
        
        $product_abroad_skus = $this->product_model->fetch_abroad_skus();
        
        $data = array(
            'can_delete'     => $this->product_model->has_delete_permission(get_current_user_id()),
            'content_url'    => site_url('pi/product/manage'),
            'child_tree_url' => site_url('pi/catalog/fetch_child_catalogs_edit_tree'),
            'abroad_skus'        => $product_abroad_skus,
        );
        $this->render_list('pi/management', 'edit', $data);
    }

    public function view_list()
    {
        $this->enable_sort('product');
        
        $product_abroad_skus = $this->product_model->fetch_abroad_skus();
        
        if ($this->input->is_post())
        {
            $cat_id = $this->input->post('id');
            if ($cat_id)
            {
                $this->session->set_userdata('current_catalog_id', $cat_id);
            }
			if(!isset($_POST['id']))
			{
				$cat_id = $this->session->userdata('current_catalog_id');
			}
            unset($_POST['id']);
            unset($_POST['action']);

            $this->enable_search('product');
            $products = $this->product_model->fetch_all_product($cat_id);

            $data = array(
                'products'      => $products,
                'action'        => 'view',
                'abroad_skus'        => $product_abroad_skus,
            );
            $this->load->view('pi/management', $data);
            return ;
        }else{
			if($this->session->userdata('current_catalog_id')>0)
			{
			}else{
				$this->session->set_userdata('current_catalog_id', -1);
			}
			
		}
        $this->enable_search('product');
        $data = array(
            'content_url'    => site_url('pi/product/view_list'),
            'child_tree_url' => site_url('pi/catalog/fetch_child_catalogs_view_tree'),
            'abroad_skus'        => $product_abroad_skus,
        );
        $this->render_list('pi/management', 'view', $data);
    }

    public function view($id)
    {
        $product = $this->product_model->fetch_product($id);
        $product_catalog = $this->product_model->fetch_product_catalog($id);

        {
            $catalog_id = $this->product_model->get_one('product_basic', 'catalog_id', array('id' => $id));
            $coefficient = array();

            $temp_id = $catalog_id;
            do{
                $catalog_obj = $this->product_catalog_model->fetch_product_catalog($temp_id);
                $coefficient[] = $catalog_obj;
                $temp_id = $catalog_obj->parent;

            }while ($catalog_obj->parent != '-1');
        }

        $sku = $this->product_model->fetch_product_sku($id);

        $path = $this->_get_upload_path($sku);
		$upload_file_path = $this->_get_upload_path($sku, 'uploads');

        $uploaded_images = directory_map($path);
        
        if (empty($uploaded_images))
        {
            $uploaded_images = array();
        }
		$ad_code_uploaded = directory_map($upload_file_path);


        $data = array(
            'uploaded_images'   => $uploaded_images,
			'ad_code_uploaded'  => $ad_code_uploaded,
            'uploaded_path'     => $path,
			'upload_file_path'  => $upload_file_path,
            'product'           => $product,
            'action'            => 'view',
            'product_catalog'   => $product_catalog,
            'coefficient'       => $coefficient,
            'stock_code'        => $this->shipping_code_model->fetch_stock_codes_status(),
            'all_out_packing'        => $this->product_model->fetch_statuses('out_packing'),
        );
        $this->load->view('pi/view_detail', $data);
    }

    public function save_new()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'name_cn',
                'label' => 'chinese name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'name_en',
                'label' => 'english name',
                'rules' => 'trim|required',
            )
        );
    
        $this->push_rules(
            $rules,
            array(
                'field' => 'sku',
                'label' => 'SKU',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'catalog_id',
                'label' => 'Product catalog',
                'rules' => 'trim|required',
            )
        );
        
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if ($this->product_model->check_exists('product_basic', array('sku' => trim($this->input->post('sku')))))
        {
            echo $this->create_json(0, lang('product_sku_exists'));
            return;
        }
        $parent = $this->input->post('parent');
        
        if(empty ($parent))
        {
            echo $this->create_json(0, lang('catalog_required'));
            return;
        }
		if($parent=='-1'||$parent==-1)
        {
            echo $this->create_json(0, lang('catalog_required'));
            return;
        }
        
        $catalog_obj = $this->product_catalog_model->fetch_product_catalog($parent);
        
        $sku = strtoupper(trim($this->input->post('sku')));
        $data_base = array(
            'product_id'            => $this->input->post('product_id'),
            'name_cn'               => trim($this->input->post('name_cn')),
            'name_en'               => trim($this->input->post('name_en')),
            'sku'                   => $sku,
            'catalog_id'            => $parent,
            'tester_id'             => $catalog_obj->tester_id,
            'seo_user_id'           => $catalog_obj->seo_user_id,
            'stock_user_id'           => $catalog_obj->stock_user_id,
            'product_develper_id'   => get_current_user_id(),
        );

        try
        {
            if($this->input->post('apply_tag') == '1')
            {
                $user = array();
                $user = $this->account->get_account();
                $data = array(
                    'apply_status'                      => 3,
                    'edit_user_id'                      => $user['id'] ,
                    'sku'                               => $sku ,
                );
                $apply_id = $this->input->post('apply_id');
                $develper_id = $this->product_model->get_one('product_purchase_apply', 'develper_id', array('id' => $apply_id));
                $data_base['product_develper_id'] = $develper_id;

                $this->purchase_model->update_purchase_apply_by_id($apply_id, $data);
            }

            $product_id = $this->product_model->save_product_base($data_base);

            $message = $this->messages->load('new_product_message');
            $this->events->trigger(
                'new_product_created_after',
                array(
                    'type'          => 'new_product_message',
                    'click_url'     => site_url('pi/product/add_edit', array($product_id)),
                    'content'       => lang($message['message']),
                    'owner_id'      => $this->get_current_user_id(),
                )
            );
            echo $this->create_json(1, lang('product_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function save_edit()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'name_cn',
                'label' => 'chinese name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'name_en',
                'label' => 'english name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'sku_other',
                'label' => 'sku_other',
                'rules' => 'trim',
            )
        );
		
		$this->push_rules(
            $rules,
            array(
                'field' => 'sku',
                'label' => 'SKU',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'pure_weight',
                'label' => 'pure weight',
                'rules' => 'trim|required|positive_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'width',
                'label' => 'width',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'length',
                'label' => 'length',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'height',
                'label' => 'height',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'image_url',
                'label' => 'image url',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'video_url',
                'label' => 'video url',
                'rules' => 'trim|is_url',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'box_contain_number',
                'label' => 'box contain number',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'box_total_weight',
                'label' => 'box total weight',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'box_width',
                'label' => 'box width',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'box_height',
                'label' => 'box height',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'box_length',
                'label' => 'box length',
                'rules' => 'trim|positive_zero_numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'stock_code',
                'label' => 'stock code',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'shelf_code',
                'label' => 'shelf code',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'bulky_cargo',
                'label' => 'bulky cargo',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'description',
                'label' => 'description',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'short_description',
                'label' => 'short description',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'description_cn',
                'label' => 'description_cn',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'short_description_cn',
                'label' => 'short description_cn',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'min_stock_number',
                'label' => 'min stock number',
                'rules' => 'trim|required|numeric',
            )
        );
        $this->push_rules(
            $rules,
            array(
                'field' => 'au_min_stock_number',
                'label' => 'au_min stock number',
                'rules' => 'trim|required|numeric',
            )
        );
        $this->push_rules(
            $rules,
            array(
                'field' => 'uk_min_stock_number',
                'label' => 'uk_min stock number',
                'rules' => 'trim|required|numeric',
            )
        );
        $this->push_rules(
            $rules,
            array(
                'field' => 'de_min_stock_number',
                'label' => 'de_min stock number',
                'rules' => 'trim|required|numeric',
            )
        );
		$this->push_rules(
            $rules,
            array(
                'field' => 'yb_min_stock_number',
                'label' => 'yb_min stock number',
                'rules' => 'trim|required|numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'packing_or_not',
                'label' => 'packing or not',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'packing_material',
                'label' => 'packing material',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'sale_amount_level',
                'label' => 'sale amount level',
                'rules' => 'trim|numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'sale_quota_level',
                'label' => 'sale quota level',
                'rules' => 'trim|numeric',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'lowest_profit',
                'label' => 'lowest profit',
                'rules' => 'trim|numeric',
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if($this->input->post('shelf_code'))
        {
            $shelf_code = trim($this->input->post('shelf_code'));

            if ( ! $this->product_model->check_exists('product_shelf_code', array('name' => $shelf_code)))
            {
                echo $this->create_json(0, lang('shelf_code_inexistence'));
                return;
            }
        }
        
        $parent = $this->input->post('parent');

        $pure_weight = trim($this->input->post('pure_weight'));

        $packing_material = trim($this->input->post('packing_material'));
        $pack_weight = $this->product_model->get_one('product_packing', 'weight', array('id' => $packing_material));

        $box_total_weight = trim($this->input->post('box_total_weight'));

        $product_id = $this->input->post('product_id');

        $min_stock_number           = $this->input->post('min_stock_number');
        $min_stock_number_count     = $min_stock_number !== false ? trim($min_stock_number) : 0 ;
        $uk_min_stock_number        = $this->input->post('uk_min_stock_number');
        $uk_min_stock_number_count  = $uk_min_stock_number !== false ? trim($uk_min_stock_number) : 0 ;
        $de_min_stock_number        = $this->input->post('de_min_stock_number');
        $de_min_stock_number_count  = $de_min_stock_number !== false ? trim($de_min_stock_number) : 0 ;
        $au_min_stock_number        = $this->input->post('au_min_stock_number');
        $au_min_stock_number_count  = $au_min_stock_number !== false ? trim($au_min_stock_number) : 0 ;
		$yb_min_stock_number        = $this->input->post('yb_min_stock_number');
        $yb_min_stock_number_count  = $yb_min_stock_number !== false ? trim($yb_min_stock_number) : 0 ;

        $base_url = rtrim(base_url(), '/');

        $catalog_id = $this->input->post('parent');
        
        $data = array(
            'product_id'            => $product_id,
            'name_cn'               => trim($this->input->post('name_cn')),
            'name_en'               => trim($this->input->post('name_en')),
            'sku'                   => strtoupper(trim($this->input->post('sku'))),
			'sku_other'             => strtoupper(trim($this->input->post('sku_other'))),
            'catalog_id'            => $catalog_id,
            'description'           => trim($this->input->post('description')),
            'short_description'     => trim($this->input->post('short_description')),
            'description_cn'        => trim($this->input->post('description_cn')),
            'short_description_cn'  => trim($this->input->post('short_description_cn')),
            'pure_weight'           => $pure_weight,
            'width'                 => trim($this->input->post('width')),
            'length'                => trim($this->input->post('length')),
            'height'                => trim($this->input->post('height')),
            'image_url'             => str_replace(ltrim($base_url,"http://"), '', str_replace($base_url, '', trim($this->input->post('image_url')))),
            'video_url'             => trim($this->input->post('video_url')),
			'buy_url'             => trim($this->input->post('buy_url')),
            'market_model'          => trim($this->input->post('market_model')),
            'box_contain_number'    => trim($this->input->post('box_contain_number')),
            'box_total_weight'      => $box_total_weight,
            'box_width'             => trim($this->input->post('box_width')),
            'box_height'            => trim($this->input->post('box_height')),
            'box_length'            => trim($this->input->post('box_length')),
            'stock_code'            => trim($this->input->post('stock_code')),
            'bulky_cargo'           => trim($this->input->post('bulky_cargo')),
            'packing_or_not'        => trim($this->input->post('packing_or_not')),
            'packing_material'      => $packing_material,
            'sale_status'           => trim($this->input->post('sale_status')),
			'price'					=> price($this->input->post('price')),
			'sale_price'			=> price($this->input->post('sale_price')),
//            'forbidden_level'       => trim($this->input->post('forbidden_level')),
            'lowest_profit'         => trim($this->input->post('lowest_profit')),
            'picture_url'           => trim($this->input->post('picture_url')),
            'pack_cost'             => trim($this->input->post('pack_cost')),
            'product_develper_id'   => trim($this->input->post('product_develper_id')),
        );

        if($min_stock_number>=0)
        {
            $data['min_stock_number'] = $min_stock_number_count;
        }
        if($uk_min_stock_number>=0)
        {
            $data['uk_min_stock_number'] = $uk_min_stock_number_count;
        }
        if($de_min_stock_number>=0)
        {
            $data['de_min_stock_number'] = $de_min_stock_number_count;
        }
        if($au_min_stock_number>=0)
        {
            $data['au_min_stock_number'] = $au_min_stock_number_count;
        }
		if($yb_min_stock_number>=0)
        {
            $data['yb_min_stock_number'] = $yb_min_stock_number_count;
        }

        if ( ! empty($shelf_code))
        {
            $data['shelf_code'] = $shelf_code;
        }
        if ($pack_weight > 0)
        {
            $fill_material_heavy = trim($this->input->post('fill_material_heavy'));
            if ($fill_material_heavy === FALSE)
            {
                $product_object = $this->product_model->fetch_product($product_id);
                
                $fill_material_heavy = $product_object->fill_material_heavy;
            }
            else
            {
                $data['fill_material_heavy'] = $fill_material_heavy;
            }
            $total_weight = $pure_weight + $pack_weight + $fill_material_heavy;
            $data['total_weight'] = $total_weight;
        }

        if($this->input->post('product_develper_by_purchase') != '-1')
        {
            $data['product_develper_id'] = $this->input->post('product_develper_by_purchase');
        }

        if ($this->input->post('purchaser_id') > 0)
        {
            $data['purchaser_id'] = $this->input->post('purchaser_id');
        }

        try
        {
            $product_id = $this->product_model->save_product_base($data);
            
            $forbidden_levels = $this->input->post('forbidden_level') ;
            if (empty($forbidden_levels))
            {
                $forbidden_levels = array();
            }
            $this->product_model->save_pruduct_forbidden_level($product_id, $forbidden_levels);

            echo $this->create_json(1, lang('product_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_product()
    {
        $product_id = $this->input->post('id');
        $this->product_model->drop_product($product_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function fetch_all_products_by_cat_id()
    {
        $cat_id = $this->input->post('id');
        $action = $this->input->post('action');
        $products = $this->product_model->fetch_all_product($cat_id);
        
        $data = array(
            'products'  => $products,
            'action'    => $action,
        );
        $this->load->view('pi/management', $data);
    }

    private function render_list($url, $action, $data = array())
    {
        $products = $this->product_model->fetch_all_product();
        
        $level = 1;
        $parent_id = -1;
        $cats = $this->product_catalog_model->fetch_child_catalogs_tree($parent_id, $level);
        $data['cats'] = $cats;
        $this->set_2column_tree($data);

        $data = array(
            'products'  => $products,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    protected function push_rules(&$rules, $cond)
    {
        $field = $this->input->post($cond['field']);

        if ($field !== FALSE)
        {
            $rules[] = $cond;
        }
    }

    public function fetch_catalog_name_cn($id = NULL)
    {
        $catalog = $this->product_catalog_model->fetch_catalog_name($id);

        return $catalog->name_cn;

    }

    public function fetch_catalog_name_en($id = NULL)
    {
        $catalog = $this->product_catalog_model->fetch_catalog_name($id);

        return $catalog->name_en;

    }

    public function upload_images($type = 'images')
    {
        $image_folder = 'images';
        if (isset($key))
        {
            $image_folder = $type;
        }
        $this->load->library('upload');
        $product_id = $this->input->post('product_id');
        $sku = $this->product_model->fetch_product_sku($product_id);

        if (empty ($product_id))
        {
            return;
        }

        $error = 0;
        $upload_images = $_FILES['upload_images'];
        for ($i = 0; $i < count($upload_images['name']); $i++) {

            $_FILES['userfile']['name'] = $upload_images['name'][$i];
            $_FILES['userfile']['type'] = $upload_images['type'][$i];
            $_FILES['userfile']['tmp_name'] = $upload_images['tmp_name'][$i];
            $_FILES['userfile']['error'] = $upload_images['error'][$i];
            $_FILES['userfile']['size'] = $upload_images['size'][$i];

            $config['file_name'] = $this->_create_image_name($sku, $product_id, $image_folder);
            $config['upload_path'] = $this->_get_upload_path($sku, $image_folder);
            $config['allowed_types'] = 'jpg|jpeg|gif|png';

            $this->upload->initialize($config);

            if ($this->upload->do_upload()) {
                $error += 0;
            } else {
                $error += 1;
            }
        }

        if ($error > 0) {
            echo $this->upload->display_errors('<p>', '</p>');
            exit;
        }

        $url = site_url('pi/product/add_edit', array($product_id));
        $this->output->set_header("Location: $url");
    }

    public function delete_uploaded_image($type = 'images')
    {
        $image_folder = 'images';
        if (isset($key))
        {
            $image_folder = $type;
        }
        $image = $this->input->post('image_name');
        $sku = $this->input->post('sku');
        if (empty ($image) OR empty ($sku))
        {
            return;
        }
        $product_id = $this->product_model->fetch_product_id($sku);


        if (unlink($this->_get_upload_path($sku, $image_folder) . $image))
        {
            echo $this->create_json(1, lang('ok'));
        }
        else
        {
            echo $this->create_json(0, lang('error_msg'));
        }        
    }

    public function sale_amount_level()
    {
        echo 'Lion';
    }

    public function sale_quota_level()
    {
        echo 'Lion 2';
    }

    private function _get_upload_path_file($sku, $product_id, $image_folder)
    {
        return $this->_get_upload_path($sku, $image_folder) . $this->_create_image_name($sku, $product_id, $image_folder);
    }

    private function _get_upload_path($sku, $image_folder = 'images')
    {
        $dir = "./$image_folder/" . substr($sku, 0, 1) . '/' . trim($sku). '/';

        if ( ! is_dir($dir))
        {
            @mkdir($dir, 0777, TRUE);
        }
        
        return $dir;        
    }

    private function _create_image_name($sku, $product_id, $image_folder = 'images')
    {
        $dir = $this->_get_upload_path($sku, $image_folder);
        $map = directory_map($dir, 1);

        foreach ($map as $key => $value)
        {
            $extension = strrchr($value, ".");
            $map[$key] = substr($value, 0, -strlen($extension));
        }
        
        $start = 0;
        $name = '';
        // try to get a file name.
        while(1)
        {
            $start++;
            $name = $sku . '-' . $start . '-' . substr(md5($product_id), 4, 4);
            if ( ! in_array($name, $map))
            {
                break;
            }
        }

        return $name;
    }

    private function _make_tree($parent_catalogs)
    {
        $tree = array("0" => lang('please_select'));
        $names = array();
        /*foreach ($parent_catalogs as $cat)
        {
            $path = $cat->path;
            $names[$cat->id] = $cat->name_cn;
            $items = explode('>', $path);
            $item_names = array();
            
            $space_counter = 0;

            foreach ($items as $item)
            {
                $tree[$item] = repeater('&nbsp;&nbsp;', $space_counter) . element($item, $names);
                $space_counter++;
            }

            //flat_to_multi($item_names, $tree);
        }*/
		$data=array();
		foreach ($parent_catalogs as $cat)
        {
            $data[$cat->id]=array('id'=>$cat->id,'parentid'=>(int)$cat->parent,'name'=>$cat->name_cn);
        }
		$this->load->library('tree',$data);
		$tree = array(-1 => lang('please_select'));
		$catalogs=$this->tree->getArray(-1);
		foreach($catalogs as $catalog)
		{
			$tree[$catalog['id']]=$catalog['name'];
		}

        return $tree;
    }
	
	//add by mallerp
    public function upload_file($type = 'uploads') {
        $file_folder = 'uploads';
        if (isset($key)) {
            $file_folder = $type;
        }
        $this->load->library('upload');
        $product_id = $this->input->post('product_id');
        
        if (empty($product_id)) {
            return;
        }

        $error = 0;
        $upload_files = $_FILES['upload_file'];

        for ($i = 0; $i < count($upload_files['name']); $i++) {

            $_FILES['userfile']['name'] = $upload_files['name'][$i];
            $_FILES['userfile']['type'] = $upload_files['type'][$i];
            $_FILES['userfile']['tmp_name'] = $upload_files['tmp_name'][$i];
            $_FILES['userfile']['error'] = $upload_files['error'][$i];
            $_FILES['userfile']['size'] = $upload_files['size'][$i];

            //$file_name = $this->_create_file_name($product_id, $product_id, $file_folder);
			$file_name = $upload_files['name'][$i];
            $config['file_name'] = $file_name;
			
			$sku = $this->product_model->fetch_product_sku($product_id);
            $file_url = $this->_get_upload_path($sku, $file_folder);

            $config['upload_path'] = $file_url;
            $config['allowed_types'] = '*';

            $this->upload->initialize($config);

            if ($this->upload->do_upload()) {
                $error += 0;
            } else {
                $error += 1;
            }


            $file_data = $this->upload->data();

            //$url = $file_url . $file_name . $file_data['file_ext'];
            //$url = substr($url, 2);

        }
        if ($error > 0) {
            echo $this->upload->display_errors('<p>', '</p>');
            exit;
        }

        $url = site_url('pi/product/add_edit/', array($product_id));
        $this->output->set_header("Location: $url");
    }
	public function delete_ad_code($type = 'uploads')
    {
		$sku = $this->input->post('sku');
		$file_name = $this->input->post('file_name');
		if (empty ($file_name) OR empty ($sku))
        {
            return;
        }
		
		$file_folder = $this->_get_upload_path($sku, 'uploads');
        
        
        
        
        if (unlink($file_folder.'/'.$file_name))
        {
            echo $this->create_json(1, lang('ok'));
        }
        else
        {
            echo $this->create_json(0, $file_folder.'/'.$file_name.lang('error_msg'));
        }        
    }
	//add by mallerp end
	public function import_product() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'pi/import_product', $data);
        $this->template->render();
    }
	function do_import_product_upload()
    {
		setlocale(LC_ALL, 'en_US.UTF-8');
		
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100000';
        $config['max_width']  = '1024';
        $config['max_height']  = '7680';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('pi/import_product', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);
            $output_data = array();
			$i=0;
            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1 or $row[0]==''){continue;}
				//
				//$purchaser_id=$this->user_model->fetch_user_id_by_name(trim($row[34]));
				//print_r($row);
				//echo $row[34].$purchaser_id;
				//$product_develper_id=$this->user_model->fetch_user_id_by_name(trim($row[35]));
				//$product_adjustment_id=$this->user_model->fetch_user_id_by_name(trim($row[36]));
				$purchaser_id=$this->user_model->fetch_user_id_by_name(trim($row[19]));
				$product_develper_id=$this->user_model->fetch_user_id_by_name(trim($row[18]));
				$catalog=$this->product_catalog_model->fetch_catalog_id_by_name_cn(trim($row[12]));
				$catalog_id = isset($catalog->id) ? $catalog->id : '1';
				$data=array(
							'name_en'=>$row[1],
							'name_cn'=>trim($row[2]),
							'market_model'=>trim($row[3]),
							'price'=>$row[5],
							'pure_weight'=>$row[6],
							'fill_material_heavy'=>$row[7],
							'stock_count'=>$row[8],
							'description_cn'=>$row[9],
							'description'=>$row[10],
							'short_description'=>$row[11],
							'stock_code'=>$row[13],
							'shelf_code'=>$row[14],
							'bulky_cargo'=>$row[15],
							'min_stock_number'=>$row[16],
							'sale_price'=>$row[17],
							'image_url'=>$row[20],
							'buy_url'=>$row[21],
							'total_weight'=>(int)$row[6]+(int)$row[7],
							'catalog_id'=>$catalog_id,
							'purchaser_id'=>$purchaser_id,
							'product_develper_id'=>$product_develper_id,
							
							);
				//print_r($data);
				//die();
				if ($this->product_model->check_exists('product_basic', array('sku' => $row[0])))
				{
					//var_dump($data);
					$this->product_model->update_product($row[0],$data);
					$output_data[$row[0]]='update';
				}else{
					$data['sku']=$row[0];
					//var_dump($data);
					$product_id = $this->product_model->insert_product_base($data);
					$output_data[$row[0]]='insert';
				}
				$provider_names=explode(',',$row[4]);
				if(count($provider_names)>0)
				{
					foreach($provider_names as $provider_name)
					{
						$provider_id = $this->purchase_model->fetch_provider_id_by_name($provider_name);
						$product_id = $this->product_model->fetch_product_id($row[0]);
						if ($this->purchase_model->check_exists('provider_product_map', array('provider_id' => $provider_id, 'product_id' => $product_id)))
						{
						}else{
							$data = array(
							'provider_id'    => $provider_id,
							'product_id'     => $product_id,
							'price1to9'      => '0',
							'price10to99'    => '0',
							'price100to999'  => '0',
							'price1000'      => '0',
							'provide_level'  => '127',
							);
							$this->purchase_model->add_provider_sku($data);
						}
					
					}
				}


            }
            $data_page = array(
                'data' => $output_data,
            );
            $this->template->write_view('content', 'pi/import_product_success', $data_page);
            $this->template->render();
        }
    }
	public function set_product_purchaser()
	{
		$all_users = $this->user_model->fetch_all_users();
		$data = array(
					  'all_users'=>$all_users,
					  );
		
		$this->template->write_view('content', 'pi/set_product_purchaser', $data);
        $this->template->render();
	}
	public function save_set_product_purchaser()
	{
		$skus = trim($this->input->post('sku'));
		$purchaser_id = trim($this->input->post('purchaser_id'));
		$product_develper_id = trim($this->input->post('product_develper_id'));
		$skus=explode(',', $skus);
		foreach($skus as $sku)
		{
			$data=array();
			if($purchaser_id>0){$data['purchaser_id']=$purchaser_id;}
			if($product_develper_id>0){$data['product_develper_id']=$product_develper_id;}
			if(!empty($data))
			{
				$this->product_model->update_product_by_sku($sku, $data);
			}
			
		}
		echo $this->create_json(1, lang('configuration_accepted'));
	}
	public function import_product_stock() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'pi/import_product_stock', $data);
        $this->template->render();
    }
	public function do_import_product_stock_upload()
	{
		setlocale(LC_ALL, 'en_US.UTF-8');
		
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100000';
        $config['max_width']  = '1024';
        $config['max_height']  = '7680';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('pi/import_product_stock', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);
            $output_data = array();
			$i=0;
            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1){continue;}
				$data=array('shelf_code'=>$row[1]);
				//print_r($data);
				//die();
				$this->product_model->update_product($row[0],$data);
				$output_data[$row[0]]=$row[1];
            }
            $data_page = array(
                'data' => $output_data,
            );
            $this->template->write_view('content', 'pi/import_product_success', $data_page);
            $this->template->render();
        }
	}
	
	
}

?>
