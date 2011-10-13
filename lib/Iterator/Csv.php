<?php

class Iterator_Csv implements Iterator {
  private $filename;
  private $handle;
  private $keys;
  private $line;
  
  public function __construct($filename) {
    $this->filename = $filename;
  }
  
  public function __destruct() {
    $this->close();
  }
  
  private function open() {
    $this->close();
    $this->handle = fopen($this->filename, "r");
    if ($this->handle === false) {
      throw new Exception("Unable to open file, '$this->filename'");
    }
  }
  
  private function close() {
    if ($this->handle) {
      fclose($this->handle);
      $this->handle = null;
    }
  }
  
  private function readNext() {
    $current = fgetcsv($this->handle);
    if ($current) {
      $current = array_combine($this->keys, $current);
    } else {
      $this->close();
    }
    $this->current = $current;
    $this->line++;
  }
  
  public function rewind() {
    $this->open();
    $this->keys = fgetcsv($this->handle);
    $this->line = -1;
    $this->readNext();
  }
  
  public function current() {
    return $this->current;
  }
  
  public function key() {
    return $this->line;
  }
  
  public function next() {
    $this->readNext();
  }
  
  public function valid() {
    return !empty($this->current);
  }
}
