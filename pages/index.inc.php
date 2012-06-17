<?php
/**
 * column_edit - XFORM Plugin
 *
 * @author http://rexdev.de
 * @package redaxo 4.3
 * @package xform 2.9
 */

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself     = 'column_edit';
$myroot     = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself.'/';
$page       = rex_request('page', 'string');
$subpage    = rex_request('subpage', 'string');
$func       = rex_request('func', 'string');
$table_name = rex_request('table_name', 'string');
$oldname    = rex_request('oldname', 'string');
$newname    = rex_request('newname', 'string');
$columndef  = rex_request('columndef', 'string');

// TITLE & SUBPAGE NAVIGATION
//////////////////////////////////////////////////////////////////////////////
rex_title('Column Edit', $REX['ADDON'][$page]['SUBPAGES']);

$db = new rex_sql;
#$db->setDebug(true);

// EDIT FIELDNAME
if($func=='savesettings' && $table_name!='' && $oldname!='' && $newname!='' && $columndef!='')
{
  $alter_qry = 'ALTER TABLE `'.$table_name.'` CHANGE `'.$oldname.'` `'.$newname.'` '.stripslashes($columndef).';';
  $field_qry = 'UPDATE `rex_xform_field` SET `f1`=\''.$newname.'\' WHERE `f1`=\''.$oldname.'\' AND `table_name`=\''.$table_name.'\';';
  if($db->setQuery($alter_qry)) {
    echo rex_info('Sucessfull: '.$alter_qry);
    if($db->setQuery($field_qry)) {
      echo rex_info('Sucessfull: '.$field_qry);
    } else {
      echo rex_warning('"'.$field_qry.'"<br/>'.$db->getErrno().' - '.$db->getError());
    }
  } else {
    echo rex_warning('"'.$alter_qry.'"<br/>'.$db->getErrno().' - '.$db->getError());
  }
}

// TABLE SELECT
////////////////////////////////////////////////////////////////////////////////
$xtm_tables = $db->getArray('SELECT `table_name` FROM `rex_xform_table`;');

$sel = new rex_select();
$sel->setSize(1);
$sel->setName('table_name');
$sel->setAttribute('id','xtm_tables');
$sel->addOption('SELECT XFORM TABLE:','');
foreach($xtm_tables as $k => $v)
{
  $sel->addOption($v['table_name'],$v['table_name']);
}
$sel->setSelected($table_name);
$table_select = $sel->get();

// FIELD SELECT
////////////////////////////////////////////////////////////////////////////////
$sel = new rex_select();
$sel->setSize(1);
$sel->setName('oldname');
$sel->setAttribute('id','xtm_fields');
$sel->addOption('','');
$sel->setSelected($oldname);
$field_select = $sel->get();


// MAIN
////////////////////////////////////////////////////////////////////////////////
?>

<div class="rex-addon-output">
  <div class="rex-form">

  <form action="index.php" method="POST" id="settings">
    <input type="hidden" name="page" value="xform" />
    <input type="hidden" name="subpage" value="column_edit" />
    <input type="hidden" name="func" value="savesettings" />

        <fieldset class="rex-form-col-1">
          <legend>Tablemanager Tables/Columns</legend>
          <div class="rex-form-wrapper">

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Table</label>
                <?php echo $table_select ?>
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Column</label>
                <?php echo $field_select ?>
              </p>
            </div><!-- .rex-form-row -->

          </div><!-- .rex-form-wrapper -->
        </fieldset>

        <fieldset class="rex-form-col-1">
          <legend>Edit Column</legend>
          <div class="rex-form-wrapper">

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="newname">Name</label>
                <input id="newname" class="rex-form-text" type="text" name="newname" value="<?php echo $newname ?>" />
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="columndef">Definition</label>
                <input id="columndef" class="rex-form-text" type="text" name="columndef" value="<?php echo stripslashes($columndef) ?>" />
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row rex-form-element-v2">
              <p class="rex-form-submit">
                <input class="rex-form-submit" type="submit" id="submit" name="submit" value="Apply Changes" />
              </p>
            </div><!-- .rex-form-row -->

          </div><!-- .rex-form-wrapper -->
        </fieldset>

  </form>

  </div><!-- .rex-form -->
</div><!-- .rex-addon-output -->

<script>
// GENERIC CALLBACK FUNC
function column_edit_callback(data,success_func){
  return jQuery.ajax({
    url: '../index.php',
    type: 'POST',
    data: {
      column_edit:JSON.stringify(data)
    },
    success: success_func,
    error: function(xhr, ajaxOptions, thrownError){
      console.log(xhr.status);
      console.log(thrownError);
    }
  });
};

////////////////////////////////////////////////////////////////////////////////
(function($){ // jQuery noConflict /////////////////////////////////////////////

  $('#xtm_tables').change(function(){
    data = {};
    data.table_name = $(this).val();
    data.action = 'column-select-options';
    column_edit_callback(data,function(ret){
      $('#xtm_fields').html(ret.html);
      $('#newname').val('');
      $('#columndef').val('');
    });
  });

  $('#xtm_fields').change(function(){
    $('#newname').val($(this).val());
    $('#columndef').val($('#xtm_fields #opt_'+$(this).val()).attr('data-mysql-create-opts'));
  });

})(jQuery); // end jQuery noConflict ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
jQuery(function($){ // jQuery noConflict ONLOAD ////////////////////////////////

  if($('#xtm_tables').val()!=''){
    data = {};
    data.table_name = $('#xtm_tables').val();
    data.selected_column = '<?php echo $newname ?>';
    data.action = 'column-select-options';
    column_edit_callback(data,function(ret){
      $('#xtm_fields').html(ret.html);
    });
  }

}); // end jQuery noConflict ONLOAD ////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
</script>

