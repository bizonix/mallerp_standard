<?php
include 'PDFMerger.php';

$pdf = new PDFMerger;

for ($i = 0; $i < 200; $i++) {
	$pdf->addPDF('samplepdfs/one.pdf', '1, 3, 4')
	->addPDF('samplepdfs/two.pdf', '1-2')
	->addPDF('samplepdfs/three.pdf', 'all')
	->addPDF('samplepdfs/test.pdf', 'all');
}
	$pdf->merge('download', 'samplepdfs/TEST2.pdf');
	
	//REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
	//You do not need to give a file path for browser, string, or download - just the name.
