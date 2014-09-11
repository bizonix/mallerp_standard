<?php
require 'solr_base_model'.EXT;
class Email_search_model extends Solr_base_model
{   
    public function serach_email($sql, $start = 0)
    {
        $limit = 100;
        $this->solr_query->setQuery($sql);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setStart($start);
        $this->solr_query->setRows($limit);
        $this->solr_query->addField('item_titles');
        $this->solr_query->addField('buyer_email');
        $this->solr_query->addField('buyer_name');
        $this->solr_query->addField('address_line_1');
        $this->solr_query->addField('address_line_2');
        $this->solr_query->addField('town_city');
        $this->solr_query->addField('state_province');
        $this->solr_query->addField('country');
        $this->solr_query->addField('zip_code');
        $this->solr_query->addField('contact_phone_number');
        $this->solr_query->addField('item_no');
        $this->solr_query->addField('ship_confirm_date');
        $this->solr_query->addField('skus');
        $this->solr_query->addField('qties');
        $this->solr_query->addField('track_number');
        $this->solr_query->addField('shipping_code');
        $this->solr_query->addField('company_email');
        $this->solr_query->addField('input_user');
        $this->solr_query->setFacetMinCount(1, 'country');
        $this->solr_query->setFacetOffset(0);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        return $response_array;
//        echo "<pre>";
//        print_r($response_array);
    }

    public function save_sub($data)
    {
        $this->db->insert('email_subscription', $data);
    }

    public function update_sub($data, $sub)
    {
        $this->db->update('email_subscription', $data, array('subscription' => $sub));
    }

    public function check_exits($table, $sub)
    {
        $this->db->where(array('subscription' => $sub));
        $this->db->from($table);
        return $this->db->count_all_results() > 0 ? TRUE : FALSE;
    }

    public function fetch_subscription()
    {
        $this->db->select('subscription');
        $this->db->from('email_subscription');
        $query = $this->db->get();
        $datas =  $query->result();
        $subs = array();
        foreach ($datas as $data)
        {
            $subs[$data->subscription] = $data->subscription;
        }
        return $subs; 
    }

    public function delete_all_subscription()
    {
        $this->db->empty_table('email_subscription');
    }
    public function fetch_currency_code()
    {
        $this->db->select('code');
        $this->db->from('currency_code');
        $this->db->where(array('code !=' => '[edit]'));
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_users()
    {
        $this->db->select('login_name,name_en, phone, platform1, email');
        $this->db->from('user');
        $query = $this->db->get();
        return $query->result();
    }

    public function test()
    {
        $limit = 100;
        $sql = 'country:"United States" OR country:"France" OR country:"Italy"';
        $this->solr_query->setQuery($sql);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setStats(TRUE);
        //$this->solr_query->setStart($start);
        $this->solr_query->setRows($limit);
        $this->solr_query->addField('buyer_email');
        $this->solr_query->addField('buyer_name');
        $this->solr_query->addField('address_line_1');
        $this->solr_query->addField('address_line_2');
        $this->solr_query->addField('town_city');
        $this->solr_query->addField('state_province');
        $this->solr_query->addField('country');
        $this->solr_query->addField('zip_code');
        $this->solr_query->addField('contact_phone_number');
        $this->solr_query->addField('company_email');
        $this->solr_query->setFacetMinCount(1, 'country');

        $this->solr_query->setFacetOffset(0);
        $updateResponse = $this->solr_client->query($this->solr_query);
        $response_array = $updateResponse->getResponse();
        return $response_array;
        echo "<pre>";
        print_r($response_array);
    }
    
    
}
?>
