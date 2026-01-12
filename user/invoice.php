<?php
require_once "../admin/db.php";
require_once "../fpdf/fpdf.php";
require_once "../phpqrcode/qrlib.php";

/* ================= ORDER ID ================= */
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid Order");

/* ================= FETCH ORDER + USER ================= */
$o = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.state, u.pincode
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE o.id = $id
"));
if (!$o) die("Order not found");

/* ================= FETCH ITEMS ================= */
$items = mysqli_query($conn,"
    SELECT oi.*, p.pet_name
    FROM order_items oi
    JOIN pets p ON p.id = oi.pet_id
    WHERE oi.order_id = $id
");

$invoiceNo = "INV-" . date("Y") . "-" . $o['id'];

/* ================= QR CODE ================= */
$tempDir = __DIR__ . "/../temp/";
if (!is_dir($tempDir)) mkdir($tempDir,0777,true);

$qrUrl  = "http://localhost/pet/user/order_details.php?id=".$o['id'];
$qrFile = $tempDir."qr_".$o['id'].".png";
QRcode::png($qrUrl, $qrFile, QR_ECLEVEL_H, 4);

/* ================= PDF START ================= */
$pdf = new FPDF();
$pdf->AddPage();

/* ================= WATERMARK ================= */
$watermark = __DIR__ . "/../assets/watermark.jpg";
if (file_exists($watermark)) {
    $pdf->Image($watermark, 30, 80, 150);
}

/* ================= HEADER ================= */
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,6,"Black Berry",0,1);
$pdf->SetFont("Arial","",10);
$pdf->Cell(0,5,"Email: blackberry@gmail.com | Phone: +91  98256-98256",0,1);
$pdf->Ln(2);
$pdf->Line(10,30,200,30);

/* ================= LOGO ================= */
$logo = __DIR__ . "/../assets/logo.jpg";
if (file_exists($logo)) {
    $pdf->Image($logo,160,10,30);
}

/* ================= ORDER INFO ================= */
$pdf->Ln(6);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(100,6,"Order ID: ".$o['id'],0,0);
$pdf->Cell(0,6,"Invoice No: ".$invoiceNo,0,1);

$pdf->Cell(100,6,"Order Date: ".date("d-m-Y",strtotime($o['created_at'])),0,0);
$pdf->Cell(0,6,"Payment: Cash On Delivery",0,1);

/* ================= ADDRESSES ================= */
$pdf->Ln(4);
$startY = $pdf->GetY();

$pdf->SetFont("Arial","B",10);
$pdf->Cell(95,6,"Billing Address",0,0);
$pdf->Cell(95,6,"Shipping Address",0,1);

$pdf->SetFont("Arial","",9);

/* Billing */
$pdf->SetXY(10,$startY+6);
$pdf->MultiCell(95,5,
    $o['name']."\n".
    $o['phone']."\n".
    $o['email']."\n".
    $o['address']."\n".
    $o['city'].", ".$o['state']." - ".$o['pincode']
);

/* Shipping */
$pdf->SetXY(105,$startY+6);
$pdf->MultiCell(95,5,
    $o['name']."\n".
    $o['phone']."\n".
    $o['address']."\n".
    $o['city'].", ".$o['state']." - ".$o['pincode']
);

$pdf->SetY(max($pdf->GetY(), $startY+36));

/* ================= PRODUCT TABLE ================= */
$pdf->Ln(4);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(70,7,"Pet",1);
$pdf->Cell(20,7,"Qty",1,0,'C');
$pdf->Cell(30,7,"Price",1,0,'R');
$pdf->Cell(30,7,"Tax",1,0,'R');
$pdf->Cell(30,7,"Total",1,1,'R');

$pdf->SetFont("Arial","",9);
$subtotal = 0;

while($i=mysqli_fetch_assoc($items)){
    $tax = round($i['subtotal'] * 0.12,2);
    $subtotal += $i['subtotal'];

    $pdf->Cell(70,7,$i['pet_name'],1);
    $pdf->Cell(20,7,$i['quantity'],1,0,'C');
    $pdf->Cell(30,7,"Rs. ".number_format($i['price'],2),1,0,'R');
    $pdf->Cell(30,7,"Rs. ".number_format($tax,2),1,0,'R');
    $pdf->Cell(30,7,"Rs. ".number_format($i['subtotal'],2),1,1,'R');
}

/* ================= TOTAL SUMMARY ================= */
$rightX = 120;
$labelW = 50;
$valueW = 30;

$pdf->Ln(6);
$pdf->SetFont("Arial","",10);

$pdf->SetX($rightX);
$pdf->Cell($labelW,6,"Subtotal",0,0,'R');
$pdf->Cell($valueW,6,"Rs. ".number_format($subtotal,2),0,1,'R');

$pdf->SetX($rightX);
$pdf->Cell($labelW,6,"GST (12%)",0,0,'R');
$pdf->Cell($valueW,6,"Rs. ".number_format($o['gst'],2),0,1,'R');

$pdf->SetX($rightX);
$pdf->Cell($labelW,6,"Delivery Charge",0,0,'R');
$pdf->Cell($valueW,6,"Rs. ".number_format($o['delivery_charge'],2),0,1,'R');

$pdf->SetX($rightX);
$pdf->Cell($labelW,6,"Discount",0,0,'R');
$pdf->Cell($valueW,6,"- Rs. ".number_format($o['discount'],2),0,1,'R');

$pdf->Ln(2);
$pdf->SetFont("Arial","B",12);
$pdf->SetX($rightX);
$pdf->Cell($labelW,8,"Grand Total",0,0,'R');
$pdf->Cell($valueW,8,"Rs. ".number_format($o['grand_total'],2),0,1,'R');

/* ================= MANAGER SIGNATURE ================= */
$signPath = __DIR__ . "/../assets/manager_sign.jpg";
if (file_exists($signPath)) {

    $pdf->Ln(8);
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(0,6,"Authorized Signature",0,1,'R');

    $pdf->Image($signPath, 140, $pdf->GetY(), 40);

    $pdf->Ln(18);
    $pdf->SetFont("Arial","B",10);
    $pdf->Cell(0,6,"Taibani Mo Samar (Manager)",0,1,'R');
}

/* ================= QR CODE ================= */
if (file_exists($qrFile)) {
    $pdf->Rect(160,230,35,35);
    $pdf->Image($qrFile,162,232,31);
}

$pdf->Ln(6);
$pdf->SetFont("Arial","I",8);
$pdf->Cell(0,5,"Scan QR to view order details",0,1,"C");

/* ================= FOOTER ================= */
$pdf->Ln(4);
$pdf->Cell(0,5,"This is a computer generated invoice. No signature required.",0,1,"C");

/* ================= OUTPUT ================= */
$pdf->Output("D","Invoice_".$invoiceNo.".pdf");

/* ================= CLEANUP ================= */
if (file_exists($qrFile)) unlink($qrFile);
exit;
