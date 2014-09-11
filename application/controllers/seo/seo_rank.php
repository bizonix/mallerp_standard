<?php
require_once APPPATH.'controllers/seo/seo'.EXT;
class Seo_rank extends Seo
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('seo_model');
        $this->load->library('form_validation');
        $this->load->helper('seo');
    }
    public function seo_rank_search()
    {
        $this->template->write_view('content', 'seo/seo_rank_search');
        $this->template->render();
    }
    public function seo_rank_result()
    {      
        $url_search = trim($this->input->post('url_search'));
        $main_url = get_main_domain($url_search);
        $a_rank = get_alexa_ranking($url_search);
        $index_url = get_index_url($url_search);
        $index_pr = get_pr($index_url);
        $page_pr = get_pr($url_search);
        $reach_rate = get_change_percent($url_search);
        $data = array(
            'rank'       => $a_rank,
            'index_pr'   => $index_pr,
            'page_pr'    => $page_pr,
            'index_url'  => $main_url,
            'page_url'   => $url_search,
            'rank_url'   => $main_url,
            'reach_rate' => $reach_rate,
        );
        $this->load->view("seo/seo_rank_search_result",$data);
     }
}
?>
