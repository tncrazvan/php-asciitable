<?php
require_once '../vendor/autoload.php';
use net\razsharen\asciitable\AsciiTable;

$table = new AsciiTable();

$table->add("key","value");
$table->add("hello","world");

echo $table->toString();