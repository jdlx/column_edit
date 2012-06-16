<?php
/**
 * fieldname_edit - XFORM Plugin
 *
 * @author http://rexdev.de
 * @package redaxo4.3
 */

$myself = 'fieldname_edit';


// XFORM PLUING CHEKCKS
////////////////////////////////////////////////////////////////////////////////
if(!OOPlugin::isActivated('xform','manager'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Erst XFORM Tablemanager Plugin installieren & aktivieren!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


$REX['ADDON']['install'][$myself] = 1;
