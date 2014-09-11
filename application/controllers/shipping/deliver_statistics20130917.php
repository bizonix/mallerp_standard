<?php

require_once APPPATH . 'controllers/shipping/shipping' . EXT;

class Deliver_statistics extends Shipping {
    private $shipping_codes = array();
    public function __construct() {
        parent::__construct();

        $this->load->model('shipping_statistics_model');
        $this->load->model('shipping_code_model');
        $this->template->add_js('static/js/sorttable.js');

        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_codes = array();
        foreach ($shipping_code_object as $row)
        {
            $this->shipping_codes[] = $row->code;
        }
    }

    public function package_statistics()
    {
        if ( ! $this->input->is_post())
        {
            $split_date = FALSE;
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $split_date = $this->input->post('split_date');
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }

        list($scope_statistics, $shippers) = $this->shipping_statistics_model->fetch_scope_statistics($begin_time, $end_time, 1, NULL, $split_date);
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'split_date'          => $split_date,
            'shipping_codes'      => $this->shipping_codes,
        );

        $this->template->write_view('content', 'shipping/statistics/package_statistics', $data);
        $this->template->render();
    }

    public function department_package_statistics()
    {
        $shipper = NULL;
        if ( ! $this->input->is_post())
        {
            $split_date = FALSE;
            $begin_time = date('Y-m-d') . ' ' . '00:00:00';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $shipper = $this->input->post('shipper');
            $split_date = $this->input->post('split_date');
        }

        $priority = 2;
        list($scope_statistics, $shippers) = $this->shipping_statistics_model->fetch_scope_statistics($begin_time, $end_time, $priority, $shipper, $split_date);
        $all_shippers = $this->user_model->fetch_users_by_system_code('shipping');
        $data = array(
            'scope_statistics'    => $scope_statistics,
            'begin_time'          => $begin_time,
            'end_time'            => $end_time,
            'department'          => TRUE,
            'shippers'            => $shippers,
            'all_shippers'        => $all_shippers,
            'current_shipper'     => $shipper,
            'split_date'          => $split_date,
            'shipping_codes'      => $this->shipping_codes,
        );

        $this->template->write_view('content', 'shipping/statistics/department_package_statistics', $data);
        $this->template->render();
    }

    public function shipping_download()
    {
        $this->load->library('excel');
        $this->load->model('order_model');
        $result = $this->order_model->fetch_all_confirmed_pinyi_order(date('Y-m-d'));
        $head = array(
            'Date',
            '',
            'Record',
            '',
            'SKU',
            '',
            'Country',
            '',
            '币种',
            '人员识别',
            'mail',
            'if  Registered',
            "Weight（KG） \n",
        );
        $data = array();
        foreach ($result as $row)
        {
            $ship_confirm_date = explode(' ', $row->ship_confirm_date);
            $if_registered = ($row->is_register == 'PT' or $row->is_register == 'PT2') ? 1 : '';
            $shipping_weights = explode(',', $row->sub_ship_weight_str);
            $track_numbers = explode(',', $row->track_number);
            $i = 0;
            foreach ($shipping_weights as $shipping_weight)
            {
                $track_number = empty($track_numbers[$i]) ? '' : $track_numbers[$i];
                $data[] = array(
                    $ship_confirm_date[0],
                    '',
                    $row->id . '-' . $row->is_register,
                    '',
                    '',
                    '',
                    $row->country,
                    '',
                    '',
                    '李雪花',
                    $track_number,
                    $if_registered,
                    $shipping_weight / 1000,
                );
                $i++;
            }
        }

        $this->excel->array_to_excel($data, $head, 'pinyi_' . date('Y-m-d'));
    }

    public function shipping_eub_download()
    {
        $this->load->library('excel');
        $this->load->model('order_model');
        $result = $this->order_model->fetch_all_confirmed_pinyi_h_order(date('Y-m-d'));
        $head = array(
            'Date',
            '',
            'Record',
            '',
            'SKU',
            '',
            'Country',
            '',
            '币种',
            '人员识别',
            'mail',
            'if  Registered',
            "Weight（KG） \n",
        );
        $data = array();
        foreach ($result as $row)
        {
            $ship_confirm_date = explode(' ', $row->ship_confirm_date);
            $if_registered = 1;
            $data[] = array(
                $ship_confirm_date[0],
                '',
                $row->id . '-' . $row->is_register,
                '',
                '',
                '',
                $row->country,
                '',
                '',
                '李雪花',
                $row->track_number,
                $if_registered,
                $row->ship_weight / 1000,
            );
        }

        $this->excel->array_to_excel($data, $head, 'EUB_' . date('Y-m-d'));
//        $this->shipping_eub_download_over_50();
    }

    public function shipping_eub_download_over_50()
    {
        $this->load->library('excel');
        $this->load->model('order_model');
        $result = $this->order_model->fetch_all_confirmed_pinyi_h_order(date('Y-m-d'), true, false);
        $head = array(
            'Date',
            '',
            'Record',
            '',
            'SKU',
            '',
            'Country',
            '',
            '币种',
            '人员识别',
            'mail',
            'if  Registered',
            "Weight（KG） \n",
        );
        $data = array();
        foreach ($result as $row)
        {
            $ship_confirm_date = explode(' ', $row->ship_confirm_date);
            $if_registered = 1;
            $data[] = array(
                $ship_confirm_date[0],
                '',
                $row->id . '-' . $row->is_register,
                '',
                '',
                '',
                $row->country,
                '',
                '',
                '李雪花',
                $row->track_number,
                $if_registered,
                $row->ship_weight / 1000,
            );
        }

        $this->excel->array_to_excel($data, $head, 'EUB(50G以上)_' . date('Y-m-d'));
    }

    public function shipping_eub_download_below_50()
    {
        $this->load->library('excel');
        $this->load->model('order_model');
        $result = $this->order_model->fetch_all_confirmed_pinyi_h_order(date('Y-m-d'), false, true);
        $head = array(
            'Date',
            '',
            'Record',
            '',
            'SKU',
            '',
            'Country',
            '',
            '币种',
            '人员识别',
            'mail',
            'if  Registered',
            "Weight（KG） \n",
        );
        $data = array();
        foreach ($result as $row)
        {
            $ship_confirm_date = explode(' ', $row->ship_confirm_date);
            $if_registered = 1;
            $data[] = array(
                $ship_confirm_date[0],
                '',
                $row->id . '-' . $row->is_register,
                '',
                '',
                '',
                $row->country,
                '',
                '',
                '李雪花',
                $row->track_number,
                $if_registered,
                $row->ship_weight / 1000,
            );
        }

        $this->excel->array_to_excel($data, $head, 'EUB(50G以下)_' . date('Y-m-d'));
    }

    public function shipping_all_download()
    {
        $this->load->library('excel');
        $this->load->model('order_model');
        $result = $this->order_model->fetch_all_confirmed_all_order(date('Y-m-d'));
        $head = array(
            'Date',
            '订单号',
            '物流方式',
            '地址1',
            '地址2',
            '顾客名',
            '物品',
            "Weight（KG）",
            '追踪号',
            '录单备注',
            '发货备注',
            "发货人",
        );
        $data = array();
        foreach ($result as $row)
        {
            $ship_confirm_date = explode(' ', $row->ship_confirm_date);           
            $data[] = array(
                $ship_confirm_date[0],
                $row->item_no,
                $row->id . '-' . $row->is_register,
                $row->address_line_1,
                $row->address_line_2,
                $row->name,
                $row->sku_str,
                $row->ship_weight / 1000,
                $row->track_number,
                $row->sys_remark,
                $row->ship_remark,
                $row->ship_confirm_user,
            );
        }

        $this->excel->array_to_excel($data, $head, 'All_' . date('Y-m-d'));
    }
}
?>
