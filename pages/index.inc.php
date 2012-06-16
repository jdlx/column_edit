<?php
/**
 * fieldname_edit - XFORM Plugin
 *
 * @author http://rexdev.de
 * @package redaxo4.3
 */

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself     = 'fieldname_edit';
$myroot     = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself.'/';
$page       = rex_request('page', 'string');
$subpage    = rex_request('subpage', 'string');
$func       = rex_request('func', 'string');
$table_name = rex_request('table_name', 'string');
$oldname    = rex_request('oldname', 'string');
$newname    = rex_request('newname', 'string');

$db = new rex_sql;
#$db->setDebug(true);

// EDIT FIELDNAME
if($func=='savesettings' && $table_name!='' && $oldname!='' && $newname!='')
{
  $db->setQuery('ALTER TABLE `'.$table_name.'` CHANGE `'.$oldname.'` `'.$newname.'` TEXT;');
  $db->setQuery('UPDATE `rex_xform_field` SET `f1`=\''.$newname.'\' WHERE `f1`=\''.$oldname.'\' AND `table_name`=\''.$table_name.'\';');
}

// TABLE SELECT
////////////////////////////////////////////////////////////////////////////////
$xtm_tables = $db->getArray('SELECT `table_name` FROM `rex_xform_table`;');

$sel = new rex_select();
$sel->setSize(1);
$sel->setName('table_name');
$sel->setAttribute('id','xtm_tables');
$sel->addOption('choose table','');
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

// TITLE & SUBPAGE NAVIGATION
//////////////////////////////////////////////////////////////////////////////
rex_title('Fieldname Edit', $REX['ADDON'][$page]['SUBPAGES']);


// MAIN
////////////////////////////////////////////////////////////////////////////////
?>

<div class="rex-addon-output">
  <div class="rex-form">

  <form action="index.php" method="POST" id="settings">
    <input type="hidden" name="page" value="xform" />
    <input type="hidden" name="subpage" value="fieldname_edit" />
    <input type="hidden" name="func" value="savesettings" />

        <fieldset class="rex-form-col-1">
          <legend>Tablemanager Fieldname Editor</legend>
          <div class="rex-form-wrapper">

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Table</label>
                <?php echo $table_select ?>
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-select">
                <label for="select">Field</label>
                <?php echo $field_select ?>
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="newname">New Name</label>
                <input id="newname" class="rex-form-text" type="text" name="newname" value="<?php echo $newname ?>" />
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row rex-form-element-v2">
              <p class="rex-form-submit">
                <input class="rex-form-submit" type="submit" id="submit" name="submit" value="Einstellungen speichern" />
              </p>
            </div><!-- .rex-form-row -->

          </div><!-- .rex-form-wrapper -->
        </fieldset>

  </form>

  </div><!-- .rex-form -->
</div><!-- .rex-addon-output -->

<script>
// GENERIC CALLBACK FUNC
function fieldname_edit_callback(data,success_func){
  return jQuery.ajax({
    url: '../index.php',
    type: 'POST',
    data: {
      test: 'test',
      fieldname_edit:JSON.stringify(data)
    },
    success: success_func,
    error: function(xhr, ajaxOptions, thrownError){
      console.log(xhr.status);
      console.log(thrownError);
    }
  });
};

// noConflict ONLOAD ///////////////////////////////////////////////////////////
jQuery(function($){

  $('#xtm_tables').change(function(){
    data = {};
    data.table_name = $(this).val();
    data.action = 'get-fieldnames';
    fieldname_edit_callback(data,function(ret){
      // console.log(ret);
      $('#xtm_fields').html(ret.html);
    });
  });

  $('#xtm_fields').change(function(){
    $('#newname').val($(this).val());
  });

});
</script>

