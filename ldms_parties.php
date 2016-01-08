<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Parties
  # Logic for party management
  #
  
  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # HTTP Vars
  $event = $_REQUEST["event"];
  
  if($event == "new-party"){
    $party_name = utf8_decode($_REQUEST["party_name"]);
    $party_type = $_REQUEST["party_type"];
    $insert = true;
    # showing the table after inserting
    $event = $party_type;
  }
  
  if($event == "internal-parties"){
    $db_table = "internal_parties";
    $db_column = "internal_party";
  }else if($event == "external-parties"){
    $db_table = "external_parties";
    $db_column = "external_party";
  }
  
  # insert
  if($insert){
    $check_sql  = "
      SELECT * FROM $db_table
      WHERE  $db_column = '$party_name'
    ";
    $check_result = $db->query($check_sql);
    if($check_result->num_rows != 0){
      echo "Partei schon vorhanden!";
    }else{
      if($event == "internal-parties"){
        $shortname = strtolower(str_replace(" ", "-", $party_name));
        $insert_sql = "
          INSERT INTO $db_table (id, $db_column)
          VALUES ('$shortname', '$party_name')
        ";
      }else if($event == "external-parties"){
        $insert_sql = "
          INSERT INTO $db_table (id, $db_column)
          VALUES (uuid(), '$party_name')
        ";
      }
      if($db->query($insert_sql)){
        echo "Partei erfolgreich angelegt";
      }else{
        echo "Es ist ein Fehler beim Einfügen in die DB aufgetreten<br>";
        echo $insert_sql;
      }
    }
  }
  
  # DB SEARCH
  $party_sql = "SELECT * FROM $db_table ORDER BY $db_column";
  $party_result = $db->query($party_sql);
 
?>
<table class="tablesorter filter-table" id="party-result-table">
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
      <td><?php echo utf8_encode($party->$db_column) ?></td>
      <td>
        <?php
          $select_count = "SELECT COUNT(*) AS anz FROM documents WHERE ";
          if($event == "internal-parties"){
            $select_count .= " internalID ";
          }else if($event == "external-parties"){
            $select_count .= " externalID ";
          }
          $select_count .= " = '".$party->id."'";
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
    $("#party-result-table").tablesorter({widgets: ['zebra']});
    $('#party-result-table').filterTable();
  });
</script>