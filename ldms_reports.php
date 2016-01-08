<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Reports
  # Logic for reports
  #
  
  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # HTTP Vars
  $report = $_REQUEST["report"];
  
  if($report == "name-duplicate"){
    $report_type = "party";
    $h3 = "Externe Parteien mit gleichem Namen";
    $party_sql = "
      SELECT id, external_party
      FROM external_parties e
      WHERE EXISTS (
        SELECT id FROM external_parties ed WHERE e.external_party = ed.external_party AND e.id <> ed.id
      )
      ORDER BY external_party;
    ";
  }
  
  if($report == "name-4-letters"){
    $report_type = "party";
    $h3 = "Externe Parteien mit 4 gleichen Anfangsbuchstaben";
    $party_sql = "
      SELECT e.id, e.external_party
      FROM external_parties e
      WHERE EXISTS (
        SELECT id FROM external_parties ed WHERE LEFT(e.external_party,4) = LEFT(ed.external_party,4) AND e.id <> ed.id
      )
      ORDER BY external_party;
    ";
  }
      
  if($report == "name-6-letters"){
    $report_type = "party";
    $h3 = "Externe Parteien mit 6 gleichen Anfangsbuchstaben";
    $party_sql = "
      SELECT e.id, e.external_party
      FROM external_parties e
      WHERE EXISTS (
        SELECT id FROM external_parties ed WHERE LEFT(e.external_party,6) = LEFT(ed.external_party,6) AND e.id <> ed.id
      )
      ORDER BY external_party;
    ";
  }
  
  if($report == "name-10-letters"){
    $report_type = "party";
    $h3 = "Externe Parteien mit 10 gleichen Anfangsbuchstaben";
    $party_sql = "
      SELECT e.id, e.external_party
      FROM external_parties e
      WHERE EXISTS (
        SELECT id FROM external_parties ed WHERE LEFT(e.external_party,10) = LEFT(ed.external_party,10) AND e.id <> ed.id
      )
      ORDER BY external_party;
    ";
  }
  
  if($report_type == "party"){
    # DB SEARCH
    $party_result = $db->query($party_sql);
    $h3 .= " (".$party_result->num_rows." Zeilen)";
  }
  
  if($report == "db-and-archive"){
    $report_type = "files";
    $h3 = "Archiv und DB Konsistenz";
    # scan archive
    $all_existing_files = ListIn(ARCHIVE_FOLDER);
    
    # count things in db
    $doc_count_sql     = "SELECT COUNT(*) AS c FROM documents";
    $doc_count_query   = $db->query($doc_count_sql);
    $doc_count         = $doc_count_query->fetch_object();
    $doc_counted       = $doc_count->c;
    
    $ep_count_sql      = "SELECT COUNT(*) AS c FROM external_parties";
    $ep_count_query    = $db->query($ep_count_sql);
    $ep_count          = $ep_count_query->fetch_object();
    $ep_counted        = $ep_count->c;
    
    $epdoc_count_sql   = "SELECT * FROM documents GROUP by externalID";
    $epdoc_count_query = $db->query($epdoc_count_sql);
    $epdoc_counted     = $epdoc_count_query->num_rows;
        
    # read DB
    $read = "
      SELECT d.*, e.external_party, i.internal_party, m.mailtype AS mt 
      FROM  documents d
      JOIN  external_parties e      ON (d.externalID  = e.id) 
      JOIN  internal_parties i      ON (d.internalID  = i.id) 
      JOIN  mailtypes m             ON (d.mailtype    = m.id)
      LEFT JOIN  document_labels dl ON (d.document_id = dl.document_id)
      WHERE TRUE 
    ";
    $sql = $db->query($read);

    # search db entries in files
    $db_dokumente = array();
    $treffer_archive = array();
    $fehler_archive  = array();
    while ($document = $sql->fetch_object()){
    
      $filepath = 
              $document->internalID
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
        
        $db_dokumente[$document->document_id] = $filepath;
      
      if(in_array($filepath, $all_existing_files)){
        $treffer_archive[$document->document_id] = $filepath;
      }else{
        $fehler_archive[$document->document_id] = $filepath;
      }
    }
    
    # search files in db
    $treffer_db = array();
    $fehler_db  = array();
    foreach($all_existing_files AS $existing_file){
      if(in_array($existing_file, $db_dokumente)){
        $treffer_db[] = $existing_file;
      }else{
        $fehler_db[] = $existing_file;
      }
    }
    
    # Duplikate in DB
    $unique = array_unique($treffer);
    $unique_assoc = array_diff_assoc($treffer, $unique);
  
  }
  
?>
<h3><?php echo $h3 ?></h3>
<?php if($report_type == "party"){ ?>
  <table class="tablesorter" id="report-result-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Party</th>
        <th>#</th>
      </tr>
    </thead>
    <tbody>
  <?php while ($party = $party_result->fetch_object()){ ?>
      <tr>
        <td><?php echo $party->id ?></td>
        <td><?php echo utf8_encode($party->external_party) ?></td>
        <td>
          <?php
            $select_count = "
              SELECT COUNT(*) AS anz 
              FROM documents 
              WHERE externalID  = '".$party->id."'";
            $count_result = $db->query($select_count);
            $count = $count_result->fetch_object();
            echo $count->anz;
          ?>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
  <script>
    $(function(){
      $("#report-result-table").tablesorter({widgets: ['zebra']});
    });
  </script>
<?php } ?>

<?php if($report_type == "files"){ ?>
  <table>
    <tr>
      <td>Dokumente in der DB</td>
      <td><?php echo $doc_counted ?></td>
    </tr>
    <tr>
      <td>PDF Dokumente im Archiv</td>
      <td><?php echo count($all_existing_files) ?></td>
    </tr>
    <tr>
      <td>Gefundene DB Einträge im Archiv</td>
      <td><?php echo count($treffer_archive) ?></td>
    </tr>
    <tr>
      <td>Gefundene Dokumente in der DB</td>
      <td><?php echo count($treffer_db) ?></td>
    </tr>
    <tr>
      <td>Nicht gefunden bei der Suche im Archiv</td>
      <td><?php echo count($fehler_archive) ?></td>
    </tr>
    <tr>
      <td>Nicht gefunden bei der Suche in der DB</td>
      <td><?php echo count($fehler_db) ?></td>
    </tr>
    <tr>
      <td>Duplikate in der DB</td>
      <td><?php echo count($unique_assoc) ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Externe Parteien in der DB</td>
      <td><?php echo $ep_counted ?></td>
    </tr>
    <tr>
      <td>Externe Parteien mit Dokumenten</td>
      <td><?php echo $epdoc_counted ?></td>
    </tr>
    <tr>
      <td>Externe Parteien ohne Dokumente</td>
      <td><?php echo $ep_counted - $epdoc_counted ?></td>
    </tr>
  </table>
<?php } ?>