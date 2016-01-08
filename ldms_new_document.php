<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # New Document
  # View for new documents
  #

  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # Scan and sort inbox folder
  $all_files = scandir(INBOX_FOLDER);
  
?>
<div class="selection-fields is-floating-left">
  <form action="<?php echo BASE_PATH . '/ldms_create_document.php' ?>" method="post">
  <table>
    <tr>
      <td>Datei auswählen</td>
      <td>
        <select id="select_file" name="file" data-placeholder="Bitte Datei ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1">
          <option value=""></option>
          <?php
            foreach ($all_files as $file) {
              # File info for:
              # $file_info['filename']  = filename without type ending
              # $file_info['dirname']   = directory name
              # $file_info['extension'] = type ending
              # $file_info['basename']  = full filename with type ending
              $file_info = pathinfo(INBOX_FOLDER."/".$file); 
             
              # File size
              $size = ceil(filesize(INBOX_FOLDER."/".$file)/1024); 
             
              # Delete folders and Thumbs.db
              if ($file != "." && $file != ".." && $file != "Thumbs.db") { 
          ?>
            <option value="<?php echo utf8_encode($file_info['basename']) ?>">
              <?php echo utf8_encode($file_info['basename']) ?> (<?php echo strtoupper($file_info['extension']) ?> | <?php echo $size  ?> kB)
            </option>
          <?php
              }
            }
          ?> 
        </select>
      </td>
    </tr>
    <tr>
      <td>Direction</td>
      <td>
        <input type="radio" name="direction" id="direction-in" value="in" />   <label for="direction-in">Incoming</label>
        <input type="radio" name="direction" id="direction-out" value="out" /> <label for="direction-out">Outgoing</label>
      </td>
    </tr>
    <tr>
      <td>Datum</td>
      <td>
        <input type="text" name="date" size="17" value="<?php echo date("d.m.Y",time()); ?>" class="datechooser dc-dateformat='d.m.Y' dc-iconlink='<?php echo JS_PATH ?>/datechooser/cal.gif' dc-linkposition='left' dc-weekstartday='1' dc-startdate='<?php echo date("mdY",time());?>' dc-latestdate='<?php echo date("mdY",time());?>' dc-earliestdate='01012000'" readonly="readonly" style="height:23px;" />
      </td>
    </tr>
    <tr>
      <td>Number</td>
      <td><input type="text" name="number" value="online" size="20" style="height:23px;" /></td>
    </tr>
    <tr>
      <td>Internal party</td>
      <td>
        <select name="internalparty" data-placeholder="Bitte Partei ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1">
          <option value=""></option>
          <?php
            # read all internal parties
            $ip_read = "SELECT * FROM internal_parties ORDER BY internal_party";
            $ip_sql = $db->query($ip_read);
            while ($internal_party = $ip_sql->fetch_object()){
          ?>
          <option value="<?php echo $internal_party->id ?>"><?php echo utf8_encode($internal_party->internal_party) ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Mail type</td>
      <td>
        <select name="mailtype" data-placeholder="Bitte Mailtyp ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1">
          <option value=""></option>
          <?php
            # read all mailtypes
            $mt_read = "SELECT * FROM mailtypes ORDER BY mailtype";
            $mt_sql = $db->query($mt_read);
            while ($mailtype = $mt_sql->fetch_object()){
          ?>
          <option value="<?php echo $mailtype->id ?>"><?php echo utf8_encode($mailtype->mailtype) ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Other party</td>
      <td>
        <select name="otherparty" data-placeholder="Bitte Partei ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1">
          <option value=""></option>
          <?php
            # read all external (other) parties
            $ep_read = "SELECT * FROM external_parties ORDER BY external_party";
            $ep_sql = $db->query($ep_read);
            while ($external_party = $ep_sql->fetch_object()){
          ?>
          <option value="<?php echo $external_party->id ?>"><?php echo utf8_encode($external_party->external_party) ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Labels</td>
      <td>
        <input type="hidden" id="create-label-selection" name="labels" style="width:500px" />
      </td>
    </tr>
    <tr>
      <td>Kommentar</td>
      <td>
        <textarea name="comment" rows="4" cols="68"></textarea>
      </td>
    </tr>
    <tr>
      <td>Asana</td>
      <td><input type="checkbox" id="asana-checkbox" name="asana" /><label for="asana-checkbox"> Asana Task erstellen</label></td>
    </tr>
    <tr class="asana-relevant">
      <td>Verantwortlich</td>
      <td>
        <select name="asana_owner" data-placeholder="Einen User ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1">
          <option value=""></option>
          <?php
            # read all users
            $nu_read = "SELECT * FROM notify_users ORDER BY name";
            $nu_sql = $db->query($nu_read);
            while ($user = $nu_sql->fetch_object()){
          ?>
          <option value="<?php echo $user->mail ?>"><?php echo utf8_encode($user->name) ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr class="asana-relevant">
      <td>Follower</td>
      <td>
        <select name="asana_follower[]" data-placeholder="Mehrere User ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1" multiple="multiple">
          <option value=""></option>
          <?php
            # read all users
            $nu_read = "SELECT * FROM notify_users ORDER BY name";
            $nu_sql = $db->query($nu_read);
            while ($user = $nu_sql->fetch_object()){
          ?>
          <option value="<?php echo $user->mail ?>"><?php echo utf8_encode($user->name) ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
  </table>
  <br />
  <button type="submit" name="event" value="create">Dokument anlegen</button>
  </form>
</div>
<div id="pdf-preview" class="is-floating-left">
  <strong>PDF-Vorschau</strong><br />
  Zur Vorschau die entsprechende Datei auswählen
</div>
<script>
  
  $(function(){
    <?php
      # read all labels and build them as a string for select2
      $label_read = "SELECT * FROM labels ORDER BY label";
      $label_sql = $db->query($label_read);
      $label_string = "";
      while ($label = $label_sql->fetch_object()){
        # in '' and comma separated
        $label_string .= "'".$label->label."', ";
      }
      # delete last comma and space
      $label_string = substr($label_string, 0, -2);
    ?>
    $('#create-label-selection').select2({tags:[<?php echo $label_string ?>],tokenSeparators: [",", " "]});
    
    $('.asana-relevant').hide();
    $('#asana-checkbox').click(function() {
      if($('#asana-checkbox').is(':checked')){
        $('.asana-relevant').show();
      }else{
        $('.asana-relevant').hide();
      }
    });
  });
      
  $('#select_file').on('change', function(evt, params) {
    var filename = params.selected;
    var ifrm = document.createElement("iframe"); 
    ifrm.setAttribute("src", '<?php echo JS_PATH ?>/viewerJS/#../../<?php echo INBOX_FOLDER_REL ?>/' + filename); 
    ifrm.style.width  = 1024+"px"; 
    ifrm.style.height = 724+"px";
    
    $("#pdf-preview").html(ifrm);
  });
  
</script>