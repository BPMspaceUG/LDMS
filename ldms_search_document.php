<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # New Document
  # Document search
  #

  require_once(dirname(__FILE__) . '/ldms_config.php');
  
?>
<div class="selection-fields is-floating-left">
  <table>
    <tr>
      <td>Direction</td>
      <td class="direction-radios">
        <input type="radio" name="search-direction" id="search-direction-in" value="in" />   <label for="search-direction-in">Incoming</label>
        <input type="radio" name="search-direction" id="search-direction-out" value="out" /> <label for="search-direction-out">Outgoing</label>
        <input type="radio" name="search-direction" id="search-direction-both" value="" /> <label for="search-direction-both">Both</label>
      </td>
    </tr>
    <tr>
      <td>Date from</td>
      <td>
        <input type="text" id="search-date-from" size="17" value="" class="datechooser dc-dateformat='d.m.Y' dc-iconlink='<?php echo JS_PATH ?>/datechooser/cal.gif' dc-linkposition='left' dc-weekstartday='1' dc-startdate='<?php echo date("mdY",time());?>' dc-latestdate='<?php echo date("mdY",time());?>' dc-earliestdate='01012000'" style="height:23px;" />
      </td>
    </tr>
    <tr>
      <td>Date to</td>
      <td>
        <input type="text" id="search-date-to" size="17" value="" class="datechooser dc-dateformat='d.m.Y' dc-iconlink='<?php echo JS_PATH ?>/datechooser/cal.gif' dc-linkposition='left' dc-weekstartday='1' dc-startdate='<?php echo date("mdY",time());?>' dc-latestdate='<?php echo date("mdY",time());?>' dc-earliestdate='01012000'" style="height:23px;" />
      </td>
    </tr>
    <tr>
      <td>Number</td>
      <td><input type="text" id="search-number" size="20" style="height:23px;" /></td>
    </tr>
    <tr>
      <td>Internal party</td>
      <td>
        <select id="search-internalparty" data-placeholder="Bitte Partei ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1" multiple="multiple">
          <option value=""></option>
          <?php
            # read all internal parties
            $ip_read = "SELECT * FROM internal_parties ORDER BY internal_party";
            $ip_sql = $db->query($ip_read);
            while ($internal_party = $ip_sql->fetch_object()){
          ?>
          <option value="<?php echo $internal_party->id ?>"><?php echo $internal_party->internal_party ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Mail type</td>
      <td>
        <select id="search-mailtype" data-placeholder="Bitte Mailtyp ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1" multiple="multiple">
          <option value=""></option>
          <?php
            # read all mailtypes
            $mt_read = "SELECT * FROM mailtypes ORDER BY mailtype";
            $mt_sql = $db->query($mt_read);
            while ($mailtype = $mt_sql->fetch_object()){
          ?>
          <option value="<?php echo $mailtype->id ?>"><?php echo $mailtype->mailtype ?></option>
          <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Other party</td>
      <td>
        <select id="search-otherparty" data-placeholder="Bitte Partei ausw&auml;hlen..." class="chosen-select" style="width:500px;" tabindex="1" multiple="multiple">
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
        <input type="hidden" id="search-label-selection" style="width:500px;" />
      </td>
    </tr>
    <tr>
      <td>Comment</td>
      <td>
        <input type="text" id="search-comment" style="width:496px;height:23px;"></textarea>
      </td>
    </tr>
    <tr>
      <td>Limit 100</td>
      <td>
        <input type="checkbox" id="search-limit" checked="checked" />
      </td>
    </tr>
    <tr>
      <td>SQL anzeigen</td>
      <td>
        <input type="checkbox" id="search-show-sql" />
      </td>
    </tr>
  </table>
  <button id="search-button" type="button">Dokument(e) suchen</button>
</div>
<div id="search-result-box" class="is-floating-left">
  <h2>Suchergebnisse</h2>
  <div id="search-results"></div>
</div>
<script>
  
  $('button#search-button').click(function() {
    $('#search-results').html('<img src="<?php echo IMAGES_PATH ?>/ajax-loader.gif" alt="loading..." />');
    $('#search-result-box').show();
  
    var direction  = encodeURIComponent($(".direction-radios input[type='radio']:checked").val());
    var date_from  = encodeURIComponent($('#search-date-from').val());
    var date_to    = encodeURIComponent($('#search-date-to').val());
    var number     = encodeURIComponent($('#search-number').val());
    var internalID = encodeURIComponent($('#search-internalparty').val() || [].join( "," ));
    var externalID = encodeURIComponent($('#search-otherparty').val() || [].join( "," ));
    var mailtype   = encodeURIComponent($('#search-mailtype').val() || [].join( "," ));
    var labels     = encodeURIComponent($('#search-label-selection').val());
    var comment    = encodeURIComponent($('#search-comment').val());
    var limit      = encodeURIComponent($('#search-limit').is(':checked'));
    var showsql    = encodeURIComponent($('#search-show-sql').is(':checked'));
    var event      = 'search';
    
    var url  = '<?php echo BASE_PATH ?>/ldms_search.php'
             + '?event='+event
             + '&direction='+direction
             + '&date_from='+date_from
             + '&date_to='+date_to
             + '&number='+number
             + '&internalparty='+internalID
             + '&mailtype='+mailtype
             + '&otherparty='+externalID
             + '&labels='+labels
             + '&comment='+comment
             + '&limit='+limit
             + '&showsql='+showsql;
             
    console.log(url);
                              
    $.get(url, function(data) {
      $('#search-results').html(data);
    });
  });
  
  
  $(function(){
    $('#search-result-box').hide();
    
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
    // TODP
    $('#search-label-selection').select2({tags:[<?php echo $label_string ?>],tokenSeparators: [",", " "]});
  });
</script>