<?php 
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # LDMS Functions
  #
  
  # Scanfunktion
  function ListIn($dir, $prefix = '') {
    $dir = rtrim($dir, '\\/');
    $result = array();

      foreach (scandir($dir) as $f) {
        # nicht archiv-server-184
        if ($f !== '.' and $f !== '..' and $f !== 'archiv-server-184') {
          if (is_dir("$dir/$f")) {
            $result = array_merge($result, ListIn("$dir/$f", "$prefix$f/"));
          } else {
            $file_info = pathinfo($f); 
            # nur PDF
            if($file_info['extension'] == 'pdf'){
              $result[] = utf8_encode($prefix.$f);
            }
          }
        }
      }

    return $result;
  }
  
  # YYMMDD -> YYYY-MM-DD
  function date_ldms2mysql($date){
    $y = substr($date, 0, 2);
    $m = substr($date, 2, 2);
    $d = substr($date, 4, 2);
    return '20'.$y.'-'.$m.'-'.$d;
  }
    
  # DD.MM.YYYY -> YYMMDD
  function date_german2ldms($date){
    list($day, $month, $year) = explode(".", $date);
    $year = substr($year, -2);
    return sprintf("%02d%02d%02d", $year, $month, $day);
  }
  
  # DD.MM.YYYY -> YY
  function date_german2yearonly($date){
    list($day, $month, $year) = explode(".", $date);
    return $year;
  }
  
  # DD.MM.YYYY -> MM
  function date_german2monthonly($date){
    list($day, $month, $year) = explode(".", $date);
    return $month;
  }
  
  # DD.MM.YYYY -> YYYY-MM-DD
  function date_german2mysql($date) { 
    list($day, $month, $year) = explode(".", $date); 
    return sprintf("%04d-%02d-%02d", $year, $month, $day); 
  }
  
  # YYYY-MM-DD -> DD.MM.YYYY
  function date_mysql2german($datum) { 
    list($jahr, $monat, $tag) = explode("-", $datum); 
    return sprintf("%02d.%02d.%04d", $tag, $monat, $jahr); 
  }
  
  # 1992 Ausschluss
  function date_ldms2year($date){
    return '20'.substr($date, 0, 2);
  }
  
?>