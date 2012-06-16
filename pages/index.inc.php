<?php
/**
 * fieldname_edit - XFORM Plugin
 *
 * @author http://rexdev.de
 * @package redaxo4.3
 */

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself    = 'fieldname_edit';
$myroot    = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself.'/';
$page      = rex_request('page', 'string');
$subpage   = rex_request('subpage', 'string');
$func      = rex_request('func', 'string');
$tablename = rex_request('tablename', 'string');
$oldname   = rex_request('oldname', 'string');
$newname   = rex_request('newname', 'string');

if($func=='savesettings')
{
  $db = new rex_sql;
  #$db->setDebug(true);
  $db->setQuery('ALTER TABLE `'.$tablename.'` CHANGE `'.$oldname.'` `'.$newname.'` TEXT;');
  $db->setQuery('UPDATE `rex_xform_field` SET `f1`=\''.$newname.'\' WHERE `f1`=\''.$oldname.'\' AND `table_name`=\''.$tablename.'\';');
}

// TITLE & SUBPAGE NAVIGATION
//////////////////////////////////////////////////////////////////////////////
rex_title('Fieldname Edit', $REX['ADDON'][$page]['SUBPAGES']);


// MAIN
////////////////////////////////////////////////////////////////////////////////

echo '
<div class="rex-addon-output">
  <div class="rex-form">

  <form action="index.php" method="POST" id="settings">
    <input type="hidden" name="page" value="xform" />
    <input type="hidden" name="subpage" value="'.$myself.'" />
    <input type="hidden" name="func" value="savesettings" />

        <fieldset class="rex-form-col-1">
          <legend>Tablemanager Fieldname Editor</legend>
          <div class="rex-form-wrapper">

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="textinput1">Table</label>
                <input id="textinput1" class="rex-form-text" type="text" name="tablename" value="'.$tablename.'" />
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="textinput1">Old Fieldname</label>
                <input id="textinput1" class="rex-form-text" type="text" name="oldname" value="'.$oldname.'" />
              </p>
            </div><!-- .rex-form-row -->

            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-text">
                <label for="textinput1">New Fieldname</label>
                <input id="textinput1" class="rex-form-text" type="text" name="newname" value="'.$newname.'" />
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
';
