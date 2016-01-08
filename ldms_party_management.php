<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Party Management
  # Manage the parties
  #

  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # Scan and sort inbox folder
  $all_files = scandir(INBOX_FOLDER);
  
?>
<div class="selection-fields is-floating-left">
  <button class="party-button" id="external-parties" type="button">Alle externen Parteien anzeigen</button>
  <button class="party-button" id="internal-parties" type="button">Alle internen Parteien anzeigen</button>
  <br /><br />
  <h2>Neue Partei anlegen</h2>
  <table>
    <tr>
      <td>Name: </td>
      <td><input type="text" name="party_name" id="party-name" placeholder="Neue Partei" /></td>
    </tr>
    <tr>
      <td>Typ: </td>
      <td class="party_types">
        <input type="radio" name="party_type" value="internal-parties" id="party-type-in" /> <label for="party-type-in">intern</label>
        <input type="radio" name="party_type" value="external-parties" id="party-type-ex" checked="checked" /> <label for="party-type-ex">extern</label>
      </td>
    </tr>
  </table>
  <button class="party-button" id="new-party" type="button">Partei anlegen</button>
</div>
<div id="party-result-box" class="is-floating-left">
  <h2>Parteien</h2>
  <div id="party-results"></div>
</div>
<script>
  $('button.party-button').click(function() {
    $('#party-results').html('<img src="<?php echo IMAGES_PATH ?>/ajax-loader.gif" alt="loading..." />');
    $('#party-result-box').show();
    
    var url = '';
    var id = $(this).attr('id');
    
    if(id == 'new-party'){
      var name = encodeURIComponent($('#party-name').val());
      var type = encodeURIComponent($(".party_types input[type='radio']:checked").val());
      url = '<?php echo BASE_PATH ?>/ldms_parties.php'
          + '?event='+ id
          + '&party_name=' + name
          + '&party_type=' + type;
    }else{
      url = '<?php echo BASE_PATH ?>/ldms_parties.php?event='+$(this).attr('id');
    }
                     
    $.get(url, function(data) {
      $('#party-results').html(data);
      if(id == 'new-party'){
        location.reload();
      }
    });
  });
  
  $(function(){
    $('#party-result-box').hide();
  });
</script>