<?php
require_once '../vendor/autoload.php';
use net\razshare\asciitable\AsciiTable;

$table = new AsciiTable();

$table->add("key","value");
$table->add("hello","world");

echo $table->toString();