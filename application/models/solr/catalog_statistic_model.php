<?php
require 'solr_base_model'.EXT;
class Catalog_statistic_model extends Solr_base_model
{
    public function fetch_order_infos($begin_time, $end_time, $status_ids)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $limit = 4000;
        $start = 0;
        $sql = "order_status:($status_ids[0] OR $status_ids[1])";
        $sql .= " AND input_datetime:[$begin_time TO $end_time]";
        $this->solr_query->setQuery($sql);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_COUNT);
        $this->solr_query->setFacet(TRUE);       
        $this->solr_query->setRows($limit);
        $this->solr_query->addField('skus');
        $this->solr_query->addField('qties');
        $this->solr_query->addField('gross');
        $this->solr_query->addField('net');
        $this->solr_query->addField('currency');
        $this->solr_query->setFacetOffset(0);
        $datas = array();
        $i = 1;
        do
        {            
            $this->solr_query->setStart($start);
            $updateResponse = $this->solr_client->query($this->solr_query);
            $response_array = $updateResponse->getResponse();
            if ($i > 1)
            {
                $datas = array_merge($datas, $response_array->response->docs);
            }
            else
            {
                $datas = $response_array->response->docs;
            }
            $num = $response_array->response->numFound;
            $start += 4000;
            $i++;
        }
        while ($start < $response_array->response->numFound);
        return $datas;
    }

    public function fetch_sku_price()
    {
        $this->db->select('sku,price');
        $this->db->from('product_basic');
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_dep_saler_sku($saler_ids)    
    {
        $this->db->select('pb.sku, pb.catalog_id, pb.price, sp.saler_id');
        $this->db->from('product_basic as pb');
        $this->db->join('product_catalog_sale_permission as  sp', 'pb.catalog_id = sp.product_catalog_id');
        $this->db->where_in('saler_id', $saler_ids);
        $query = $this->db->get();
        return $query->result();
    }
}
?>
