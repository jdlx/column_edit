<?php
/**
 * column_edit - XFORM Plugin
 *
 * @version 0.8.2
 * @author  http://rexdev.de
 * @package redaxo 4.3.x/4.4.x
 * @package xform 2.9.x
 */

$myself = 'column_edit';


// XFORM PLUING CHEKCKS
////////////////////////////////////////////////////////////////////////////////
if(!OOPlugin::isActivated('xform','manager'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Erst XFORM Tablemanager Plugin installieren & aktivieren!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


$REX['ADDON']['install'][$myself] = 1;
