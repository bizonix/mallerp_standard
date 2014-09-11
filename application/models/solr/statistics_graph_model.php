<?php
class Statistics_graph_model extends Solr_base_model
{
    public function fetch_order_count_statistics_by_time_gap($begin_time, $end_time, $gap)
    {
        $field = 'input_datetime';
        $this->solr_query->setFacet(TRUE);
        $this->solr_query->setFacetSort(SolrQuery::FACET_SORT_INDEX);
        $this->solr_query->addFacetDateField($field);
        $this->solr_query->setFacetDateStart($begin_time);
        $this->solr_query->setFacetDateEnd($end_time);
        $this->solr_query->setFacetDateGap($gap);
        $this->solr_query->setFacetDateHardEnd(TRUE);
        $this->solr_query->setFacetOffset(0);
        $this->solr_query->setFacetLimit(20000);
        
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();

        return $response_array;
    }

    public function test()
    {
        $field = 'gross';
        $this->solr_query->setStats(TRUE);
        $this->solr_query->addStatsField($field);
        $this->solr_query->addStatsFacet('country');
        $response = $this->solr_client->query($this->solr_query);
        $response_array = $response->getResponse();
        $docs = $response_array;

        echo "<pre>";
        var_dump($docs);


        return;



        $result = array();
        $counties = array(
            'united states',
            'united kingdom',
        );
        $currencies = array(
            'USD',
            'AUD',
        );
        foreach ($docs as $doc)
        {
            $country = strtolower($doc->country);
            $currency = $doc->currency;
            $gross = $doc->net;
            if ( ! in_array($doc->currency, $currencies))
            {
                continue;
            }
            if ( ! in_array($country, $counties)) {
                $country = 'other';
            }
            if (isset($result[$country])) {
                if (isset($result[$country][$currency]))
                {
                    $result[$country][$currency] += $gross;
                }
                else
                {
                    $result[$country][$currency] = $gross;
                }
            }
            else {
                $result[$country] = array();
                $result[$country][$currency] = $gross;
            }

        }
        echo '<pre>';
        var_dump($result);
    }
}
?>
