<?php

require_once 'lib/autoload.php';

class Iterator_CsvTest extends PHPUnit_Framework_TestCase {
  public function test() {
    $csv = new Iterator_Csv(__DIR__.'/data.csv');
    foreach ($csv as $row) {
      print_r($row);
    }
  }
}
