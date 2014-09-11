<?php

$CI = & get_instance();
require_once APPPATH . 'controllers/edu/edu' . EXT;

class Content extends Edu {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('group_model');
        $this->load->model('document_content_model');
        $this->load->model('document_catalog_model');
    }

    public function add() {
        $this->template->add_js('static/js/ajax/document.js');

        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_css('static/css/accordion.css');

        $group_all = $this->group_model->fetch_all_groups();
        $parent_catalog = $this->document_catalog_model->fetch_all_document_catalog_for_make_tree();
        $parent_catalog = $this->_make_tree($parent_catalog);

        $data = array(
            'parent' => $parent_catalog,
            'group_all' => $group_all,
        );

        $this->template->write_view('content', 'edu/content/add', $data);
        $this->template->render();
    }

    public function save() 
    {
        $title = trim($this->input->post('title'));
        $document_content = trim($this->input->post('document_content'));
        $parent = trim($this->input->post('parent'));

        if( ! $title)
        {
            echo $this->create_json(0, lang('title_required'));
            return;
        }
        
        if( ! $document_content)
        {
            echo $this->create_json(0, lang('document_content_required'));
            return;
        }
        if( ! $parent)
        {
            echo $this->create_json(0, lang('parent_required'));
            return;
        }

        $content_id = $this->input->post('content_id');

        $user = array();
        $user = $this->account->get_account();

        $data = array(
            'title' => trim($this->input->post('title')),
            'custom_date' => trim($this->input->post('custom_date')),
            'content' => $this->input->post('document_content'),
            'catalog_id' => $this->input->post('parent'),
            'level' => $this->input->post('level'),
            'owner_id' => $user['id'],
        );

        try {
            if (!$content_id) {
                if ($this->document_content_model->check_exists('document_content', array('title' => trim($this->input->post('title'))))) {
                    echo $this->create_json(0, lang('document_title_exists'));
                    return;
                } else {
                    $insert_id = $this->document_content_model->insert_content($data);
                }
            } else {
                $data = array();

                if (trim($this->input->post('title')) !== $this->document_content_model->get_one('document_content', 'title', array('id' => $content_id))) {
                    if ($this->document_content_model->check_exists('document_content', array('title' => $this->input->post('title')))) {
                        echo $this->create_json(0, lang('document_title_exists'));
                        return;
                    } else {
                        $data['title'] = trim($this->input->post('title'));
                    }
                }

                $data['catalog_id'] = trim($this->input->post('parent'));

                $data['custom_date'] = trim($this->input->post('custom_date'));

                $data['content'] = $this->input->post('document_content');
                
                $data['level'] = $this->input->post('level');
                
                $this->document_content_model->update_content($content_id, $data);
                

                echo $this->create_json(1, lang('configuration_accepted'));
            }
        } catch (Exception $ex) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function manage() {
        $this->enable_search('document_content');
        $this->enable_sort('document_content');
        $cat_id = $this->input->post('id');

        if ($this->input->is_post()) {
            if ($cat_id) {
                $this->session->set_userdata('current_document_catalog_id', $cat_id);
            }
            unset($_POST['id']);
            unset($_POST['action']);
            $contents = $this->document_content_model->fetch_all_document_content();
            foreach ($contents as $content) {
                $content->dcata_path = $this->path_to_name($content->dcata_path);
            }
            $data = array(
                'contents' => $contents,
                'action' => 'edit',
                'cat_name' => $this->document_catalog_model->fetch_catalog_name($cat_id),
            );
            $this->load->view('edu/content/management', $data);
            return;
        }
        $this->session->set_userdata('current_document_catalog_id', -1);
        $contents = $this->document_content_model->fetch_all_document_content();
        $data = array(
            'content_url' => site_url('edu/content/manage'),
            'id_rename' => 'dc_DOT_catalog_id',
            'child_tree_url' => site_url('edu/catalog/fetch_child_catalogs_edit_tree'),
            'cat_name' => $this->document_catalog_model->fetch_catalog_name($cat_id),
        );
        $this->render_list('edu/content/management', 'edit', $data);
    }

    public function manage_by_catalog_id($id) {
        $this->enable_search('document_content');
        $contents = $this->document_content_model->fetch_document_content_by_catalog_id($id);
        foreach ($contents as $content) {
            $content->dcata_path = $this->path_to_name($content->dcata_path);
        }

        $data = array(
            'contents' => $contents,
            'action' => 'edit',
        );

        $this->template->write_view('content', 'edu/content/management', $data);
        $this->template->render();
    }

    public function view_list_by_catalog_id($id) {
        $this->enable_search('document_content');
        $contents = $this->document_content_model->fetch_document_content_by_catalog_id($id);
        foreach ($contents as $content) {
            $content->dcata_path = $this->path_to_name($content->dcata_path);
        }
        $data = array(
            'contents' => $contents,
            'action' => 'view',
        );

        $this->template->write_view('content', 'edu/content/management', $data);
        $this->template->render();
    }

    public function view_list() {
        $this->enable_search('document_content');
        $this->enable_sort('document_content');

        $cat_id = $this->input->post('id');
        if ($this->input->is_post()) {
            if ($cat_id) {
                $this->session->set_userdata('current_document_catalog_id', $cat_id);
            }
            unset($_POST['id']);
            unset($_POST['action']);
            $content = "";
            $content->dcata_path = "";
            $contents = $this->document_content_model->fetch_all_document_content();
            foreach ($contents as $content) {
                $content->dcata_path = $this->path_to_name($content->dcata_path);
            }
            $data = array(
                'contents' => $contents,
                'action' => 'view',
                'path_name' => $content->dcata_path,
            );
            $this->load->view('edu/content/management', $data);
            return;
        }
        $this->session->set_userdata('current_document_catalog_id', -1);
        $contents = $this->document_content_model->fetch_all_document_content();
        foreach ($contents as $content) {
            $content->dcata_path = $this->path_to_name($content->dcata_path);
        }
        $data = array(
            'content_url' => site_url('edu/content/view_list'),
            'child_tree_url' => site_url('edu/catalog/fetch_child_catalogs_view_tree'),
            'path_name' => isset($content->dcata_path)?$content->dcata_path:'',
        );
        $this->render_list('edu/content/management', 'view', $data);
    }

    public function view($id) {
        $document_content = $this->document_content_model->fetch_document_content($id);
        $document_content->dcata_path = $this->path_to_name($document_content->dcata_path);

        $comments = $this->document_content_model->fetch_document_comment($id);

        $files = $this->document_content_model->fetch_document_files($id);

        $tag = false;
        if (get_current_user_id () === $document_content->u_id) {
            $tag = true;
        }

        $data = array(
            'document_content' => $document_content,
            'comments' => $comments,
            'tag' => $tag,
            'files' => $files,
        );
        $this->load->view('edu/content/view', $data);
    }

    public function edit($id) {

        $this->template->add_js('static/js/ajax/document.js');
        $this->template->add_js('static/js/accordion/accordion.js');
        $this->template->add_css('static/css/accordion.css');

        if ($id) {
            if ($this->is_super_user()) {
                $parent_catalog = $this->document_catalog_model->fetch_all_document_catalog_for_make_tree();
                $parent_catalog = $this->_make_tree($parent_catalog);
                $document_content = $this->document_content_model->fetch_document_content($id);
                $files = $this->document_content_model->fetch_document_files($id);
            } else {

                $owner_id = $this->document_content_model->fetch_owner_id($id);
                
                if ($owner_id == $this->get_current_user_id()) {
                    $parent_catalog = $this->document_catalog_model->fetch_all_document_catalog_for_make_tree();
                    $parent_catalog = $this->_make_tree($parent_catalog);

                    $document_content = $this->document_content_model->fetch_document_content($id);

                    $files = $this->document_content_model->fetch_document_files($id);
                } else {
                    echo lang('error_notice');
                    return;
                }
            }
        } else {
            echo lang('error_notice');
            return;
        }


        $data = array(
            'parent' => $parent_catalog,
            'document_content' => $document_content,
            'action' => 'edit',
            'content_id' => $id,
            'files' => $files,
        );
        $this->template->write_view('content', 'edu/content/view_edit', $data);
        $this->template->render();
    }

    public function drop_content() {
        try {
            $id = $this->input->post('id');
            $this->document_content_model->drop_content($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    private function render_list($url, $action, $data) {
        $level = 1;
        $parent_id = -1;
        $cats = $this->document_catalog_model->fetch_child_catalogs_tree($parent_id, $level);
        $data['cats'] = $cats;
        $this->set_2column_tree($data);
        $contents = $this->document_content_model->fetch_all_document_content();
        foreach ($contents as $content) {
            $content->dcata_path = $this->path_to_name($content->dcata_path);
        }
        $data = array(
            'contents' => $contents,
            'action' => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->add_js('static/js/ajax/document.js');
        $this->template->add_js('static/js/tiny_mce/tiny_mce.js');
        $this->template->add_css('static/css/accordion.css');
        $this->template->render();
    }

    private function _make_tree($parent_catalogs) {
        $tree = array();
        $names = array();
        foreach ($parent_catalogs as $cat) {
            $path = $cat->path;
            $names[$cat->id] = $cat->name;
            $items = explode('>', $path);
            $item_names = array();
            $space_counter = 0;

            foreach ($items as $item) {
                $tree[$item] = repeater('&nbsp;&nbsp;', $space_counter) . element($item, $names);
                $space_counter++;
            }
            //flat_to_multi($item_names, $tree);
        }

        return $tree;
    }

    public function path_to_name($path) {
        $name = array();
        $row = explode('>', $path);
        for ($i = 0; $i < count($row); $i++) {
            $name[] = $this->fetch_catalog_name($row[$i]);
        }
        $path_name = implode('>', $name);

        return $path_name;
    }

    public function fetch_catalog_name($id = NULL) {
        $catalog = $this->document_catalog_model->fetch_document_catalog($id);
        return $catalog->name;
    }

    public function drop_comment($comment_id, $content_id) {
        try {
            $this->document_content_model->drop_comment($comment_id);

            $comments = $this->document_content_model->fetch_document_comment($content_id);
            $content = $this->document_content_model->fetch_document_content($content_id);


            $tag = false;
            if (get_current_user_id () === $content->u_id) {
                $tag = true;
            }

            $data_current = array(
                'comments' => $comments,
                'tag' => $tag,
                'content_id' => $content_id,
            );

            $this->load->view('edu/content/current_comment', $data_current);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function comment_save() {
        $rules = array(
            array(
                'field' => 'document_comment',
                'label' => lang('document_comment'),
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $content_id = $this->input->post('content_id');

        $data = array(
            'content_id' => $content_id,
            'comment' => $this->input->post('document_comment'),
            'creator' => get_current_user_id(),
        );

        try {
            $insert_id = $this->document_content_model->insert_comment($data);

            $comments = $this->document_content_model->fetch_document_comment($content_id);

            $content = $this->document_content_model->fetch_document_content($content_id);

            $tag = false;
            if (get_current_user_id () === $content->u_id) {
                $tag = true;
            }

            $data_current = array(
                'comments' => $comments,
                'tag' => $tag,
                'content_id' => $content_id,
            );

            $this->load->view('edu/content/current_comment', $data_current);
        } catch (Exception $ex) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function upload_file($type = 'uploads') {
        $file_folder = 'uploads';
        if (isset($key)) {
            $file_folder = $type;
        }
        $this->load->library('upload');
        $content_id = $this->input->post('content_id');
        $description = $this->input->post('description');
        $desc_arr = explode(';', $description);

        if (empty($content_id)) {
            return;
        }

        $error = 0;
        $upload_files = $_FILES['upload_file'];

        if (count($desc_arr) !== count($upload_files['name'])) {
            $url = site_url('edu/content/edit/', array($content_id));

            echo "<script >alert('" . lang('description_count_no_the_same') . "');window.location.href='" . $url . "';</script>";

            return;
        }

        for ($i = 0; $i < count($upload_files['name']); $i++) {

            $_FILES['userfile']['name'] = $upload_files['name'][$i];
            $_FILES['userfile']['type'] = $upload_files['type'][$i];
            $_FILES['userfile']['tmp_name'] = $upload_files['tmp_name'][$i];
            $_FILES['userfile']['error'] = $upload_files['error'][$i];
            $_FILES['userfile']['size'] = $upload_files['size'][$i];

            $file_name = $this->_create_file_name($content_id, $content_id, $file_folder);
            $config['file_name'] = $file_name;

            $file_url = $this->_get_upload_path($content_id, $file_folder);

            $config['upload_path'] = $file_url;
            $config['allowed_types'] = '*';

            $this->upload->initialize($config);

            if ($this->upload->do_upload()) {
                $error += 0;
            } else {
                $error += 1;
            }


            $file_data = $this->upload->data();

            $url = $file_url . $file_name . $file_data['file_ext'];
            $url = substr($url, 2);

            $data = array(
                'content_id' => $content_id,
                'file_url' => $url,
                'file_description' => $desc_arr[$i],
                'before_file_name' => $upload_files['name'][$i],
            );
            $insert_id = $this->document_content_model->insert_content_file($data);
        }
        if ($error > 0) {
            echo $this->upload->display_errors('<p>', '</p>');
            exit;
        }

        $url = site_url('edu/content/edit/', array($content_id));
        $this->output->set_header("Location: $url");
    }

    private function _get_upload_path($content_id, $file_folder = 'uploads') {
        $dir = "./$file_folder/" . $content_id . '/';

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, TRUE);
        }

        return $dir;
    }

    private function _create_file_name($content_id, $content_id, $file_folder = 'uploads') {
        $dir = $this->_get_upload_path($content_id, $file_folder);
        $map = directory_map($dir, 1);

        foreach ($map as $key => $value) {
            $extension = strrchr($value, ".");
            $map[$key] = substr($value, 0, -strlen($extension));
        }

        $start = 0;
        $name = '';
        // try to get a file name.
        while (1) {
            $start++;
            $name = $content_id . '-' . $start . '-' . substr(md5($content_id), 4, 4);
            if (!in_array($name, $map)) {
                break;
            }
        }

        return $name;
    }

    public function drop_file($file_id, $content_id) {
        try {
            $file_url = $this->document_content_model->get_one('document_content_file_map', 'file_url', array('id' => $file_id));

            $this->document_content_model->drop_file($file_id);

            unlink('./' . $file_url);

            $files = $this->document_content_model->fetch_document_files($content_id);

            $data_current = array(
                'files' => $files,
                'content_id' => $content_id,
            );

            $this->load->view('edu/content/current_file', $data_current);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function down_file($id) {
        $file_obj = $this->document_content_model->fetch_document_file($id);

        $link = base_url() . $file_obj->file_url;

        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_obj->before_file_name . '"');

        readfile("$link");
        exit();
    }

}

?>
