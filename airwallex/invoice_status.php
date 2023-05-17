<?php
require_once __DIR__ . '/../../../init.php';;

use \WHMCS\ClientArea;

$invoiceid = $_GET['invoiceid'];

$ca = new ClientArea();
$userid = $ca->getUserID();

$invoice = localAPI('GetInvoice', ['invoiceid' => $invoiceid]);

echo $invoice['userid'] !== $userid ? 'Unauthorized' : $invoice['status'];
