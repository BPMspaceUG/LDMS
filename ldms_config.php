<?php 
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # LDMS Config
  # Config File
  #
  
  ##########################
  #    DO SETTINGS HERE    #
  ##########################

  # Base path
  # Example: "http://ldms:8080";
  $basepath = "http://ldms:8080";
  
  # Inbox folder (absolute path)
  # Default: "inbox"
  $inbox_folder_abs = dirname(__FILE__) . '/inbox';
  # Inbox folder (relative path from base)
  $inbox_folder_rel = './inbox';
  
  # Archive folder (absolute path)
  # Default: "archive"
  $archive_folder_abs = dirname(__FILE__) . '/archive';
  # Archive folder (relative path from base)
  $archive_folder_rel = './archive';
  
  # Ensure that PHP has rights in those directories!!
  
  
  # DB Server
  $db_server   = "localhost";
  # DB Name
  $db_database = "ldms";
  # DB User
  $db_user     = "";
  # DB Password
  $db_pass     = "";
  
  # Asana API Key
  $asana_api_key = "";
  
  # debug mode off/on
  error_reporting(0);
  
  ##########################
  #   STOP CHANGING HERE   #
  ##########################
  
  # Version
  define("VERSION","1.4");
  
  # INBOX_FOLDER
  define("INBOX_FOLDER",     $inbox_folder_abs);
  define("INBOX_FOLDER_REL", $inbox_folder_rel);
  
  # ARCHIVE FOLDER
  define("ARCHIVE_FOLDER",     $archive_folder_abs);
  define("ARCHIVE_FOLDER_REL", $archive_folder_rel);
  
  # Paths
  define("ROOT_PATH",   dirname(__FILE__));
  define("BASE_PATH",   $basepath);
  define("STYLE_PATH",  BASE_PATH.'/styles');
  define("JS_PATH",     BASE_PATH.'/js');
  define("IMAGES_PATH", BASE_PATH.'/images');
  
  # Names
  define("SHORT_NAME", "LDMS " . VERSION);
  define("LONG_NAME" , "LDMS " . VERSION . " - DOCUMENT MANAGEMENT");
  
  # Asana
  define("ASANA_APIKEY", $asana_api_key);
  
  # DB Connection
	$db = new mysqli($db_server, $db_user, $db_pass, $db_database);
  
  if ($db->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
  }
  
  require_once(dirname(__FILE__) . '/ldms_functions.php');
?>