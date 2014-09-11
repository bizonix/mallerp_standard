<?php
require_once APPPATH.'controllers/edu/edu'.EXT;

class Catalog extends Edu
{
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('group_model');
        $this->load->model('document_catalog_model');
    }
    
    public function add($id = NULL)
    {
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_css('static/css/accordion.css');

        $group_all = $this->group_model->fetch_all_groups();

        $parent_catalog = $this->document_catalog_model->fetch_all_document_catalog_for_make_tree();
        $parent_catalog = $this->_make_tree($parent_catalog);

        $data = array(
            'parent'              => $parent_catalog,
            'group_all'           => $group_all,
        );

        $this->template->write_view('content', 'edu/add', $data);
        $this->template->render();
    }

    public function save()
    {
        $rules = array(
            array(
                'field' => 'name',
                'label' => lang('chinese_name'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'parent',
                'label' => lang('parent'),
                'rules' => 'trim',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        $name = trim($this->input->post('name'));
        $parent = $this->input->post('parent');
        $permission_groups = $this->input->post('group');

        if($parent === '' OR $parent === NULL OR !$parent)
        {
            $parent = -1;
        }

        $catalog_id = $this->input->post('catalog_id');

        $data = array(
          'name'                     => $name,
          'parent'                   => $parent,
        );

        try
        {
            if($catalog_id < 0)
            {
                if ($this->document_catalog_model->check_exists('document_catalog', array('name' => $name)))
                {
                    echo $this->create_json(0, lang('document_catalog_exists'));
                    return;
                }
                else
                {
                    $insert_id = $this->document_catalog_model->insert_catalog($data);
                    if($parent != -1)
                    {
                        $document_catalog = $this->document_catalog_model->fetch_document_catalog($parent);
                        $parent_path = $document_catalog->path;
                        $path = $parent_path.'>'.$insert_id;
                    }
                    else
                    {
                        $path = $insert_id;
                    }
                    $data = array(
                        'path'      => $path,
                    );
                    $this->document_catalog_model->update_document_catalog($insert_id,$data);

                    $group_ids = $this->input->post('group');

                    if (empty($group_ids))
                    {
                        $group_ids = array();
                    }
                    $this->document_catalog_model->save_catalog_groups($insert_id, $group_ids);

                    echo $this->create_json(1, lang('ok'));
                }
            }
            else
            {
                $data = array();
                
                if (trim($this->input->post('name')) !== $this->document_catalog_model->get_one('document_catalog', 'name', array('id' => $this->input->post('catalog_id'))))
                {
                    if ($this->document_catalog_model->check_exists('document_catalog', array('name' => $this->input->post('name'))))
                    {
                        echo $this->create_json(0, lang('document_catalog_exists'));
                        return;
                    }
                    else
                    {
                        $data['name'] = trim($this->input->post('name'));
                    }
                }
                
                if ($parent !== $this->document_catalog_model->get_one('document_catalog', 'parent', array('id' => $catalog_id)) && $catalog_id != $parent)
                {
                    $child_catalog_ids = array();
                    $child_catalogs = $this->document_catalog_model->fetch_child_catalogs($catalog_id);
                    foreach ($child_catalogs as $child)
                    {
                        $child_catalog_ids[] = $child->id;
                    }

                    if( ! in_array($parent, $child_catalog_ids))
                    {
                        $data['parent'] = $parent;

                        $path = $this->document_catalog_model->get_one('document_catalog', 'path', array('id' => $parent)).'>'.$catalog_id;

                        $data['path'] = $path;

                        $this->update_child_catalog($path, $catalog_id);
                    }
                    else
                    {
                        echo $this->create_json(0, lang('operation_not_allowed'));
                        return;
                    }
                }

                if($data)
                {
                    $this->document_catalog_model->update_catalog($catalog_id, $data);
                }

                $group_ids = $this->input->post('group');

                if (empty($group_ids))
                {
                    $group_ids = array();
                }
                $this->document_catalog_model->save_catalog_groups($catalog_id, $group_ids);

                echo $this->create_json(1, lang('configuration_accepted'));
            }
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }



    public function manage()
    {
        $this->enable_search('document_catalog');
        $this->render_list('edu/management', 'edit');
    }

    public function edit($id)
    {
        //Load js and css.
        $this->template->add_js('static/js/ajax/document.js');
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_css('static/css/accordion.css');

        //Load group.
        $group_all = $this->group_model->fetch_all_groups();

        //Load possession group by catalog id .
        $group_possession = $this->document_catalog_model->fetch_group_ids($id);

        //Load catalog.
        $parent_catalog = $this->document_catalog_model->fetch_all_document_catalog_for_make_tree();
        $parent_catalog = $this->_make_tree($parent_catalog);

        //Load document catalog by id .
        $document_catalog = $this->document_catalog_model->fetch_document_catalog($id);

        $catalog = $this->document_catalog_model->fetch_document_catalog($id);

        $data = array(
            'catalog'                           => $catalog,
            'parent'                            => $parent_catalog,
            'document_catalog'                  => $document_catalog,
            'group_possession'                  => $group_possession,
            'group_all'                         => $group_all,
            'action'                            => 'edit',
        );
        $this->template->write_view('content', 'edu/view_edit', $data);
        $this->template->render();
    }


    public function  drop_catalog($id = NULL)
    {
        $catalog_id = $this->input->post('id');
        $parent = array();
        $document_catalogs = $this->document_catalog_model->fetch_all_document_catalog();
        foreach($document_catalogs as $document_catalog)
        {
            $parent[] = $document_catalog->parent;
        }
        if(in_array($catalog_id, $parent) == FALSE)
        {
            $this->document_catalog_model->drop_catalog($catalog_id);
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

        $cats = $this->document_catalog_model->fetch_child_catalogs_tree($catalog_id, $level);
        $data = array(
            'cats'           => $cats,
            'content_url'    => $content_url,
            'child_tree_url' => $child_tree_url,
        );
        $this->load->view('default/create_tree', $data);
    }

    public function fetch_child_catalogs_edit_tree()
    {
        return $this->fetch_child_catalogs_tree(site_url('edu/content/manage'), site_url('edu/catalog/fetch_child_catalogs_edit_tree'));
    }

    public function fetch_child_catalogs_view_tree()
    {
        return $this->fetch_child_catalogs_tree(site_url('edu/content/view_list'), site_url('edu/catalog/fetch_child_catalogs_view_tree'));
    }


    private function render_list($url, $action)
    {
        $catalogs = $this->document_catalog_model->fetch_all_document_catalog();
        foreach ($catalogs as $catalog)
        {
            $catalog->path = $this->path_to_name($catalog->path);
        }

        $data = array(
            'catalogs'    => $catalogs,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    private function _make_tree($parent_catalogs)
    {
        $tree = array("-1" => lang('please_select'));
        $names = array();
        foreach ($parent_catalogs as $cat)
        {
            $path = $cat->path;
            $names[$cat->id] = $cat->name;
            $items = explode('>', $path);
            $item_names = array();
            $space_counter = 0;

            foreach ($items as $item)
            {
                $tree[$item] = repeater('&nbsp;&nbsp;', $space_counter) . element($item, $names);
                $space_counter++;
            }
        }

        return $tree;
    }

    public function path_to_name($path)
    {
        $name = array();
        $row = explode('>', $path);
        for($i=0;$i<count($row);$i++)
        {
            $name[] = $this->fetch_catalog_name($row[$i]);
        }
        $path_name = implode('>', $name);

        return $path_name;
    }

    public function fetch_catalog_name($id = NULL)
    {
        $catalog = $this->document_catalog_model->fetch_document_catalog($id);

        return $catalog->name;

    }

    private function update_child_catalog($parent_path, $id)
    {
        if($id)
        {
            $catalogs = $this->document_catalog_model->fetch_child_catalogs($id);
            foreach ($catalogs as $catalog)
            {
                $data['path'] = $parent_path .'>'.  $catalog->id;
                $this->document_catalog_model->update_catalog($catalog->id, $data);
                if($this->document_catalog_model->fetch_child_catalogs($catalog->id))
                {
                    $path = $parent_path . '>' . $catalog->id;
                    $this->update_child_catalog($path, $catalog->id);
                }
            }
        }
    }
}

?>
