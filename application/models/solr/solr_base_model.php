<?php
class Solr_base_model extends CI_Model
{
    protected $solr_client;
    protected $solr_query;
    protected $CI; 
    
    public function  __construct() {
        parent::__construct();
        $this->load->database();

        $this->CI = & get_instance();
        
        $options = array(
            'hostname'  => SOLR_SERVER_HOSTNAME,
            'port'      => SOLR_SERVER_PORT,
        );
        $this->solr_client = new SolrClient($options);
        $this->solr_query = new SolrQuery('*:*');
    }
}
?>
