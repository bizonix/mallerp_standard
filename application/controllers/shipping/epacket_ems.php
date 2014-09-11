<?php
require_once APPPATH . 'controllers/shipping/shipping' . EXT;
class Epacket_ems extends Shipping
{
    private $user;
    private $token;
	private $is_register;
    protected $order_statuses = array();
	protected $sender_info = array();
	protected $collect_info = array();
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('script');
        $this->load->model('mixture_model');
        $this->load->model('epacket_model');
        $this->load->model('order_model');
		$this->load->model('ebay_order_model');
        $this->load->model('product_model');
        $this->load->model('order_shipping_record_model');
        $this->load->model('shipping_code_model');
		$this->load->model('shipping_subarea_model');
        $this->load->helper('shipping_helper');
    }
	public function add_order() {
        $this->order_model->enable_get_track_number();

        $orders = $this->epacket_model->fetch_unconfirmed_ems_list(NULL);
        $counter = 0;//var_dump($orders);
        foreach ($orders as $order) {
            if ($this->order_model->is_get_track_number_stop()) {
                break;
            }
            $shipped = $this->order_model->check_order_shipped_or_not($order->id);
            if ($shipped) {
                //continue;
            }
            $this->script->fetch_specification_epacket_track_number(array('order_id' => $order->id));

            if ((++$counter/ 20)  == 0)
            {
                sleep(3);
            }
        }
        $this->order_model->reset_get_track_number();
    }
	public function download_pdf($date = NULL) {
		$timeStamp = strtotime($date);
		$date = date("Y-m-d", $timeStamp);
        $this->process_download_pdf($date);
    }
	private function process_download_pdf($date = NULL, $part = FALSE) {
        require_once APPPATH . 'libraries/pdf/PDFMerger.php';

        $pdf = new PDFMerger;

        if (!($date)) {
            $date = date('Y-m-d');
        }

        $pdf_folder = "/var/www/html/mallerp/static/ems/$date/";
        $input_user = get_current_user_id();

        $priority = fetch_user_priority_by_system_code('shipping');
        // director? then set input_user as FALSE, no need to filter it
        if ($priority > 1) {
            $input_user = FALSE;
        }
        $confirmed_list = $this->epacket_model->fetch_ems_confirmed_list($date, $input_user, $part);
        if (empty($confirmed_list) OR !file_exists($pdf_folder)) {
            echo 'No pdf for ' . $date;
            return;
        }

        foreach ($confirmed_list as $order) {
            $track_number = $order->track_number;
            $pdf_url = $pdf_folder . $track_number . '.pdf';
			$sku_pdf_url = $pdf_folder . 'sku_list_'.$track_number . '.pdf';
            if ( ! file_exists($pdf_url))
            {
                continue;
            }
            $pdf->addPDF($pdf_url, 'all');
			
            $data = array(
                'downloaded' => 1,
            );
            $this->epacket_model->update_ems_confirmed_list($order->id, $data);
			if ( ! file_exists($sku_pdf_url))
            {
                continue;
            }
			$pdf->addPDF($sku_pdf_url, 'all');
        }
        $pdf->merge('download', "ems_eub_$date.pdf");
    }
	public function download_part_pdf($date = NULL) {
        $this->process_download_pdf($date,TRUE);
    }
}
?>