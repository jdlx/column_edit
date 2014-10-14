<?php
/**
 * column_edit - XFORM Plugin
 *
 * @version 1.1.0
 * @author  http://rexdev.de
 * @package redaxo 4.5.x/4.6.x
 * @package xform 2.9.x - 4.8.0
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
