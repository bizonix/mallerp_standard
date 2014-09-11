<?php
function fetch_user_all_integrals($user_id = NULL)
{
    $CI = & get_instance();
    $result = $CI->seo_model->fetch_user_all_integrals($user_id);
    $total_count = 0;
    foreach ($result as $row)
    {
        if( !isset($row->integral))
        {
            $count = 0;
        }
        else
        {
            $count = $row->integral;
        }
        $total_count += $count;
    }

    return $total_count;
}

function fetch_content_resources_count($id = NULL)
{
    $CI = & get_instance();
    $seo_resources = $CI->seo_model->fetch_all_release_resource_by_user($id);
    $resource_ids =  array();
    foreach($seo_resources as $seo_resource)
    {
         $resource_ids[] = $seo_resource->resource_id;
    }
    $resources = $CI->seo_model->fetch_no_release_resources_by_user($CI->get_current_user_id(), $resource_ids, $id, -1);
    $count = 0;
    foreach($resources as $resource)
    {
        $count++;
    }
    return $count;
}

function get_pr($url)
{
    $CI = & get_instance();
    if ( ! isset($CI->seo_google))
    {
        $CI->load->library('seo_google');
    }
    return $CI->seo_google->get_pr($url);
}
function get_alexa_ranking($url)
{
    $url = "http://www.alexa.com/search?q=".$url."&r=site_siteinfo&p=bigtop";
    $ranking = 0;
    $content = file_get_contents($url);
    $preg = "/<span class=\"traffic-stat-label\">(.*)<\/a>/iUs";
    if(preg_match($preg, $content, $array))
    {
        $rank =explode(":",$array[0]);
        $ranking =  $rank[1];
        $ranking = str_replace(",","",$ranking);
    }
    return $ranking;
}
function get_change_percent($url)
{
    $percent_url = "http://www.alexa.com/siteinfo/".$url;
    $content = file_get_contents($percent_url);
    $preg = "/<div id=\"reach\" c=\"1\" y=\"r\" class=\"tw-table\">(.*)<div id=\"bounce\" c=\"\" y=\"b\" class=\"tw-table\">/iUs";
    $change_rate = array();
    if(preg_match($preg,$content,$array))
    {
       $str = explode("%",$array[0]);
       $num =  count($str)-2;
       for($i=$num;$i>=0;$i--)
       {
           static $j=0;
           if($j=="3")
           {
               break;
            }
           if(strpos($str[$i], "+")){
              $str1 = substr($str[$i],strpos($str[$i], "+"));
           }
           else if(strpos($str[$i], "-"))
           {
               if($i == "0")
               {
                   $strt = substr($str[$i],-5);
                   $str1 = substr($strt,strpos($strt, "-"));
               }
               else
               {
                   $str1 = substr($str[$i],strpos($str[$i], "-"));
               }
           }
           $change_rate[] = $str1;
           $j++;
       }
       return $change_rate;
    }
    else
    {
      return $change_rate = array('0','0','0');
    }
 }
function get_main_domain($url)
{
    if(substr($url,0,7)!= "http://")
    {
        $url = "http://".$url;
    }
    $url = parse_url($url);
    $url = strtolower($url['host']) ;
    $domain = array('com','cn','name','org','net','hk','uk','tw','info','cl','gov','edu','biz','aero','coop','museum','pro','tv','mil','int','eu','us','ru');
    $main_url = $url;
    $dd = implode('|',$domain);
    $main_url = preg_replace('/(\.('.$dd.'))*\.('.$dd.')$/iU','',$main_url);
    $main_url = explode('.',$main_url);
    $main_url = array_pop($main_url);
    $main_url = substr($url,strrpos($url,$main_url));
    return $main_url;
}
function get_index_url($url)
{
    if(substr($url,0,7) != "http://")
    {
        $url = "http://".$url;
        $urls = parse_url($url);
        $url = $urls['host'];
    }
    return $url;
}
?>
