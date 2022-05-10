<?php
require_once __DIR__ . '/../../../init.php';;

use \Illuminate\Database\Capsule\Manager as Capsule;
use \WHMCS\ClientArea;

$invoiceid = $_GET['invoiceid'];

$ca = new ClientArea();
$userid = $ca->getUserID();

$query = Capsule::table('tblinvoices')->where('id', $invoiceid)->where('userid', $userid)->first();

echo $query->status ?? 'Unpaid';
