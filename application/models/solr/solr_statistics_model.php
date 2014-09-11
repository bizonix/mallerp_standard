<?php
class Solr_statistics_model extends Solr_base_model
{

    public function fetch_country($status_id)
    {
        $this->solr_query->setQuery('order_status:"'.$status_id.'"');
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetLimit(20);
        $this->solr_query->addFacetField('country');
        $this->solr_query->setFacetMinCount(1, 'country');
        $this->solr_query->setFacetOffset(0);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        $facet_countries = $response_array->facet_counts->facet_fields->country;
        return $facet_countries;

    }

    public function fetch_country_all()
    {
        $this->solr_query->setQuery('*:*');
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetLimit(20);
        $this->solr_query->addFacetField('country');
        $this->solr_query->setFacetMinCount(1, 'country');
        $this->solr_query->setFacetOffset(0);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        $facet_countries = $response_array->facet_counts->facet_fields->country;
        return $facet_countries;
    }

    public function fetch_return_info_by_country($currency, $status_id, $begin_time, $end_time, $input_user)
    {
        $field = 'return_cost';
        $this->solr_query->setStats(TRUE);
        if(empty ($input_user))
        {
            $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        }
        else
        {
           $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
        }

        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('country');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
 
    }

    public function fetch_total_info_by_country($currency, $begin_time, $end_time, $input_user , $status_id = false)
    {

        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        if ($status_id == '-1')
        {
            $status_id = '00000001';
            if(empty($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'"  AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        } else {
            if (empty($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        }
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('country');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
//        echo "<pre>";
//        echo "<hr>";
//        print_r($stats->facets->country);

    }

    public function fetch_retrun_info_by_shipping_code($currency, $status_id, $begin_time, $end_time, $input_user)
    {

        $field = 'return_cost';
        $this->solr_query->setStats(TRUE);

        if (empty($input_user))
        {
           $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        }
        else
        {
           $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
        }

        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('shipping_code');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
    }

    public function fetch_total_info_by_ship_shipping_code($currency, $begin_time, $end_time, $input_user, $status_id = false)
    {
        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        if($status_id == '-1')
        {
            if(empty ($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        } else {
            if(empty ($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        }
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('shipping_code');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;

    }


    public function fetch_return_info_by_input_user($currency, $status_id, $begin_time, $end_time)
    {
        $field = 'return_cost';
        $this->solr_query->setStats(TRUE);
        $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('input_user');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
    }

    public function fetch_total_info_by_input_user($currency, $begin_time, $end_time, $status_id = false)
    {
        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        if($status_id == '-1')
        {
            $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        } else {
            $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        }
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('input_user');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
    }

    public function fetch_return_info_by_ship_confirm_user($currency, $status_id, $begin_time, $end_time,$input_user)
    {
        $field = 'return_cost';
        $this->solr_query->setStats(TRUE);
        if(empty ($input_user))
        {
            $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
        }
        else
        {
           $this->solr_query->setQuery('currency:"'.$currency.'" AND order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
        }
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('ship_confirm_user');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
    }

    public function fetch_total_info_by_ship_confirm_user($currency, $begin_time, $end_time, $input_user, $status_id = false)
    {
        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        if($status_id == '-1')
        {
            if(empty ($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND -order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        } else {
            if(empty ($input_user))
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']');
            }
            else
            {
                $this->solr_query->setQuery('currency:"'.$currency.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.'] AND input_user:"'.$input_user.'"');
            }
        }
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('ship_confirm_user');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $stats = $response_array->stats->stats_fields->$field;
        return $stats;
    }

    public function fetch_total_count_of_sku($field, $begin_time, $end_time, $input_user = NULL)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $total_counts = 100000;
        $query = <<<QUERY
input_datetime:[$begin_time TO $end_time]
QUERY;
        if (! empty ($input_user))
        {
            $query .= " AND input_user:$input_user";
        }
        $this->solr_query->setQuery($query);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();

        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );
    }

    public function fetch_return_count_of_sku($field, $status_id, $begin_time, $end_time, $input_user = NULL)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $total_counts = 100;
        $query = 'order_status:"'.$status_id.'" AND input_datetime:['.$begin_time.'  TO  '.$end_time.']';
        if (! empty ($input_user))
        {
            $query .= " AND input_user:$input_user";
        }

        $this->solr_query->setQuery($query);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );
    }

    public function check_second_ksk_in_past_six_month($buyer_id)
    {

        $cur_time = date('Y-m', strtotime('-1 month'));
        $past_time = date('Y-m', strtotime('-7 month'));
        $first_day_time = strtotime($cur_time."-01");
        $past_day_time = strtotime($past_time."-01");
        $first_day = date("Y-m-d H:i:s",$first_day_time);
        $past_day = date("Y-m-d H:i:s",$past_day_time);
        $first_day = to_utc_format($first_day);
        $past_day = to_utc_format($past_day );
        //echo $first_day;
        //echo $past_day;
        $this->solr_query->setQuery('input_datetime['.$past_day.' TO '.$first_day.'] AND buyer_id:"'.$buyer_id.'"');

        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();

        //$facet_skus = $response_array->facet_counts->facet_fields->skus;
        echo "<pre>";
        print_r($response_array);


        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );

    }



    public function fetch_refund_resend($field, $begin_time, $end_time, $duty_user = NULL)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $total_counts = 1000000;
        $query = <<<QUERY
refund_verify_status:[1 TO *]
AND
input_datetime:[$begin_time TO $end_time]
QUERY;
        if ($duty_user !== NULL)
        {
            $query .= " AND refund_duties:$duty_user";
        }
        $this->solr_query->setQuery($query);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setRows('10000');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField('buyer_id');
        $this->solr_query->addField('item_no');
        $this->solr_query->addField('refund_duties');

        $this->solr_query->addField('refund_verify_content');
        $this->solr_query->addField('refund_verify_type');

        
        $this->solr_query->addField('refund_verify_status');
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );
    }

    public function fetch_field_count($field, $begin_time, $end_time, $duty_user = NULL)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $total_counts = 1000000;
        $query = <<<QUERY
input_datetime:[$begin_time TO $end_time]
QUERY;
        if ($duty_user !== NULL)
        {
            $query .= " AND refund_duties:$duty_user";
        }
        $this->solr_query->setQuery($query);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();

        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );
    }

        function get_dept_name_by_id($id) {
        return $this->get_one('document_catalog', 'name', array('id' => $id));
    }

    public function fetch_refund_resend_by_duty($field)
    {
        $duty_user = get_current_user_name();
        $total_counts = 1000;
        $query = <<<QUERY
refund_verify_status:[1 TO *]
AND
refund_duties:$duty_user
QUERY;
        $this->solr_query->setQuery($query);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacetOffset('0');
        $this->solr_query->setRows('100');
        $this->solr_query->setFacetLimit($total_counts);
        $this->solr_query->addField('buyer_id');
        $this->solr_query->addField('item_no');
        $this->solr_query->addField('refund_duties');

        $this->solr_query->addField('refund_verify_content');
        $this->solr_query->addField('refund_verify_type');


        $this->solr_query->addField('refund_verify_status');
        $this->solr_query->addField($field);
        $this->solr_query->addFacetField($field);
        $this->solr_query->setFacetMinCount(1);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        return array(
            'facet' => $response_array->facet_counts->facet_fields->$field,
            'query' => $response_array->response,
        );
    }

    public function test()
    {

        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('country');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $docs = $response_array;
        $stats = $response_array->stats->stats_fields->$field->facets->country;
        $stats = (array)$stats;
        echo "<pre>";
        print_r($stats);
    }

}

?>
