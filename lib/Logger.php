<?php

class Logger {

  public static function logError(string $msg1, string $msg2) {
    $date = date("D M d, Y G:i");
    $myfile = file_put_contents(\ApplicationConfig::$logFile, "ERROR" ."\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $date . "\t" . $msg1 . "\t" . $msg2, FILE_APPEND | LOCK_EX);
  }

  public static function logWarning(string $msg1, string $msg2) {
    $date = date("D M d, Y G:i");
    $myfile = file_put_contents(\ApplicationConfig::$logFile, "Warning" ."\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $date . "\t" . $msg1 . "\t" . $msg2, FILE_APPEND | LOCK_EX);
  }
}