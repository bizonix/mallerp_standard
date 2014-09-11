<?php
include_once "../db.inc.php";
include_once 'PDFMerger.php';

$date = date('Y-m-d');
if (isset($_GET['day'])) {
    $date = $_GET['day'];
    switch ($_GET['day']) {
        case 'yesterday' :
            $date = date('Y-m-d', strtotime($_GET['day']));
            break;
    }
}

$pdf = new PDFMerger;
$pdf_folder = "/var/www/html/mallerp2011/static/pdf/$date/";
if (! file_exists($pdf_folder)) {
    echo '没有pdf文件!';
    return;
}
// get transaction id
$sql = "select transaction_id from epacket_confirm_list where print_label = 1 and input_date like '$date%' order by id asc";

$query = mysql_query($sql, $db_conn);
while ($result = mysql_fetch_assoc($query)) {
    $transaction_id = $result['transaction_id'];
    $pdf_url = $pdf_folder . $transaction_id . '.pdf';
    $pdf->addPDF($pdf_url, 'all');
}

$pdf->merge('download', "epacket $date.pdf");

