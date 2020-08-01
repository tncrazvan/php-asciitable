<?php
require_once '../vendor/autoload.php';
use com\github\tncrazvan\asciitable\AsciiTable;

$table = new AsciiTable();

$table->add("key","value");
$table->add("hello","world");

echo $table->toString();