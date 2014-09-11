<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function epacket_specification_order($order_info,$sender_info,$receiver_info,$collect_info,$items)
{
	$orderid=$order_info['orderid'];
	$customercode=$order_info['customercode'];
	$volweight=$order_info['volweight'];
	$startdate=$order_info['startdate'];
	$enddate=$order_info['enddate'];
	$sender_name=$sender_info['sender_name'];
	$sender_postcode=$sender_info['sender_postcode'];
	$sender_phone=$sender_info['sender_phone'];
	$sender_mobile=$sender_info['sender_mobile'];
	$sender_province=$sender_info['sender_province'];
	$sender_city=$sender_info['sender_city'];
	$sender_county=$sender_info['sender_county'];
	$sender_company=$sender_info['sender_company'];
	$sender_street=$sender_info['sender_street'];
	$sender_email=$sender_info['sender_email'];
	
	$receiver_name=$receiver_info['receiver_name'];
	$receiver_postcode=$receiver_info['receiver_postcode'];
	$receiver_phone=$receiver_info['receiver_phone'];
	$receiver_mobile=$receiver_info['receiver_mobile'];
	$receiver_country=$receiver_info['receiver_country'];
	$receiver_province=$receiver_info['receiver_province'];
	$receiver_city=$receiver_info['receiver_city'];
	$receiver_county=$receiver_info['receiver_county'];
	$receiver_street=$receiver_info['receiver_street'];
	
	$collect_name=$collect_info['collect_name'];
	$collect_postcode=$collect_info['collect_postcode'];
	$collect_phone=$collect_info['collect_phone'];
	$collect_mobile=$collect_info['collect_mobile'];
	$collect_country='CN';
	$collect_province=$collect_info['collect_province'];
	$collect_city=$collect_info['collect_city'];
	$collect_county=$collect_info['collect_county'];
	$collect_company=$collect_info['collect_company'];
	$collect_street=$collect_info['collect_street'];
	$collect_email=$collect_info['collect_email'];
	$requestXMLBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<orders xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<order>
<orderid>$orderid</orderid>
<customercode>$customercode</customercode>
<vipcode></vipcode>
<clcttype>0</clcttype>
<pod>false</pod>
<untread>Returned</untread>
<volweight>$volweight</volweight>
<startdate>$startdate</startdate>
<enddate>$enddate</enddate>
<printcode>01</printcode>
<sender>
<name>$sender_name</name>
<postcode>$sender_postcode</postcode>
<phone>$sender_phone</phone>
<mobile>$sender_mobile</mobile>
<country>CN</country>
<province>$sender_province</province>
<city>$sender_city</city>
<county>$sender_county</county>
<company>$sender_company</company>
<street>$sender_street</street>
<email>$sender_email</email>
</sender>
<receiver>
<name>$receiver_name</name>
<postcode>$receiver_postcode</postcode>
<phone>$receiver_phone</phone>
<mobile>$receiver_mobile</mobile>
<country>$receiver_country</country>
<province>$receiver_province</province>
<city>$receiver_city</city>
<county>$receiver_county</county>
<street>$receiver_street</street>
</receiver>
<collect>
<name>$collect_name</name>
<postcode>$collect_postcode</postcode>
<phone>$collect_phone</phone>
<mobile>$collect_mobile</mobile>
<country>CN</country>
<province>$collect_province</province>
<city>$collect_city</city>
<county>$collect_county</county>
<company/>
<street>$collect_street</street>
<email>$collect_email</email>
</collect>
<items>
XML;
        foreach ($items as $item) {
        	$cnname=$item['cnname'];
            $enname=$item['enname'];
            $count=$item['count'];
            $weight=$item['weight'];
            $delcarevalue=$item['delcarevalue'];
            $requestXMLBody .= <<<XML
<item>
<cnname>$cnname</cnname>
<enname>$enname</enname>
<count>$count</count>
<unit>unit</unit>
<weight>$weight</weight>
<delcarevalue>$delcarevalue</delcarevalue>
<origin>CN</origin>
<description/>
</item>
XML;
        }

        $requestXMLBody .= <<<XML
</items>
<remark/></order></orders>
XML;
	return $requestXMLBody;
}
function epacket_specification_print_lable($orders)
{
	$requestXMLBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<orders xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
XML;
	foreach ($orders as $mailnum) {
    	$requestXMLBody .= <<<XML
<order>
<mailnum>$mailnum</mailnum>
</order>
XML;
        }

        $requestXMLBody .= <<<XML
</orders>
XML;
    return $requestXMLBody;
}
?>