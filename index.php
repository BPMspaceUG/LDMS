<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Index
  # Main frame
  #
  
  require_once(dirname(__FILE__) . '/ldms_config.php');
      
  # HTTP Vars
  $message = $_REQUEST["message"];  
  
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo SHORT_NAME ?></title>
  <meta charset="utf-8" />
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,400,600' rel='stylesheet' type='text/css' />
  <link rel="stylesheet" href="<?php echo STYLE_PATH ?>/ldms_style.css" />
  <link rel="stylesheet" href="<?php echo JS_PATH ?>/chosen/chosen.css" />
  <link rel="stylesheet" href="<?php echo JS_PATH ?>/select2/select2.css" />
  <link rel="stylesheet" href="<?php echo JS_PATH ?>/datechooser/datechooser.css" />
  <link rel="stylesheet" href="<?php echo JS_PATH ?>/tablesorter/themes/blue/style.css">
  <link rel="icon" type="image/x-icon" href="<?php echo IMAGES_PATH ?>/ldms_favicon.ico" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="<?php echo JS_PATH ?>/chosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="<?php echo JS_PATH ?>/select2/select2.js" type="text/javascript"></script>
  <script src="<?php echo JS_PATH ?>/datechooser/datechooser.js" type="text/javascript"></script>
  <script src="<?php echo JS_PATH ?>/tablesorter/jquery.tablesorter.js" type="text/javascript"></script>
  <script src="<?php echo JS_PATH ?>/sunnywalker/jquery.filtertable.js" type="text/javascript"></script>
</head>

<body>
  <header>
    <img src="<?php echo IMAGES_PATH ?>/ldms_logo.png" alt="LDMS" style="float: right;" />
  </header>
  <article>
    <h1><?php echo LONG_NAME ?></h1>
    <ul class="menu-selection-list">
      <li class="menu-selection" id="new-document-menu"><a href="#new-document">Neues Dokument anlegen</a></li>
      <li class="menu-selection" id="search-document-menu"><a href="#search-document">Dokument suchen</a></li>
      <li class="menu-selection" id="party-management-menu"><a href="#party-management">Parteien verwalten</a></li>
      <li class="menu-selection" id="reports-menu"><a href="#reports">Reports</a></li>
    </ul><!-- /.menu-selection -->
    
    <?php if($message != ""){ ?>
    <div class="message">
      <?php echo $message ?>
    </div>
    <?php } ?>
    
    <div class="type-box" id="new-document">
      <h1>Neues Dokument anlegen</h1>
      <?php require(ROOT_PATH . '/ldms_new_document.php') ?>
    </div><!-- /#new-document -->
    
    <div class="type-box" id="search-document">
      <h1>Dokument suchen</h1>
      <?php require(ROOT_PATH . '/ldms_search_document.php') ?>
    </div><!-- /#search-document -->
    
    <div class="type-box" id="party-management">
      <h1>Parteien verwalten</h1>
      <?php require(ROOT_PATH . '/ldms_party_management.php') ?>
    </div><!-- /#party-management -->

    <div class="type-box" id="reports">
      <h1>Reports</h1>
      <?php require(ROOT_PATH . '/ldms_report_management.php') ?>
    </div><!-- /#reports -->
    
  </article>
  <footer>
    &copy <?php echo date("Y", time()) ?> <?php echo SHORT_NAME ?>
  </footer>
  <script>
    $(function(){
      $('.type-box').hide();
      if(location.hash == ''){
        location.hash = '#new-document';
      }     
      changeTab();
    });
    
    $(window).on('hashchange', function(){
      changeTab();
    });
    
    function changeTab(){
      var id     = location.hash;
      var menuid = id + '-menu';
      $('.menu-selection').removeClass('active');
      $('.type-box').hide();
      $(menuid).addClass('active');
      $(id).show();
    }
    
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Nichts gefunden!'},
      '.chosen-select-width'     : {width:"500px"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>
</body>
</html>