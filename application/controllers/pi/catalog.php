<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Catalog extends Pi
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('product_catalog_model');
        $this->load->model('product_packing_model');
        $this->load->library('form_validation');
    }

    public function add($id = NULL)
    {
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_css('static/css/accordion.css');
        $parent_catalog = $this->product_catalog_model->fetch_all_product_catalog();
        $parent_catalog = $this->_make_tree($parent_catalog);
        $product_packing = $this->product_packing_model->fetch_all_product_packing();
        $all_purchase_users = $this->user_model->fetch_users_by_system_code('purchase');
        $all_sale_users = $this->user_model->fetch_users_by_system_code('sale');
        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');
        $all_qt_users = $this->user_model->fetch_users_by_system_code('qt');
        $all_seo_users = $this->user_model->fetch_users_by_system_code('seo');
        $str = '';
        $data = array(
            'parent'              => $parent_catalog,
            'product_packing'     => $product_packing,
            'all_purchase_users'  => $all_purchase_users,
            'all_sale_users'      => $all_sale_users,
            'all_stock_users'     => $all_stock_users,
            'all_qt_users'        => $all_qt_users,
            'all_seo_users'       => $all_seo_users,
            'parent_catalog'      => '',
            'parent_catalog_tester' => '',
            'parent_catalog_seoer' => '',
        );

        $this->template->write_view('content', 'pi/add_edit_catalog', $data);
        $this->template->render();
    }

    public function edit($id = NULL)
    {
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_js('static/js/ajax/product.js');
        $this->template->add_css('static/css/accordion.css');
        $parent_catalog = $this->product_catalog_model->fetch_all_product_catalog();
        $product_packing = $this->product_packing_model->fetch_all_product_packing();
        $all_purchase_users = $this->user_model->fetch_users_by_system_code('purchase');
        $all_sale_users = $this->user_model->fetch_users_by_system_code('sale');
        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');
        $all_qt_users = $this->user_model->fetch_users_by_system_code('qt');
        $all_seo_users = $this->user_model->fetch_users_by_system_code('seo');
        
        
        $purchase_user_permissions = $this->product_catalog_model->fetch_catalog_permissions($id);
        $parent_catalog = $this->_make_tree($parent_catalog);
        $catalog_name_cn = array();
        $catalog_name_en = array();
        $parent = array();
        $product_catalog = NULL;
        $str = '';
        $product_catalog = $this->product_catalog_model->fetch_product_catalog($id);

        $product_parent_catalog_user = '';
        $parent_catalog_tester = '';
        $parent_catalog_seoer = '';
        if($product_catalog->parent && $product_catalog->parent != '-1')
        {
            $product_parent_catalog = $this->product_catalog_model->fetch_product_catalog($product_catalog->parent);
            $product_parent_catalog_user = $product_parent_catalog->stock_user_id;
            $parent_catalog_tester = $product_parent_catalog->tester_id;
            $parent_catalog_seoer = $product_parent_catalog->seo_user_id;
        }
        


        $data = array(
            'product_catalog'               => $product_catalog,
            'parent'                        => $parent_catalog,
            'product_packing'               => $product_packing,
            'all_purchase_users'            => $all_purchase_users,
            'all_sale_users'                => $all_sale_users,
            'purchase_user_permissions'     => $purchase_user_permissions,
            'all_stock_users'               => $all_stock_users,
            'all_qt_users'                  => $all_qt_users,
            'parent_catalog'                => $product_parent_catalog_user,
            'parent_catalog_tester'         => $parent_catalog_tester,
            'parent_catalog_seoer'         => $parent_catalog_seoer,
            'all_seo_users'                 => $all_seo_users,
        );

        $this->template->write_view('content', 'pi/add_edit_catalog', $data);
        $this->template->render();
    }

    public function save_catalog()
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
                'field' => 'parent',
                'label' => lang('parent'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'lowest_profit',
                'label' => lang('lowest_profit'),
                'rules' => 'trim|required|positive_numeric',
            ),        
            array(
                'field' => 'packing_difficulty_factor',
                'label' => lang('packing_difficulty_factor'),
                'rules' => 'trim|required|positive_numeric|between_0_and_1',
            ),          
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $name_cn = trim($this->input->post('name_cn'));
        $name_en = trim($this->input->post('name_en'));
        $parent = $this->input->post('parent');
        $packing_material = $this->input->post('packing_material');
        $lowest_profit = price(trim($this->input->post('lowest_profit')));
        $packing_difficulty_factor = trim($this->input->post('packing_difficulty_factor'));
        $third_platform = $this->input->post('third_platform');
        $purchase_user = $this->input->post('purchase_user');
        $stocker = $this->input->post('stocker');
        $tester = $this->input->post('tester');
        $seo_user_id = $this->input->post('seo_user_id');
        $saler_permissions = $this->input->post('saler_permissions');
        $catalog_id = $this->input->post('catalog_id');

        $old_catalog = $this->product_catalog_model->fetch_product_catalog($catalog_id);
        if ( ! empty($old_catalog)
            && $old_catalog->parent != $parent 
            && $this->product_catalog_model->is_parent_catalog($catalog_id))
        {
             echo $this->create_json(0, lang('operation_not_allowed_catalog_in_use'));
             return;
        }
        if (empty($saler_permissions))
        {
            echo $this->create_json(0, lang('saler_permission_is_required'));
            return;
        }
        if($parent === '' OR $parent === NULL)
        {
            $parent = -1;
        }
        $data = array(
          'name_cn'                     => $name_cn,
          'name_en'                     => $name_en,
          'parent'                      => $parent,
          'packing_material '           => $packing_material,
          'lowest_profit'               => $lowest_profit,
          'packing_difficulty_factor'   => $packing_difficulty_factor,
          'third_platform'              => $third_platform,
          'purchaser_id'                => $purchase_user,
          'stock_user_id'               => $stocker,
          'tester_id'                   => $tester,
          'seo_user_id'                 => $seo_user_id,
        );
        try
        {
            if($catalog_id < 0)
            {
                // check if product catalog exists ?
                if ($this->product_catalog_model->check_exists('product_catalog', array('name_cn' => $name_cn,'parent'=>$parent)))
                {
                    echo $this->create_json(0, lang('product_catalog_exists'));
                    return;
                }
                else
                {
                    $insert_id = $this->product_catalog_model->add_a_catalog($data);
                    if($parent && $parent != -1)
                    {
                        $product_catalog = $this->product_catalog_model->fetch_product_catalog($parent);
                        $parent_path = $product_catalog->path;
                        $path = $parent_path.'>'.$insert_id;
                    }
                    else
                    {
                        $path = $insert_id;
                    }
                    $data = array(
                        'path'      => $path,
                    );
                    $this->product_catalog_model->update_product_catalog($insert_id,$data);
                    echo $this->create_json(1, lang('ok'));
                }
            }
            else
            {
                $parent_row = array();
                $product_catalogs = $this->product_catalog_model->fetch_all_product_catalog();
                foreach($product_catalogs as $product_catalog)
                {
                    $parent_row[] = $product_catalog->parent;
                }
                
                $catalog_obj = $this->product_catalog_model->fetch_product_catalog($catalog_id);
                
                if(in_array($catalog_id, $parent_row) == FALSE && $catalog_id != $parent && $parent!=-1)
                {
                    $catalog = $this->product_catalog_model->fetch_product_catalog($parent);
                    $parent_path = $catalog->path;
                    $path = $parent_path.'>'.$catalog_id;
                    $data = array(
                          'name_cn'                     => $name_cn,
                          'name_en'                     => $name_en,
                          'parent'                      => $parent,
                          'path'                        => $path,
                          'packing_material '           => $packing_material,
                          'lowest_profit'               => $lowest_profit,
                          'packing_difficulty_factor'   => $packing_difficulty_factor,
                          'third_platform'              => $third_platform,
                          'purchaser_id'                => $purchase_user,
                          'stock_user_id'               => $stocker,
                          'tester_id'                   => $tester,
                          'seo_user_id'                 => $seo_user_id,
                    );
                    $this->product_catalog_model->update_catalog($catalog_id,$data);
                    
                    if($catalog_obj->tester_id != $tester)
                    {
                        $this->subordinate_catalogs(array($catalog_id), $tester, 'tester_id');
                    }
                    
                    if($catalog_obj->seo_user_id != $seo_user_id)
                    {
                        $this->subordinate_catalogs(array($catalog_id), $seo_user_id, 'seo_user_id');
                    }
                }
                else
                {                   
                     if ( $catalog_id == $parent)
                     {
                         $data = array(
                              'name_cn'                     => $name_cn,
                              'name_en'                     => $name_en,
                              'packing_material '           => $packing_material,
                              'lowest_profit'               => $lowest_profit,
                              'packing_difficulty_factor'   => $packing_difficulty_factor,
                              'third_platform'              => $third_platform,
                              'purchaser_id'                => $purchase_user,
                              'stock_user_id'               => $stocker,
                              'tester_id'                   => $tester,
                              'seo_user_id'                 => $seo_user_id,
                         );
                     }
                     $this->product_catalog_model->update_catalog($catalog_id,$data);
                     
                     if($catalog_obj->tester_id != $tester)
                     {
                         $this->subordinate_catalogs(array($catalog_id), $tester, 'tester_id');
                     }
                     
                     if($catalog_obj->seo_user_id != $seo_user_id)
                     {
                         $this->subordinate_catalogs(array($catalog_id), $seo_user_id, 'seo_user_id');
                     }
                }

                // save saler permissions
                $this->product_catalog_model->save_saler_permissions($catalog_id, $saler_permissions);
                echo $this->create_json(1, lang('configuration_accepted'));
            }
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

    }

    public function fetch_catalog_name_cn($id = NULL)
    {
        $catalog = $this->product_catalog_model->fetch_catalog_name($id);
        
        return isset($catalog->name_cn)?$catalog->name_cn:'';

    }
    
    public function fetch_catalog_name_en($id = NULL)
    {
        $catalog = $this->product_catalog_model->fetch_catalog_name($id);
        
        return isset($catalog->name_en)?$catalog->name_en:'';

    }

    public function manage()
    {
         $this->enable_search('product_catalog');
         $this->render_list('pi/catalog_management', 'edit');
    }

    public function  drop_catalog($id = NULL)
    {
        $catalog_id = $this->input->post('id');
        $parent = array();
        $product_catalogs = $this->product_catalog_model->fetch_all_product_catalog();
        foreach($product_catalogs as $product_catalogs)
        {
            $parent[] = $product_catalogs->parent;
        }
        if(in_array($catalog_id, $parent) == FALSE)
        {
            $this->product_catalog_model->drop_catalog($catalog_id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        else
        {
             echo $this->create_json(0, lang('operation_not_allowed'));
        }        
    }

    public function fetch_child_catalogs_tree($content_url, $child_tree_url)
    {
        $catalog_id = $this->input->post('id');
        $level = $this->input->post('level');
        $level++;
        
        $cats = $this->product_catalog_model->fetch_child_catalogs_tree($catalog_id, $level);
        $data = array(
            'cats'           => $cats,
            'content_url'    => $content_url,
            'child_tree_url' => $child_tree_url,
        );
        $this->load->view('default/create_tree', $data);
    }

    public function fetch_child_catalogs_edit_tree()
    {
        return $this->fetch_child_catalogs_tree(site_url('pi/product/manage'), site_url('pi/catalog/fetch_child_catalogs_edit_tree'));
    }

    public function fetch_child_catalogs_view_tree()
    {
        return $this->fetch_child_catalogs_tree(site_url('pi/product/view_list'), site_url('pi/catalog/fetch_child_catalogs_view_tree'));
    }

    private function render_list($url, $action)
    {
        $product_catalogs = $this->product_catalog_model->fetch_all_path();
        $path_cn = array();
        $path_en = array();
        foreach($product_catalogs as $catalogs)
        {
            $path_cn[] = $this->path_to_name_cn($catalogs->path);
            $path_en[] = $this->path_to_name_en($catalogs->path);
        }
        $data = array(
            'product_catalogs'  => $product_catalogs,
            'path_cn'           => $path_cn,
            'path_en'           => $path_en,
            'action'            => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    public function path_to_name_cn($path)
    {
        $name = array();
        $row = explode('>', $path);
        for($i=0;$i<count($row);$i++)
        {
            $name[] = $this->fetch_catalog_name_cn($row[$i]);
        }
        $path_name = implode('>', $name);

        return $path_name;
    }
    
    public function path_to_name_en($path)
    {
        $name = array();
        $row = explode('>', $path);
        for($i=0;$i<count($row);$i++)
        {
            $name[] = $this->fetch_catalog_name_en($row[$i]);
        }
        $path_name = implode('>', $name);

        return $path_name;
    }

    private function _make_tree($parent_catalogs)
    {
        $tree = array(-1 => lang('please_select'));
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
    
    private function subordinate_catalogs($child_ids, $tester, $field)
    {
        if($child_ids)
        {
            $temp_ids = array();
            foreach ($child_ids as $id)
            {
                $this->product_catalog_model->update_catalog($id, array($field =>$tester));
                
                $sub_ids = $this->product_catalog_model->fetch_child_catalog_ids($id);
                
                $temp_ids = array_merge($temp_ids, $sub_ids);
            }
            $this->subordinate_catalogs($temp_ids, $tester, $field);
        }
    }
	public function set_catalog_sale_permission()
	{
		$data = array();
		
		$this->template->write_view('content', 'pi/set_catalog_sale_permission', $data);
        $this->template->render();
	}
    public function save_set_catalog_sale_permission()
	{
		$catalog_id = trim($this->input->post('catalog_id'));
		$saler_id = trim($this->input->post('saler_id'));
		$action_id = trim($this->input->post('action_id'));
		if (empty($saler_id)||empty($catalog_id))
        {
            echo $this->create_json(0, lang('saler_permission_is_required'));
            return;
        }else
		{
			$catalog_id=explode(',', $catalog_id);
			$saler_id=explode(',', $saler_id);
		
			foreach($catalog_id as $id)
			{
				foreach($saler_id as $user_id)
				{
					if ($this->product_catalog_model->check_exists('product_catalog_sale_permission', array('product_catalog_id' => $id,'saler_id'=>$user_id)))
                	{
						if($action_id==0)//add
						{
						}
						if($action_id==1)//del
						{
							$this->product_catalog_model->drop_product_catalog_sale_permission($id,$user_id);
						}
                	}else{
						if($action_id==0)//add
						{
							$this->product_catalog_model->add_product_catalog_sale_permission(array('product_catalog_id' =>$id,'saler_id'=>$user_id));
						}
						if($action_id==1)//del
						{
						}
					}
				}
				
				//$this->product_catalog_model->save_saler_permissions($id, $saler_id);
			}
			echo $this->create_json(1, lang('configuration_accepted'));
		}
		
	}
}
?>
