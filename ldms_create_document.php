<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Create Document
  # Logic for document naming and moving
  #
  
  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # Functions
  
  # check fields
  function error_check($var){
    if($var == ""){
      error_handling("Bitte alle notwendigen Felder ausfüllen!");
    }
  }
  
  # break and go to base with message
  function error_handling($message){
    header("Location: " . BASE_PATH . '/?message=' . urlencode($message));
    die();
  }
  
  # HTTP Vars and error checks
  $event = $_REQUEST["event"];
  if($event == "create"){
    $file =           utf8_decode($_REQUEST["file"]);
    $direction =      strtoupper($_REQUEST["direction"]);
    $date =           $_REQUEST["date"];
    $number =         $_REQUEST["number"];
    $internalparty =  $_REQUEST["internalparty"];
    $mailtype =       strtoupper($_REQUEST["mailtype"]);
    $otherparty =     $_REQUEST["otherparty"];
    $labels =         explode(",",$_REQUEST["labels"]);
    $comment =        utf8_decode($_REQUEST["comment"]);
    $asana =          $_REQUEST["asana"];
    $asana_owner =    $_REQUEST["asana_owner"];
    $asana_follower = $_REQUEST["asana_follower"];
  }
    
  error_check($file);
  error_check($direction);
  error_check($date);
  error_check($number);
  error_check($internalparty);
  error_check($mailtype);
  error_check($otherparty);
  
  # Check if same already exists and in that case add numbers
  $index = 2;
  while(true){
    $sql_check = "
      SELECT * FROM documents
      WHERE date       = '".date_german2mysql($date)."'
        AND mailtype   = '$mailtype'
        AND direction  = '$direction'
        AND externalID = '$otherparty'
        AND internalID = '$internalparty'
        AND number     = '$number'
    ";
    $sql_check_result = $db->query($sql_check);
    if($sql_check_result->num_rows == 0){
      break;
    }else{
      if($index > 2){
        $number = substr($number, 0, -(strlen($index)+1));
      }
      $number .= "-".$index;
      $index++;
    }
  }
    
  # File name: YYMMDD_DIRECTION_MAILTYPE_externalID_internalID_nr.pdf
  # Example:   141217_IN_RE_123_intID_456.pdf
  #
  $new_file_name = "";
  
  # Date
  $new_file_name .= date_german2ldms($date);
  # Direction
  $new_file_name .= "_" . $direction;
  # Mailtype
  $new_file_name .= "_" . $mailtype;
  # External ID
  $new_file_name .= "_" . $otherparty;
  # Internal ID
  $new_file_name .= "_" . $internalparty;
  # Number
  $new_file_name .= "_" . $number;
  # .pdf
  $new_file_name .= ".pdf";
  
  # Create folders (INTERNALPARTY/YYYY/MM/MAILTYPE)
  $dir          = ARCHIVE_FOLDER .'/'. $internalparty;
  $dir_year     = $dir           .'/'. date_german2yearonly($date);
  $dir_month    = $dir_year      .'/'. date_german2monthonly($date);
  $dir_mailtype = $dir_month     .'/'. strtoupper($mailtype);
  
  
  if (!file_exists($dir)) {
    if(!mkdir($dir, 0777, true)){
      error_handling("Erstellen des 'internal party' Verzeichnisses schlug fehl!");
    }
  }
  if (!file_exists($dir_year)) {
    if(!mkdir($dir_year, 0777, true)){
      error_handling("Erstellen des Jahresverzeichnisses schlug fehl!");
    }
  }
  if (!file_exists($dir_month)) {
    if(!mkdir($dir_month, 0777, true)){
      error_handling("Erstellen des Monatsverzeichnisses schlug fehl!");
    }
  }
  if (!file_exists($dir_mailtype)) {
    if(!mkdir($dir_mailtype, 0777, true)){
      error_handling("Erstellen des Mailtype-Verzeichnisses schlug fehl!");
    }
  }
  
  # Move and rename
  $file_inbox_path   = INBOX_FOLDER  .'/'. $file;
  $file_archive_path = $dir_mailtype .'/'. $new_file_name;  
  if(!rename($file_inbox_path, $file_archive_path)){
    error_handling("Verschieben der Datei schlug fehl!");
  }
    
  # Labels DB
  foreach($labels AS $label){
    if($label != ""){
      $db->query("INSERT IGNORE INTO labels (label) VALUES ('$label')");
    }
  }
  # Document DB
  $sql = "
    INSERT INTO documents
    (date, mailtype, direction, externalID, internalID, number, comment)
    VALUES
    ('".date_german2mysql($date)."', '$mailtype', '$direction', '$otherparty', '$internalparty', '$number', '$comment');\n
  ";
  if(!$db->query($sql)){
    # move the file back
    if(!rename($file_archive_path, $file_inbox_path)){
      error_handling("Eintragen in die Datenbank und Zurückverschieben der Datei schlug fehl!<br>SQL Error: ".$db->error);
    }
    error_handling("Eintragen in die Datenbank schlug fehl! Datei noch im Posteingang.<br>SQL Error: ".$db->error);
  }
  
  # Index
  $insertid = $db->insert_id;
  
  # Document and Labels DB
  foreach($labels AS $label){
    if($label != ""){
      $db->query("INSERT INTO document_labels (document_id, label) VALUES (LAST_INSERT_ID(), '$label')");
    }
  }
  
  # ASANA (see http://developer.asana.com/documentation/)
  if($asana){

    # Document data
    $search_sql = "
      SELECT d.*, e.external_party, i.*, m.mailtype AS mt 
      FROM  documents d
      JOIN  external_parties e      ON (d.externalID  = e.id) 
      JOIN  internal_parties i      ON (d.internalID  = i.id) 
      JOIN  mailtypes m             ON (d.mailtype    = m.id)
      LEFT JOIN  document_labels dl ON (d.document_id = dl.document_id)
      WHERE d.document_id = '$insertid' 
    ";
    $search_result = $db->query($search_sql);
    $document = $search_result->fetch_object();
    
    if($document->direction == "IN"){
      $conj = "von";
    }else{
      $conj = "an";
    }
    # task name
    $taskname = 'LDMS '.$document->direction.' ('.date_mysql2german($document->date).'): '.$document->mt.' '.$conj.' '.$document->external_party;
    
    $filepath = 
      BASE_PATH .'/'. ARCHIVE_FOLDER_REL
      .'/'. $document->internal_party
      .'/'. date_german2yearonly(date_mysql2german($document->date))
      .'/'. date_german2monthonly(date_mysql2german($document->date))
      .'/'. $document->mailtype
      .'/'. date_german2ldms(date_mysql2german($document->date))
      .'_'. $document->direction
      .'_'. $document->mailtype
      .'_'. $document->externalID
      .'_'. $document->internalID
      .'_'. $document->number
      .'.pdf';
    # task notes  
    $tasknotes  = "Intern: ".$document->internal_party;
    $tasknotes .= "\n";
    $tasknotes .= "Kommentar: ".$document->comment;
    $tasknotes .= "\n\n";
    $tasknotes .= $filepath;
    
    # Asana implementation
    require_once(dirname(__FILE__) . '/php-asana-api/asana.php');

    # API key, workspace and project
    $asana = new Asana(array('apiKey' => ASANA_APIKEY));
    # workspace 'xxx'
    $workspaceId = $document->asana_workspace_id;
    # project 'xxx'
    $projectId   = $document->asana_project_id;
    
    # create the task
    $result = $asana->createTask(array(
      'workspace' => $workspaceId,
      'name'      => $taskname,
      'notes'     => $tasknotes,
      'assignee'  => $asana_owner,
      'followers' => $asana_follower
    ));

    // success is 201
    if ($asana->responseCode != '201' || is_null($result)) {
      $asana_message = 'Error while trying to connect to Asana, response code: ' . $asana->responseCode;
      $asana_error = true;
    }

    if(!$asana_error){
      $resultJson = json_decode($result);
      # get the task id
      $taskId = $resultJson->data->id;
      # add the task to the project
      $result = $asana->addProjectToTask($taskId, $projectId);

      if ($asana->responseCode != '200') {
        $asana_message = 'Error while assigning project to task: ' . $asana->responseCode;
        $asana_error = true;
      }
    }
    
    if(!$asana_error){
      $asana_message = "Asana Task angelegt!";
    }
  }
  
  # Success - no error ;)
  error_handling("Dokument erfolgreich abgelegt! ".$asana_message);

?>
