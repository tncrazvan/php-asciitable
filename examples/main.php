<?php
chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';
use Razshare\AsciiTable\AsciiTable;

$table = new AsciiTable();

$table->add("key","value");
$table->add("hello","world");

echo $table->toString(true,true);