<?php

class Logger {

  public static function logError($msg1, $msg2 = '') {
    $date = date("D M d, Y G:i");
    echo $msg1;
    echo $msg2;
    $myfile = file_put_contents(\ApplicationConfig::$logFileError, "ERROR" ."\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $date . "\t" . $msg1 . "\t" . $msg2 . "\n", FILE_APPEND | LOCK_EX);
  }

  // I don't know anymore, using $msg1 and $msg as parameter names didn't work :-(
  public static function logDebug($bla, $blupp = '') {
    // echo "logDebug: " . "  bla = ";
    // var_dump($bla);
    $date = date("D M d, Y G:i");
    $s = "-------------------------------------------------------------------------------------";
    $myfile = file_put_contents(\ApplicationConfig::$logFileDebug, $s . "\n "  . $bla."\n" . $blupp . "\n\n", FILE_APPEND | LOCK_EX);
  }

  public static function logDebugPrintR($bla, $blupp = '') {
    // echo "logDebug: " . "  bla = ";
    // var_dump($bla);
    $date = date("D M d, Y G:i");
    $s = "-------------------------------------------------------------------------------------";

    // thank you! https://stackoverflow.com/questions/13361376/write-output-of-print-r-in-a-txt-file-php
    $output = print_r($blupp, true);
    $myfile = file_put_contents(\ApplicationConfig::$logFileDebug, $s . "\n "  . $bla."\n" . $output . "\n\n", FILE_APPEND | LOCK_EX);
  }
}