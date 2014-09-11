<?php
require_once APPPATH.'controllers/seo/seo'.EXT;

class Seo_keyword extends Seo
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('seo_keyword_model');
        $this->load->model('seo_service_company_model');
        $this->load->library('form_validation');
    }
    
    public function add_edit($id = NULL)
    {
        $this->template->add_js('static/js/ajax/keyword.js');

        $catalogs = $this->seo_keyword_model->fetch_all_content_catalog();
        $seo_users = $this->user_model->fetch_all_seo_users();
        $keyword_companys = $this->seo_service_company_model->fetch_all_service_companys();
        $keyword = NULL;
        $catalog_ids = NULL;
        $permissions = NULL;
        $company_permissions = NULL;
        if ($id)
        {
            $keyword = $this->seo_keyword_model->fetch_keyword($id);
            $permissions = $this->seo_keyword_model->fetch_keyword_permissions($id);
            $catalog_ids = $this->seo_keyword_model->fetch_catalogs($id);
            $company_permissions = $this->seo_service_company_model->fetch_keyword_company_permissions($id);
        }

        $data = array(
            'catalogs'              => $catalogs,
            'catalog_ids'           => $catalog_ids,
            'keyword'               => $keyword,
            'seo_users'             => $seo_users,
            'permissions'           => $permissions,
            'keyword_companys'      => $keyword_companys,
            'company_permissions'   => $company_permissions,
        );
        $this->template->write_view('content', 'seo/seo_keyword/add_edit', $data);
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('seo_keyword');
        $this->enable_sort('seo_keyword');

        $this->render_list('seo/seo_keyword/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('seo_keyword');
        $this->enable_sort('seo_keyword');

        $this->render_list('seo/seo_keyword/management', 'view');
    }


    public function view($id)
    {
        $catalogs = $this->seo_keyword_model->fetch_all_content_catalog();
        $seo_users = $this->user_model->fetch_all_seo_users();

        $keyword = $this->seo_keyword_model->fetch_keyword($id);
        $permissions = $this->seo_keyword_model->fetch_keyword_permissions($id);
        $catalog_ids = $this->seo_keyword_model->fetch_catalogs($id);
        $data = array(
            'catalogs' => $catalogs,
            'catalog_ids' => $catalog_ids,
            'keyword'   => $keyword,
            'seo_users'  => $seo_users,
            'permissions'=> $permissions,
        );
        $this->template->write_view('content', 'seo/seo_keyword/view_detail', $data);
        $this->template->render();
    }


    public function save()
    {
        $rules = array(
            array(
                'field' => 'keyword',
                'label' => lang('keyword'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'link_url',
                'label' => lang('link_url'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'global_search_monthly',
                'label' => lang('global_search_monthly'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'usa_search',
                'label' => lang('usa_search'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'search_result',
                'label' => lang('search_result'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'search_intitle',
                'label' => lang('search_intitle'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'compete_index',
                'label' => lang('compete_index'),
                'rules' => 'trim|is_natural',
            ),
            array(
                'field' => 'intitle',
                'label' => lang('intitle'),
                'rules' => 'trim|is_natural',
            ),
            array(
                'field' => 'price_per_click',
                'label' => lang('price_per_click'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'page_first_ten',
                'label' => lang('page_first_ten'),
                'rules' => 'trim|is_natural',
            ),
            array(
                'field' => 'com_ranking',
                'label' => lang('com_ranking'),
                'rules' => 'trim',
            ),
            array(
                'field' => 'compete_price',
                'label' => lang('compete_price'),
                'rules' => 'trim|is_natural',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if($this->input->post('keyword_id') < 0 )
        {
            if ($this->seo_keyword_model->check_exists('seo_keyword', array('keyword' => $this->input->post('keyword'))))
            {
                echo $this->create_json(0, lang('keyword_exists'));
                return;
            }
        }
        else
        {
            if (trim($this->input->post('keyword')) !== $this->seo_keyword_model->get_one('seo_keyword', 'keyword', array('id' => $this->input->post('keyword_id'))))
            {
                if ($this->seo_keyword_model->check_exists('seo_keyword', array('keyword' => $this->input->post('keyword'))))
                {
                    echo $this->create_json(0, lang('keyword_exists'));
                    return;
                }
            }
        }
        $search_monthly = trim($this->input->post('global_search_monthly'));
        $global_search_monthly = formate_to_number($search_monthly);
        if(positive_numeric($global_search_monthly) === FALSE && $global_search_monthly != 0 )
        {
           echo $this->create_json(0, lang('a_number_greater_than_zero'));
           return;
        }

        $usa_search = trim($this->input->post('usa_search'));
        $usa_search = formate_to_number($usa_search);
        if(positive_numeric($usa_search) === FALSE && $usa_search != 0 )
        {
           echo $this->create_json(0, lang('a_number_greater_than_zero'));
           return;
        }

        $search_result = trim($this->input->post('search_result'));
        $search_result = formate_to_number($search_result);
        if(positive_numeric($search_result) === FALSE && $search_result != 0 )
        {
           echo $this->create_json(0, lang('a_number_greater_than_zero'));
           return;
        }

        $search_intitle = trim($this->input->post('search_intitle'));
        $search_intitle = formate_to_number($search_intitle);
        if(positive_numeric($search_intitle) === FALSE && $search_intitle != 0 )
        {
           echo $this->create_json(0, lang('a_number_greater_than_zero'));
           return;
        }
           
        $data = array(
            'keyword_id'                    => $this->input->post('keyword_id'),
            'keyword'                       => trim($this->input->post('keyword')),
            'link_url'                      => trim($this->input->post('link_url')),
            'global_search_monthly'         => $global_search_monthly,
            'usa_search'                    => $usa_search,
            'search_result'                 => $search_result,
            'search_intitle'                => $search_intitle,
            'intitle'                       => trim($this->input->post('intitle')),
            'compete_index'                 => trim($this->input->post('compete_index')),
            'compete_price'                 => trim($this->input->post('compete_price')),
            'price_per_click'               => trim($this->input->post('price_per_click')),
            'page_first_ten'                => trim($this->input->post('page_first_ten')),
            'level'                         => $this->input->post('level'),
            'com_ranking'                   => trim($this->input->post('com_ranking')),
            'note'                          => trim($this->input->post('note')),
            'creator'                       => get_current_user_id(),
        );

        try
        {
            $keyword_id = $this->seo_keyword_model->save_keyword($data);

            $user_ids = $this->input->post('permissions');
            $catalog_ids = $this->input->post('catalogs');

            if (empty($user_ids) || empty($catalog_ids) )
            {
                $user_ids = array();
                $catalog_ids = array();
            }
            $this->seo_keyword_model->save_keyword_permissions($keyword_id, $user_ids);
            $this->seo_keyword_model->save_keyword_catalogs($keyword_id, $catalog_ids);

            $company_ids = $this->input->post('company_permissions') ;
            if (empty($company_ids))
            {
                $company_ids = array();
            }
            $this->seo_service_company_model->save_keyword_company_permissions($keyword_id, $company_ids);

            echo $this->create_json(1, lang('save_keyword_successed'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_keyword()
    {
        $keyword_id = $this->input->post('id');
        $this->seo_keyword_model->drop_keyword($keyword_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $categorys = $this->seo_keyword_model->fetch_all_content_catalog();

        $keywords = $this->seo_keyword_model->fetch_all_keywords();

        $data = array(
            'keywords' => $keywords,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
