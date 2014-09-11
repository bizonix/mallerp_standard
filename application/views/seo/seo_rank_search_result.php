<?php
$rates = array();
$counter = count($reach_rate);
$rates['3month'] = $reach_rate[0];
$rates['1month'] = $reach_rate[1];
if($counter=="3"){
    $rates['7day'] = $reach_rate[2];
}
else
{
    $rates['7day']= "0";
}

$head = array(
    array('text' => lang('name')),
    array('text' => lang('value')),
    array('text' => lang('link_url')),
    );
$data[] = array(
    lang('index_pr'),
    $index_pr,
    $index_url,
    );
$data[] = array(
    lang('page_pr'),
    $page_pr,
    $page_url,
    );
$data[] = array(
    lang('alexa_rank'),
    $rank,
    $rank_url,
    );
$data[] = array(
    lang('7_day_rate_change'),
    $rates['7day'],
    $index_url,
    );
$data[] = array(
    lang('one_month_rate_change'),
    $rates['1month'],
    $index_url,
    );
$data[] = array(
    lang('three_month_rate_change'),
    $rates['3month'],
    $index_url,
    );
echo $this->block->generate_table($head, $data);
?>
