<?php
  # LDMS 1.4
  # January 29, 2015
  #
  # LDMS DOCUMENT MANAGEMENT SYSTEM
  #
  # Reports

  # Showing the reports
  #

  require_once(dirname(__FILE__) . '/ldms_config.php');
  
  # Scan and sort inbox folder
  $all_files = scandir(INBOX_FOLDER);
  
?>
<div class="selection-fields is-floating-left">
  <button class="report-button" id="name-duplicate" type="button">Externe Parteien mit gleichem Namen</button><br />
  <button class="report-button" id="name-4-letters" type="button">Externe Parteien mit 4 gleichen Anfangsbuchstaben</button><br />
  <button class="report-button" id="name-6-letters" type="button">Externe Parteien mit 6 gleichen Anfangsbuchstaben</button><br />
  <button class="report-button" id="name-10-letters" type="button">Externe Parteien mit 10 gleichen Anfangsbuchstaben</button><br />
  <br />
  <button class="report-button" id="db-and-archive" type="button">Archiv und DB Konsistenz (Geduld!)</button><br />
</div>
<div id="report-result-box" class="is-floating-left">
  <h2>Report Result</h2>
  <div id="report-results"></div>
</div>
<script>
  $('button.report-button').click(function() {
    $('#report-results').html('<img src="<?php echo IMAGES_PATH ?>/ajax-loader.gif" alt="loading..." />');
    $('#report-result-box').show();

    var url = '<?php echo BASE_PATH ?>/ldms_reports.php?report='+$(this).attr('id');
                     
    $.get(url, function(data) {
      $('#report-results').html(data);
    });
  });
  
  $(function(){
    $('#report-result-box').hide();
  });
</script>