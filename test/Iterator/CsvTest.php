<?php

require_once 'lib/autoload.php';

class Iterator_CsvTest extends PHPUnit_Framework_TestCase {
  private function writeCsv($filename, $data, $delimiter = ',', $terminator = "\n") {
    $h = fopen($filename, 'w');
    if ($h === false) {
      throw new Exception("Unable to open file, '$filename'");
    }
    foreach ($data as $row) {
      $line = implode($delimiter, $row);
      fwrite($h, $line);
      fwrite($h, $terminator);
    }
    fclose($h);
  }
  
  public function provider() {
    return array(
      array(',', "\n"),
      array(',', "\r"),
    );
  }
  
  /**
   * @dataProvider provider
   */
  public function test($delimiter, $terminator) {
    $filename = __DIR__.'/data.csv';
    
    $data = array(
      array('id','fruit','date'),
      array('3','Orange','12 December 2008'),
      array('2','Pear','1 January 2001'),
      array('1','Melon','19 March 1998'),
      array('4','Grape','20 October 1908'),
    );
    $this->writeCsv($filename, $data, $delimiter, $terminator);
    
    $csv = new Iterator_Csv($filename);
    $count = 0;
    foreach ($csv as $row) {
      $count++;
      $this->assertEquals($data[0], array_keys($row));
      $this->assertEquals($data[$count], array_values($row));
    }
    $this->assertEquals(count($data) - 1, $count);
    
    unlink($filename);
  }
}
