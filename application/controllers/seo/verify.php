<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;
class Verify extends Mallerp_no_key
{
    public function __construct()
    {
		parent::__construct();
        
        define('FAILED', -1);
        define('UNKOWN', 0);
        define('VERIFIED', 1);
        
        $this->load->model('seo_model');
        $this->load->helper('seo_helper');
    }

    public function verify_all_releases()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'seo_verify_all_releases.php') === FALSE)
        {
            exit;
        }
        set_time_limit(0);
        $not_verified_releases = $this->seo_model->fetch_all_not_verified_releases();


        foreach ($not_verified_releases as $release)
        {
            $this->_proccess_verifying_release($release);
        }
    }

    public function verify_release($release_id)
    {
        $release = $this->seo_model->fetch_not_verified_release($release_id);        
        if (empty($release))
        {
            return ;
        }
        $this->_proccess_verifying_release($release);
    }

    private function _proccess_verifying_release($release)
    {
        if (empty($release->validate_url) || empty($release->content))
        {
            return;
        }

        switch ($release->res_category)
        {
            case 'Press release':
                $this->_proccess_verifying_press_release($release);
                break;
            default:
                switch ($release->con_type)
                {
                    case 'Book Marking':
                        $this->_proccess_verifying_bookmark_release($release);
                        break;
                    case 'Comment':
                        $this->_proccess_verifying_comment_release($release);
                        break;
                    default:
                        $this->_proccess_verifying_article_release($release);
                        break;
                }
        }
    }

    private function _proccess_verifying_bookmark_release($release)
    {        
        $release_content = trim(strip_tags($release->content));
        $pattern = '/url:(.*?)title:(.*?)keywords:.*/is';
        $matches = array();
        preg_match($pattern, $release_content, $matches);

        if (isset($matches[2]))
        {
            $url = trim(str_replace('/', '\/', $matches[1]));
            $title = trim(str_replace('/', '\/', $matches[2]));


            $verify_pattern = "/href=['\"]" . $url . "['\"]/i";

            echo $verify_pattern, "\n";

            $page_content = @file_get_contents($release->validate_url);

            
            if (empty ($page_content))
            {
                return;
            }            

            if (preg_match($verify_pattern, $page_content))
            {
                $status = 1;
                echo $release->id, ': Yes bookmark!', "\n";
            }
            else
            {
                $status = -1;
                echo $release->id, ': No bookmark!', "\n";
            }
            
            $this->seo_model->update_release($release->id, array('status' => $status));
        }
    }

    private function _proccess_verifying_comment_release($release)
    {
        $release_content = trim(strip_tags($release->content));
        $pattern = '/url:(.*?)keywords:(.*?)content:.*/is';
        $matches = array();
        preg_match($pattern, $release_content, $matches);
        
        if (isset($matches[1]))
        {
            $url = trim(str_replace('/', '\/', $matches[1]));


            $verify_pattern = "/href=['\"]" . $url . "['\"]/i";

            echo $verify_pattern, "\n";

            $page_content = @file_get_contents($release->validate_url);


            if (empty ($page_content))
            {
                return;
            }

            if (preg_match($verify_pattern, $page_content))
            {
                $status = 1;
                echo $release->id, ': Yes comment!', "\n";
            }
            else
            {
                $status = -1;
                echo $release->id, ': No comment!', "\n";
            }

            $this->seo_model->update_release($release->id, array('status' => $status));
        }
    }

    private function _proccess_verifying_article_release($release, $verify_links = TRUE)
    {
        $page_content = @file_get_contents($release->validate_url);

        if (empty ($page_content))
        {
            return;
        }
        $release_content = $release->content;
        
        // get all a tag
        $pattern = '/href="(.*?)"/';
        $matches = array();
        preg_match_all($pattern, $release_content, $matches);
        $links = array_unique($matches[1]);

        $release_content = preg_replace('/<.*?>/s', '', $release_content);
        $release_content = preg_replace('/&[^;]*;/', '', $release_content);
        $release_content = preg_replace('/[[“”"’\']/', '', $release_content);
        $release_content = preg_replace('/[^(\x20-\x7F)]+/', '', $release_content);
        $pattern = '/[.;,:\n]/';
        $sentences = preg_split($pattern, $release_content, NULL, PREG_SPLIT_NO_EMPTY);
        foreach ($sentences as $key => $sentence)
        {
            $sentence = preg_replace('/\s+/', ' ', $sentence);
            $sentences[$key] = $sentence;
        }
        $sentences = array_filter(array_unique($sentences));

        $status = 1;
        
        if ($verify_links)
        {
            foreach ($links as $link)
            {
                if (strpos($page_content, $link) === FALSE)
                {
                    $status = 0;
                    $remark = "can't find '$link'";
                    echo $remark, "\n";
                    $this->seo_model->update_release($release->id, array('remark' => $remark));
                    break;
                }
                echo "find '$link'\n";
            }
        }

        echo '1---------------', "\n";
        var_dump($page_content);
        $original_page_content = $page_content;
        $page_content = preg_replace('/<.*?>/s', '', $page_content);
        $page_content = preg_replace('/&[^;]*;/', '', $page_content);
        $page_content = preg_replace('/[“”"’\']/', '', $page_content);
        $page_content = preg_replace('/[^(\x20-\x7F)]+/', '', $page_content);
        $page_content = preg_replace('/\s+/', ' ', $page_content);
        echo '2---------------', "\n";
        var_dump($page_content);

        $failed_count = 0;
        foreach ($sentences as $sentence)
        {
            $sentence = trim($sentence);
            if (empty($sentence))
            {
                continue;
            }
            
            if (strpos($page_content, $sentence) === FALSE)
            {
                // try the orignial page content
                if (strpos($original_page_content, $sentence) === FALSE)
                {
                    $sentence_word_only = preg_replace('/[^A-z]+/', '', $sentence);
                    $page_content_word_only = preg_replace('/[^A-z]+/', '', $page_content);

                    // try to clear thing.
                    if (strpos($page_content_word_only, $sentence_word_only) === FALSE)
                    {
                        if (++$failed_count > 10)
                        {
                            $status = -1;
                            $remark = "can't find '$sentence'";
                            echo $remark, "\n";
                            $this->seo_model->update_release($release->id, array('remark' => $remark));
                                break;
                        }
                    }
                }
            }
            echo "find '$sentence'\n";
        }

        $this->seo_model->update_release($release->id, array('status' => $status));
    }

    private function _proccess_verifying_press_release($release)
    {
        return $this->_proccess_verifying_article_release($release, FALSE);
    }

    public function restore_all_resources_release_left()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'restore_all_resources_release_left.php') === FALSE)
        {
            exit;
        }
        $this->seo_model->restore_all_resources_release_left();
    }

    public function verify_resource_pr_alexa()
    {
        $resources = $this->seo_model->fetch_all_resource_data();
        //print_r($resources);
        $current_time = mktime();
        $seven_day_time = 60*60*24*7;
        foreach ($resources as $resource)
        {
            echo $url = $resource->url;
            echo "<br>";
            echo $current_pr = get_pr($url);
            echo "<br>";
            //$root_pr = get_pr($url);
            //if ($root_pr !== '')
            //{
              //  $data['root_pr'] = $root_pr;
            //}
            //$data['current_pr']  = $current_pr;
            echo $data['alexa_rank']  = get_alexa_ranking($url);
            echo "<br>";
            //$reach_rate = get_change_percent($url);
            //print_r($reach_rate);
            echo "<br>";
            //echo $rank = get_alexa_ranking($url);
            echo "<br>";
            /*
            $counter = count($reach_rate);
            $data['alexa_change_3_monthes'] = $reach_rate[0];
            $data['alexa_change_1_month'] = $reach_rate[1];
            if($counter=="3")
            {
                $data['alexa_change_7_days'] = $reach_rate[2];
            }
            else
            {
                $data['alexa_change_7_days']= "0";
            }
            $data['robot_updated'] = mktime();
            $robot_updated = $resource->robot_updated;
            $robot_up_time = strtotime($robot_updated);
             * 
             */
            //if(($robot_up_time+$seven_day_time)<$current_time)
            //{
               //$this->seo_model->update_resource_data($data);
            //}
        }//of --foreach
       //$this->load->view('seo/seo_show_data',$data);
    }
}
?>
