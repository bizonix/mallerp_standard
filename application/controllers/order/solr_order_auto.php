<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Solr_order_auto extends Mallerp_no_key
{    
    public function __construct() {
        parent::__construct();
        $this->load->model('solr_order_model');
        $this->load->helper('solr');
    }
    
    public function import_orders()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'solr_auto_import_orders.php') === FALSE)
        {
            exit;
        }        
        $end_time = date('Y-m-d H:i:s');
        
        set_time_limit(0);

        $limit = 1000;
        $end_time = get_current_time();
        $offset = 0;
        $order_table = 'order_list_completed';
        $order_id = 'order_id';
        $start_time = $this->solr_order_model->fetch_solr_order_updated_date();
		$start_time=date('Y-m-d H:i:s',mktime(substr($start_time,11,2),substr($start_time,14,2),substr($start_time,17,2),substr($start_time,5,2),substr($start_time,8,2)-45,substr($start_time,0,4)));
		echo $start_time;
        $successful = TRUE;
        
        $successful1 = TRUE; //$this->_process_import_order($order_table, $order_id, $start_time, $end_time, $limit, $offset);
        
        $offset = 0;
        $order_table = 'order_list';
        $order_id = 'id';

        $successful2 = $this->_process_import_order($order_table, $order_id, $start_time, $end_time, $limit, $offset);

        if ($successful1 && $successful2)
        {
            $this->solr_order_model->update_solr_order_updated_date($end_time);
        }
    }
    
    public function trancate_all_orders()
    {
        $options = array(
            'hostname' => SOLR_SERVER_HOSTNAME,
            'port'     => SOLR_SERVER_PORT,
        );

        $client = new SolrClient($options);

        $updateResponse = $client->deleteByQueries(array('*:*'));

        $client->commit();
    }

    private function _process_import_order($order_table, $order_id, $start_time, $end_time, $limit, $offset)
    {
        $total = $this->solr_order_model->fetch_order_count_by_updated($order_table, $start_time, $end_time);
        $successful = TRUE;
        $options = array(
            'hostname' => SOLR_SERVER_HOSTNAME,
            'port'     => SOLR_SERVER_PORT,
        );      
        
        echo 'table: ' . $order_table, "\n";
        echo 'total: ' . $total, "\n";
        do
        {
            echo 'off set: ' . $offset, "\n";
            $orders = $this->solr_order_model->fetch_orders_by_updated($order_table, $start_time, $end_time, $limit, $offset);

            $docs = array();
            foreach ($orders as $order)
            {
                $client = new SolrClient($options);

                $doc = new SolrInputDocument();

                $doc->addField('id', $order_table . ':' . $order->$order_id);
                $skus = explode(',', $order->sku_str);
                foreach ($skus as $sku)
                {
                    $doc->addField('skus', $sku); 
                }
                $doc->addField('list_datetime', $order->list_date . 'T' . $order->list_time . "Z");
                $doc->addField('buyer_name', $order->name);
                $doc->addField('buyer_id', $order->buyer_id);
                $doc->addField('list_type', $order->list_type);
                $doc->addField('payment_status', $order->payment_status);
                $doc->addField('subject', $order->subject);
                $doc->addField('currency', $order->currency);
                $doc->addField('gross', make_number($order->gross, 0));
                $doc->addField('fee', make_number($order->fee, 0));
                $doc->addField('net', make_number($order->net, 0));
                $doc->addField('time_zone', $order->time_zone);
                $doc->addField('note', $order->note);
                $doc->addField('buyer_email', $order->from_email);
                $doc->addField('company_email', $order->to_email);
                $doc->addField('transaction_id', $order->transaction_id);
                $doc->addField('payment_type', $order->payment_type);
                $doc->addField('shipping_address', $order->shipping_address);
                $doc->addField('address_status', $order->address_status);


                $item_titles = explode_item_title($order->item_title_str);
                foreach ($item_titles as $item_title)
                {            
                    $doc->addField('item_titles', $item_title);
                }    
                $item_ids = explode(',', $order->item_id_str);
                foreach ($item_ids as $item_id)
                {            
                    $doc->addField('item_ids', $item_id);
                }             
                $doc->addField('item_url', $order->item_url);
                $doc->addField('closing_date', $order->closing_date);
                $doc->addField('invoice_number', $order->invoice_number);
                $doc->addField('address_line_1', $order->address_line_1);
                $doc->addField('address_line_2', $order->address_line_2);
                $doc->addField('town_city', $order->town_city);
                $doc->addField('state_province', $order->state_province);
                $doc->addField('country', $order->country);
                $doc->addField('zip_code', $order->zip_code);
                $doc->addField('contact_phone_number', $order->contact_phone_number);
                $doc->addField('income_type', $order->income_type);

                $qties = explode(',', $order->qty_str);
                foreach ($qties as $qty)
                {            
                    //$doc->addField('qties', make_number($qty));
                }            
                $doc->addField('description', $order->descript);
                $doc->addField('input_datetime', to_utc_format($order->input_date));
                $doc->addField('input_user', $order->input_user);
                $doc->addField('order_status', make_number($order->order_status));
                $doc->addField('check_date', to_utc_format($order->check_date));
                $doc->addField('check_user', $order->check_user);
                $doc->addField('print_label_date', to_utc_format($order->print_label_date));
                $doc->addField('label_content', $order->label_content);
                $doc->addField('item_no', $order->item_no);
                $doc->addField('print_label_user', $order->print_label_user);
                $doc->addField('ship_confirm_date', to_utc_format($order->ship_confirm_date));
                $doc->addField('ship_confirm_user', $order->ship_confirm_user);
                $doc->addField('ship_weight', make_number($order->ship_weight, 0));
                $doc->addField('ship_remark', $order->ship_remark);
                $doc->addField('track_number', $order->track_number);
                $doc->addField('shipping_code', $order->is_register, 0);
                $doc->addField('cost', make_number($order->cost, 0));
                $doc->addField('cost_date', to_utc_format($order->cost_date));
                $doc->addField('cost_user', $order->cost_user);

                $product_costs = explode(',', trim($order->product_cost, ','));
                foreach ($product_costs as $product_cost)
                {            
                    $doc->addField('product_costs', make_number($product_cost, 0));
                }                    
                $doc->addField('product_total_cost', $order->product_cost_all);
                $doc->addField('shipping_cost', make_number($order->shipping_cost, 0));
                $doc->addField('return_date', to_utc_format($order->return_date));
                $doc->addField('return_remark', $order->return_remark);
                $doc->addField('return_user', $order->return_user);
                $doc->addField('return_why', $order->return_why);
                $doc->addField('return_order', $order->return_order);
                $doc->addField('return_cost', make_number($order->return_cost, 0));
                $doc->addField('sys_remark',  $order->sys_remark);
                $doc->addField('order_receive_date', $order->order_receive_date);
                $doc->addField('email_status', $order->email_status);
                $doc->addField('stock_user_id', make_number($order->stock_user_id));
                $doc->addField('saler_id', make_number($order->saler_id));

                $purchaser_ids = explode(',', $order->purchaser_id_str);            
                foreach ($purchaser_ids as $purchaser_id)
                {            
                    $doc->addField('purchaser_ids', make_number($purchaser_id));
                }                     
                $developer_ids = explode(',', $order->developer_id);
                foreach ($developer_ids as $developer_id)
                {            
                    $doc->addField('developer_ids', make_number($developer_id));
                }              

                $doc->addField('trade_fee', make_number($order->trade_fee, 0));
                $doc->addField('listing_fee', make_number($order->listing_fee, 0));
                $doc->addField('profit_rate', make_number($order->profit_rate, 0));

                $doc->addField('refund_verify_status', $order->refund_verify_status);
                $doc->addField('refund_verify_type', $order->refund_verify_type);
                $doc->addField('refund_verify_content', $order->refund_verify_content);
                $refund_duties = explode(',', $order->refund_duty);
                foreach ($refund_duties as $refund_duty)
                {
                    $doc->addField('refund_duties', $refund_duty);
                }
                $refund_skus = explode(',', $order->refund_sku_str);
                foreach ($refund_skus as $refund_sku)
                {
                    $doc->addField('refund_skus', $refund_sku);
                }


                $docs[] = $doc;
            }

            try
            {
                if ( ! empty($docs))
                {
                    $response = $client->addDocuments($docs);
                    $client->commit();
                }
            }
            catch (SolrException $e)
            {
                $successful = FALSE;
                var_dump($e);
                break;
            }
            $offset += $limit;
        }
        while ($offset < $total);

        return $successful;
    }

    public function facet_test()
    {
        $options = array(
            'hostname'  => SOLR_SERVER_HOSTNAME,
            'port'      => SOLR_SERVER_PORT,
        );
        
        $client = new SolrClient($options);
        $query = new SolrQuery('*');

        //$query->setQuery('input_datetime:[2010-01-01T00:00:00Z TO 2010-06-01T00:00:00Z]');
        $query->setQuery('input_datetime:[* TO 2010-06-01T00:00:00Z]');
        $query->setQuery('order_status:9');
        $query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $query->setFacet(TRUE);
        $query->setFacetLimit(200);
        $query->addFacetField('country');
        //$query->setFacetMinCount(2);
        $query->setFacetMinCount(1, 'country');
        $query->setFacetOffset(0);
        //$query->setFacetDateStart('2012-01-01T00:00:00:Z', 'input_date');
        //$query->setFacetPrefix('c');
        
        $updateResponse = $client->query($query);
        $response_array = $updateResponse->getResponse();
        $facet_datas = $response_array->facet_counts->facet_fields;
        echo '<pre>';
        print_r($facet_datas);        
    }

    public function facet_date()
    {
        $options = array(
            'hostname'  => SOLR_SERVER_HOSTNAME,
            'port'      => SOLR_SERVER_PORT,
        );
        
        $client = new SolrClient($options);
        $query = new SolrQuery('*:*');

        //$query->setQuery('input_datetime:[2010-01-01T00:00:00Z TO 2010-06-01T00:00:00Z]');
        $query->setFacet(TRUE);
        $query->setFacetSort(SolrQuery::FACET_SORT_INDEX);
        $query->setFacetLimit(20000);
        $query->addFacetDateField('input_datetime');
        //$query->setFacetDateStart('2010-06-01T00:00:00Z');
        $query->setFacetDateStart('2008-06-01T00:00:00Z');
        $query->setFacetDateEnd('2010-12-01T00:00:00Z');
        $query->setFacetDateGap('+1MONTH');
        $query->setFacetDateHardEnd(TRUE);
        //$query->setFacetMinCount(2);
        $query->setFacetOffset(0);
        //$query->setFacetDateStart('2012-01-01T00:00:00:Z', 'input_date');
        //$query->setFacetPrefix('c');
        
        $updateResponse = $client->query($query);
        $response_array = $updateResponse->getResponse();
        $facet_datas = $response_array->facet_counts->facet_fields;
        echo '<pre>';
        print_r($response_array->facet_counts);        
    }        
}
