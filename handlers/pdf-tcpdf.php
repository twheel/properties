<?php

# http://www.codenachos.com/view/basic-tcpdf-working-example

require_once ($_SERVER["DOCUMENT_ROOT"].'/tcpdf/config/lang/eng.php');
require_once ($_SERVER["DOCUMENT_ROOT"].'/tcpdf/tcpdf.php');

//TCP pdf code
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Me');
$pdf->SetTitle('Student Report');
	
$pdf->SetCellPadding(0);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 8, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

$page->layout = false;

$html = $_POST['html'];
$html = str_replace ('"/css/', '"'.$_SERVER["DOCUMENT_ROOT"].'/css/', $html);
$html = str_replace ('"/files/', '"'.$_SERVER["DOCUMENT_ROOT"].'/files/', $html);
$html = str_replace ('"/apps/', '"'.$_SERVER["DOCUMENT_ROOT"].'/apps/', $html);
$html = str_replace ('"/js/', '"'.$_SERVER["DOCUMENT_ROOT"].'/js/', $html);
$html = str_replace ('"/cache/', '"'.$_SERVER["DOCUMENT_ROOT"].'/cache/', $html);

// Print text using writeHTMLCell()
$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Student report.pdf', 'I');

exit();

?>
