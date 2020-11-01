<?php
namespace App\Libraries;

class StringHelper {
  //Breaks a string into a array of words
  public static function WordsExplode(String $string) {
    preg_match_all(
      '/[[:word:]]+/',
      $string,
      $matches,
      PREG_SET_ORDER
    );

    return $matches;
  }
}