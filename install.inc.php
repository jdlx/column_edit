<?php
/**
 * column_edit - XFORM Plugin
 *
 * @version 0.8.1
 * @author http://rexdev.de
 * @package redaxo 4.3
 * @package xform 2.9
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
