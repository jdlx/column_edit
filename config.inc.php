<?php
/**
 * column_edit - XFORM Plugin
 *
 * @version 0.8.2
 * @author  http://rexdev.de
 * @package redaxo 4.3.x/4.4.x
 * @package xform 2.9.x
 */

if(!$REX['REDAXO']) {
  return;
}


$myself           = 'column_edit';
$myroot           = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself;
$page             = rex_request('page'            , 'string'          );
$subpage          = rex_request('subpage'         , 'string'          );
$tripage          = rex_request('tripage'         , 'string'          );
$table_name       = rex_request('table_name'      , 'string'          );
$start            = rex_request('start'           , 'int'    , 0      );
$sort             = rex_request('sort'            , 'string' , 'false');
$rex_xform_search = rex_request('rex_xform_search', 'int'    , 0      );
$list             = rex_request('list'            , 'string' , 'false');
$send             = rex_request('send'            , 'string' , 'false');
$func             = rex_request('func'            , 'string' , 'false');


// ADDON REX COMMONS
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['rxid'][$myself] = '990';
$REX['ADDON'][$myself]['VERSION'] = array
(
'VERSION'      => 0,
'MINORVERSION' => 8,
'SUBVERSION'   => 2,
);
$REX['ADDON']['version'][$myself]     = implode('.', $REX['ADDON'][$myself]['VERSION']);
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';

// XFORM SUBPAGE
////////////////////////////////////////////////////////////////////////////////
if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[column_edit]"))){
  $REX['ADDON']['xform']['SUBPAGES'][] = array($myself , 'Column Edit');
}


// ADD OWN XFORM CLASSES (DON'T INCLUDE DIR IF EMPTY)
////////////////////////////////////////////////////////////////////////////////
#$REX['ADDON']['xform']['classpaths']['value'][]    = $myroot.'/xform/classes/value/';
#$REX['ADDON']['xform']['classpaths']['validate'][] = $myroot.'/xform/classes/validate/';
#$REX['ADDON']['xform']['classpaths']['action'][]   = $myroot.'/xform/classes/action/';

// RECEIVER/API
////////////////////////////////////////////////////////////////////////////////
$data = rex_request('column_edit','string',false);

if($data!==false)
{
  $data = json_decode(stripslashes($data),true);                                #FB::log($data,__CLASS__.'::'.__FUNCTION__.' $data');

  if(!is_array($data)) {
    return column_edit_reply(array('error'=>'no valid POST data'));
  }

  switch ($data['action'])
  {
    case 'column-select-options':
      if($data['table_name']=='') {
        column_edit_reply(array('html'=>''));
      }

      $db = new rex_sql;

      // GET COLUMN DEFINITIONS
      $db->setQuery('SHOW CREATE TABLE `'.$data['table_name'].'`;');
      $column_options = array();
      $create_parts = explode("\n",$db->getValue('Create Table'));              #FB::log($create_parts,__CLASS__.'::'.__FUNCTION__.' $create_parts');
      foreach ($create_parts as $part) {
        preg_match('#`(\w+)`([^,`]+),#',trim($part),$matches);                  #FB::log($matches,__CLASS__.'::'.__FUNCTION__.' $matches');
        if(count($matches)===3) {
          $column_options[$matches[1]] = trim($matches[2]);
        }
      }                                                                         #FB::log($column_options,__CLASS__.'::'.__FUNCTION__.' $column_options');

      // BUILD SELECT OPTIONS
      $select_options = '<option id="opt_"data-mysql-create-opts="" value="">Select Column:</option>';
      foreach($db->getArray('SELECT `f1` FROM `rex_xform_field` WHERE `table_name`=\''.$data['table_name'].'\';') as $k => $v)
      {
        $selected = (isset($data['selected_column']) && $data['selected_column']==$v['f1']) ? ' selected="selected"' : '' ;
        $select_options .= '<option id="opt_'.$v['f1'].'" data-mysql-create-opts="'.$column_options[$v['f1']].'" value="'.$v['f1'].'" '.$selected.'>'.$v['f1'].'</option>';
      }
      column_edit_reply(array('html'=>$select_options));
    break;

    default:
      //
  }
}

// GENERIC REPLY FUNC
////////////////////////////////////////////////////////////////////////////////
function column_edit_reply($data=false)
{
  if(!$data)
    return false;

  if(is_array($data) && count($data)>0)
  {
    while(ob_get_level()){
      ob_end_clean();
    }
    ob_start();
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
  }
}
