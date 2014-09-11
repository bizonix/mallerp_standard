<?php
require_once APPPATH.'controllers/order/order'.EXT;
class Refund_resend_statistics extends Order
{
    public $cur_rates;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('ebay_model');
        $this->load->model('solr/solr_base_model');
        $this->load->model('solr/solr_statistics_model');
        $this->load->helper('solr');
        $this->template->add_js('static/js/sorttable.js');
        $rates = $this->order_model->fetch_currency();
        foreach ($rates as $rate)
        {
            $this->cur_rates[$rate->name_en] = $rate->ex_rate;
        }
    }

    public function by_type()
    {
        $duty_user = NULL;
        if(!$this->input->is_post())
        {
             $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
             $end_time = date("Y-m-d H:i:s");
        }
        else
        {
             $begin_time = $this->input->post('begin_time');
             $end_time = $this->input->post('end_time');
        }

        $refund_type_obj = $this->order_model->fetch_all_bad_comment_type();
        $refund_types = array();
        foreach ($refund_type_obj as $item)
        { 
            $refund_types[$item->id] = $item->type;
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
            'refund_verify_type',
            $begin_time, 
            $end_time,
            $duty_user
        );
        $refunds = (array)$result['facet'];
        $query = $result['query'];
        $refund_duties = array();
        $item_no = array();
        if ( ! empty($query->docs))
        {
            foreach ($query->docs as $row)
            {
                foreach ($row->refund_duties as $duty)
                { 
                    $refund_duties[] = $duty;
                    $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                    @$item_no[$the_type] .= lang('order_number').'：'.$row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('content').'：'.$row->refund_verify_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('refund_verify_type').'：'.$the_type.br().'----------'.br();
                }
            }
        }
        $refund_duties = array_unique($refund_duties);
        $feedback_obj = $this->ebay_model->feedback_statistics('verify_type', null, $begin_time, $end_time);
        $feedbacks = array();
        $count = 1;
        foreach ($feedback_obj as $row)
        {
            if(empty($feedbacks[$row->verify_type])) {
                $feedbacks[$row->verify_type] = $count;
            } else {
                $feedbacks[$row->verify_type] += $count;
            }
        }

        $feedback_obj = $this->ebay_model->feedback_statistics_count('item_no', NULL, $begin_time, $end_time);
        $feedback_obj_count = $this->ebay_model->feedback_statistics('verify_type', NULL, $begin_time, $end_time);
        foreach($feedback_obj_count as $feed_count)
        {
            if(empty($row_count[$feed_count->verify_type])) {
                $row_count[$feed_count->verify_type] = $count;
            } else {
                $row_count[$feed_count->verify_type] += $count;
            }
        }
        $feedbacks = array();
        $feed_feedback_content = array();
        foreach ($feedback_obj as $row)
        {
            $feedbacks[$row->verify_type] = @$row_count[$row->verify_type];
            $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->verify_type));
            $row->item_no = isset($row->item_no) ? lang('order_number').'：'.$row->item_no : null;
            @$feed_feedback_content[$the_type] .=  $row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                    lang('content').'：'.$row->feedback_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                    lang('order_bad_comment_type').'：'.$the_type.br().'----------'.br();
        }

        $all_types = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));

        $data = array(
            'begin_time'            => $begin_time,
            'end_time'              => $end_time,
            'refund_types'          => $refund_types,
            'order_count'           => $order_count,
            'refunds'               => $refunds,
            'feedbacks'             => $feedbacks,
            'all_types'             => $all_types,
            'refund_duties'         => $refund_duties,
            'refund_feedback_count' => $refund_feedback_count,
            'feed_feedback_content'  => $feed_feedback_content,
            'item_no'               => $item_no,
        );
        $this->set_2column('sidebar_statistics_order');
        $this->template->write_view('content', 'order/refund_resend/by_type', $data);
        $this->template->render();
    }

     public function by_sku()
    {
        $duty_user = NULL;
        if(!$this->input->is_post())
        {
             $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
             $end_time = date("Y-m-d H:i:s");
        }
        else
        {
             $begin_time = $this->input->post('begin_time');
             $end_time = $this->input->post('end_time');
        }

        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
            'refund_skus',
            $begin_time,
            $end_time,
            $duty_user
        );

        $refunds = (array)$result['facet'];

        $item_no = array();

        $query = $result['query'];
        if ( ! empty($query->docs))
        {
            foreach ($query->docs as $row)
            {
                foreach ($row->refund_skus as $sku) {
                    if ($sku) {
                        $item_content = $this->order_model->get_one('myebay_feedback', 'feedback_content', array('item_no' => $row->item_no));
                        $item_type = @$this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                        if ($item_type) {
                            $item_no[$sku] = isset($item_no[$sku]) ? $item_no[$sku] : null;
                            $item_no[$sku] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                    lang('content') . '：' . $item_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                                    lang('refund_verify_type') . '：' . $item_type . br() . '----------' . br();
                        } else {
                            $refunds[$sku]--;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }
        $sku_result = $this->solr_statistics_model->fetch_field_count('skus', $begin_time, $end_time);
        $all_skus = (array)$sku_result['facet'];

        $feedback_obj = $this->ebay_model->feedback_statistics('feedback_sku_str', null, $begin_time, $end_time);
        $feedbacks = array();
        $feed_feedback_content = array();
        $count = 1;
        foreach ($feedback_obj as $row)
        {
            $skus = explode(',', $row->feedback_sku_str);
            foreach ($skus as $sku) {
                if (empty($feedbacks[$sku])) {
                    $feedbacks[$sku] = $count;
                } else {
                    $feedbacks[$sku] += $count;
                }
                $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->verify_type));
                if ( ! isset($feed_feedback_content[$sku]))
                {
                    $feed_feedback_content[$sku] = '';
                }
                $feed_feedback_content[$sku] .= lang('order_number') . 
                    ': ' .$row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                    lang('content') . ': ' . $row->feedback_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                    lang('order_bad_comment_type') . ': ' . $the_type . br() . '----------' . br();
            }
        }
        $refund_feedback_skus = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));

        $data = array(
            'begin_time'            => $begin_time,
            'end_time'              => $end_time,
            'order_count'           => $order_count,
            'refunds'               => $refunds,
            'feedbacks'             => $feedbacks,
            'refund_feedback_skus'  => $refund_feedback_skus,
            'all_skus'              => $all_skus,
            'refund_feedback_count' => $refund_feedback_count,
            'feed_feedback_content' => $feed_feedback_content,
            'item_no' => $item_no,
        );
        $this->set_2column('sidebar_statistics_order');
        $this->template->write_view('content', 'order/refund_resend/by_sku', $data);
        $this->template->render();
    }

    public function by_duty()
    {
        $duty_user = NULL;
        if(!$this->input->is_post())
        {
             $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
             $end_time = date("Y-m-d H:i:s");
        }
        else
        {
             $begin_time = $this->input->post('begin_time');
             $end_time = $this->input->post('end_time');
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
            'refund_duties',
            $begin_time,
            $end_time,
            $duty_user
        );
        $refunds = (array)$result['facet'];
        $query = $result['query'];

        $refund_duties = array();
        $item_no = array();
        $refund_verify_content = array();
        $refund_verify_type = array();
        if ( ! empty($query->docs))
        {
            foreach ($query->docs as $row)
            {
                foreach ($row->refund_duties as $duty)
                {
                    $refund_duties[] = $duty;
                    $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                    @$item_no[$duty] .= lang('order_number').'：'.$row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('content').'：'.$row->refund_verify_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                            lang('refund_verify_type').'：'.$the_type.br().'----------'.br();
                }
            }
        }
        $refund_duties = array_unique($refund_duties);

        $feed_item_no = array();
        $feed_feedback_content = array();
        $feed_verify_type = array();


        $feedback_obj = $this->ebay_model->feedback_statistics('feedback_duty', null, $begin_time, $end_time);
        $rows = $this->ebay_model->feedback_statistics_all();
        foreach($rows as $row)
        {
            if($row->feedback_duty)
            {
                $duty = $row->feedback_duty;
                $row->item_no = isset($row->item_no) ? lang('order_number').'：'.$row->item_no : null;
                @$feed_feedback_content[$duty] .=  $row->item_no.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                        lang('content').'：'.$row->feedback_content.'&nbsp;&nbsp;&nbsp;&nbsp;'.
                        lang('order_bad_comment_type').'：'.$row->type.br().'----------'.br();
            }
        }

        $feedbacks = array();
        $count = 1;
        foreach ($feedback_obj as $row)
        {
            $duties = explode(',', $row->feedback_duty);
            foreach ($duties as $duty)
            {
                if (empty($feedbacks[$duty]))
                {
                    $feedbacks[$duty] = $count;
                }
                else
                {
                    $feedbacks[$duty] += $count;               
                }
            }
        }

        $refund_feedback_duties = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));
        $data = array(
            'begin_time'              => $begin_time,
            'end_time'                => $end_time,
            'order_count'             => $order_count,
            'refunds'                 => $refunds,
            'feedbacks'               => $feedbacks,
            'refund_feedback_duties'  => $refund_feedback_duties,
            'refund_duties'           => $refund_duties,
            'refund_feedback_count'   => $refund_feedback_count,
            
            'item_no'                 => $item_no,
            'refund_verify_content'   => $refund_verify_content,
            'refund_verify_type'      => $refund_verify_type,

            'feed_feedback_content'  => $feed_feedback_content,
            'feed_verify_type'       => $feed_verify_type,
        );

        $this->set_2column('sidebar_statistics_order');
        $this->template->write_view('content', 'order/refund_resend/by_duty', $data);
        $this->template->render();
    }

    public function by_buyer_id() {
        $duty_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
                        'buyer_id',
                        $begin_time,
                        $end_time,
                        $duty_user,
                        '100'
        );
        $refunds = (array) $result['facet'];
        $query = $result['query'];
        $refund_duties = array();
        $item_no = array();
        $refund_verify_content = array();
        $refund_verify_type = array();
        if (!empty($query->docs)) {
            foreach ($query->docs as $row) {
                $buyer_id = $row->buyer_id;
                $refund_duties[] = $buyer_id;
                $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                @$item_no[$buyer_id] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('content') . '：' . $row->refund_verify_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('refund_verify_type') . '：' . $the_type . br() . '----------' . br();
            }
        }
        $refund_duties = array_unique($refund_duties);

        $feed_item_no = array();
        $feed_feedback_content = array();
        $feed_verify_type = array();


        $feedback_obj = $this->ebay_model->feedback_statistics('buyer_id', null, $begin_time, $end_time);
        $rows = $this->ebay_model->feedback_statistics_all();
        foreach ($rows as $row) {
            if ($row->buyer_id) {
                $buyer_id = $row->buyer_id;
                $row->item_no = isset($row->item_no) ? lang('order_number') . '：' . $row->item_no : null;
                @$feed_feedback_content[$buyer_id] .= $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('content') . '：' . $row->feedback_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('order_bad_comment_type') . '：' . $row->type . br() . '----------' . br();
            }
        }

        $feedbacks = array();
        $count = 1;
        foreach ($feedback_obj as $row) {
            $buyer_id = $row->buyer_id;
            if (empty($feedbacks[$buyer_id])) {
                $feedbacks[$buyer_id] = $count;
            } else {
                $feedbacks[$buyer_id] += $count;
            }
        }

        $refund_feedback_buyers = array_unique(array_merge(array_keys($refunds), array_keys($feedbacks)));
        $refund_feedback_count = array_sum(array_values($refunds)) + array_sum(array_values($feedbacks));
        $data = array(
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'order_count' => $order_count,
            'refunds' => $refunds,
            'feedbacks' => $feedbacks,
            'refund_feedback_buyers' => $refund_feedback_buyers,
            'refund_duties' => $refund_duties,
            'refund_feedback_count' => $refund_feedback_count,
            'item_no' => $item_no,
            'refund_verify_content' => $refund_verify_content,
            'refund_verify_type' => $refund_verify_type,
            'feed_feedback_content' => $feed_feedback_content,
            'feed_verify_type' => $feed_verify_type,
        );

        $this->set_2column('sidebar_statistics_order');
        $this->template->write_view('content', 'order/refund_resend/by_buyer_id', $data);
        $this->template->render();
    }

    public function by_city() {
        $duty_user = NULL;
        if (!$this->input->is_post()) {
            $begin_time = date("Y-m-d H:i:s", strtotime('-1 month'));
            $end_time = date("Y-m-d H:i:s");
        } else {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }
        $order_count = $this->order_model->fetch_order_count_by_input_date($begin_time, $end_time);
        $result = $this->solr_statistics_model->fetch_refund_resend(
                        'town_city',
                        $begin_time,
                        $end_time,
                        $duty_user,
                        '100'
        );
        $refunds = (array) $result['facet'];
        $query = $result['query'];
        $refund_duties = array();
        $item_no = array();
        $refund_verify_content = array();
        $refund_verify_type = array();
        if (!empty($query->docs)) {
            foreach ($query->docs as $row) {
                $town_city = $row->town_city;
                $refund_duties[] = $town_city;
                $the_type = $this->order_model->get_one('order_bad_comment_type', 'type', array('id' => $row->refund_verify_type));
                @$item_no[$town_city] .= lang('order_number') . '：' . $row->item_no . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('content') . '：' . $row->refund_verify_content . '&nbsp;&nbsp;&nbsp;&nbsp;' .
                        lang('refund_verify_type') . '：' . $the_type . br() . '----------' . br();
            }
        }
        $refund_duties = array_unique($refund_duties);

        $feed_item_no = array();
        $feed_feedback_content = array();
        $feed_verify_type = array();




        $feedbacks = array();


        $refund_feedback_city = array_unique(array_merge(array_keys($refunds)));
        $refund_feedback_count = array_sum(array_values($refunds));
        $data = array(
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'order_count' => $order_count,
            'refunds' => $refunds,
            'feedbacks' => $feedbacks,
            'refund_feedback_city' => $refund_feedback_city,
            'refund_duties' => $refund_duties,
            'refund_feedback_count' => $refund_feedback_count,
            'item_no' => $item_no,
            'refund_verify_content' => $refund_verify_content,
            'refund_verify_type' => $refund_verify_type,
            'feed_feedback_content' => $feed_feedback_content,
            'feed_verify_type' => $feed_verify_type,
        );

        $this->set_2column('sidebar_statistics_order');
        $this->template->write_view('content', 'order/refund_resend/by_city', $data);
        $this->template->render();
    }

}
