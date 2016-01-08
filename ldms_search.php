<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Create Document

  # Logic for document search
  #
  
  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # HTTP Vars
  $event = $_REQUEST["event"];
  if($event == "search"){
    $direction =      strtoupper($_REQUEST["direction"]);
    if($direction == "UNDEFINED"){
      $direction = "";
    }
    $date_from =      $_REQUEST["date_from"];
    $date_to =        $_REQUEST["date_to"];
    $number =         $_REQUEST["number"];
    $internalparty =  explode(",",$_REQUEST["internalparty"]);
    $mailtype =       explode(",",strtoupper($_REQUEST["mailtype"]));
    $otherparty =     explode(",",$_REQUEST["otherparty"]);
    $labels =         explode(",",$_REQUEST["labels"]);
    $comment =        $_REQUEST["comment"];
    $limit =          $_REQUEST["limit"];
    $showsql =        $_REQUEST["showsql"];
  }  
    
  # DB SEARCH
  $search_sql = "
    SELECT d.*, e.external_party, i.internal_party, m.mailtype AS mt 
    FROM  documents d
    JOIN  external_parties e      ON (d.externalID  = e.id) 
    JOIN  internal_parties i      ON (d.internalID  = i.id) 
    JOIN  mailtypes m             ON (d.mailtype    = m.id)
    LEFT JOIN  document_labels dl ON (d.document_id = dl.document_id)
    WHERE TRUE 
  ";
  
  # direction
  if($direction != ""){
    $search_sql .= " AND d.direction = '$direction' ";
  }
  
  # date from
  if($date_from != ""){
    $search_sql .= " AND d.date >= '".date_german2mysql($date_from)."' ";
  }
  
  # date to
  if($date_to != ""){
    $search_sql .= " AND d.date <= '".date_german2mysql($date_to)."' ";
  }
  
  # number
  if($number != ""){
    $search_sql .= " AND d.number = '$number'";
  }
  
  # internalparty
  if(!empty($internalparty[0])){
    $search_sql .= " AND (";
    $numItems = count($internalparty);
    $i = 0;
    foreach($internalparty as $party){
      $i++;
      $search_sql .= " d.internalID = '$party' ";
      if($i < $numItems) {
        $search_sql .= " OR ";
      }
    }
    $search_sql .= " ) ";
  }
  
  # otherparty
  if(!empty($otherparty[0])){
    $search_sql .= " AND (";
    $numItems = count($otherparty);
    $i = 0;
    foreach($otherparty as $party){
      $i++;
      $search_sql .= " d.externalID = '$party' ";
      if($i < $numItems) {
        $search_sql .= " OR ";
      }
    }
    $search_sql .= " ) ";
  }
  
  # mailtype
  if(!empty($mailtype[0])){
    $search_sql .= " AND (";
    $numItems = count($mailtype);
    $i = 0;
    foreach($mailtype as $mt){
      $i++;
      $search_sql .= " d.mailtype = '$mt' ";
      if($i < $numItems) {
        $search_sql .= " OR ";
      }
    }
    $search_sql .= " ) ";
  }
  
  # labels
  if(!empty($labels[0])){
    $search_sql .= " AND (";
    $numItems = count($labels);
    $i = 0;
    foreach($labels as $label){
      $i++;
      $search_sql .= " dl.label = '$label' ";
      if($i < $numItems) {
        $search_sql .= " OR ";
      }
    }
    $search_sql .= " ) ";
  }
  
  if($comment != ""){
    $search_sql .= " AND d.comment LIKE '%$comment%' ";
  }
  
  # group for case of more labels
  $search_sql .= " GROUP BY d.document_id ORDER BY d.date DESC ";
  
  # limit (string)
  if($limit == "true"){
    $search_sql .= " LIMIT 100 ";
  }
  
  $search_result = $db->query(utf8_decode($search_sql));
  if($search_result->num_rows == 0){
    $diestring = "<p>leider keine Ergebnisse für diese Suche...</p>";
    if($showsql == "true"){ // string!
      $diestring .= "<p>".$search_sql."</p><p>".$db->error."</p>";
    }
    die($diestring);
  }
?>
<table class="tablesorter" id="search-result-table">
  <thead>
    <tr>
      <th>Datum</th>
      <th>Mailtype</th>
      <th>Direction</th>
      <th>intern</th>
      <th>extern</th>
      <th>Nummer</th>
      <th>Kommentar</th>
      <th>Labels</th>
      <th>Datei</th>
    </tr>
  </thead>
  <tbody>
<?php while ($document = $search_result->fetch_object()){ ?>
    <tr>
      <td><?php echo date_mysql2german($document->date) ?></td>
      <td><?php echo utf8_encode($document->mt) ?></td>
      <td><?php echo utf8_encode($document->direction) ?></td>
      <td><?php echo utf8_encode($document->internal_party) ?></td>
      <td><?php echo utf8_encode($document->external_party) ?></td>
      <td><?php echo utf8_encode($document->number) ?></td>
      <td><?php echo utf8_encode($document->comment) ?></td>
      <td>
        <?php
          $label_string = "";
          $label_sql = "
            SELECT label FROM document_labels WHERE document_id = '".$document->document_id."'
          ";
          $label_result = $db->query($label_sql);
          while ($label = $label_result->fetch_object()){
            $label_string .= $label->label.", ";
          }
          # delete last comma and space
          $label_string = substr($label_string, 0, -2);
          echo $label_string;
        ?>
      </td>
      <td>
        <?php
          # build file path
          $filepath = 
            BASE_PATH .'/'. ARCHIVE_FOLDER_REL
            .'/'. $document->internalID
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
        
        ?>
        <a href="<?php echo $filepath ?>" title="Dokument öffnen" target="_blank"><img src="<?php echo IMAGES_PATH ?>/pdf-icon.png" alt="Dokument öffnen" /></a>
        &nbsp;&nbsp;&nbsp;
        <a title="URL kopieren" href="#" onclick="window.prompt('In die Zwischenablage kopieren: Strg+C, Enter', '<?php echo $filepath ?>');"><img src="<?php echo IMAGES_PATH ?>/copy-icon.png" alt="URL kopieren" /></a>
      </td>
    </tr>
<?php } ?>
  </tbody>
</table>
<?php if($showsql == "true"){ // string! ?>
<p><?php echo $search_sql ?></p>
<?php } ?>
<script>
  $(function(){
    $("#search-result-table").tablesorter({widgets: ['zebra']});
  });
</script>