<?php
/**
 * column_edit - XFORM Plugin
 *
 * @version 1.1.0
 * @author  http://rexdev.de
 * @package redaxo 4.5.x/4.6.x
 * @package xform 2.9.x - 4.8.0
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
$REX['ADDON']['rxid'][$myself]        = '990';
$REX['ADDON']['version'][$myself]     = '1.1.0';
$REX['ADDON']['author'][$myself]      = 'rexdev.de';
$REX['ADDON']['supportpage'][$myself] = 'rexdev.de';
$REX['ADDON']['perm'][$myself]        = $myself.'[]';
$REX['PERM'][]                        = $myself.'[]';

// XFORM SUBPAGE
////////////////////////////////////////////////////////////////////////////////
if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[column_edit]"))){
  $REX['ADDON']['xform']['SUBPAGES'][] = array($myself , 'Column Edit');
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


// RECEIVER/API
////////////////////////////////////////////////////////////////////////////////
rex_register_extension('ADDONS_INCLUDED', function()
{
  $data = rex_request('column_edit','string',false);

  if($data!==false)
  {
    $data = json_decode(stripslashes($data),true);

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
        $column_options = array(); fb($db->getValue('Create Table'));
        $create_parts = explode(PHP_EOL,$db->getValue('Create Table'));
        foreach ($create_parts as $part) {
          preg_match('#^\s*`(\w+)`([^,`]+),$#',trim($part),$matches);
          if(count($matches)===3) {
            $column_options[$matches[1]] = trim($matches[2]);
          }
        }

        // NAME FIELD DEPENDING ON XFORM VERSION ..
        $name_field = version_compare(rex_addon::getVersion('xform'),'4.8','>=') ? 'name' : 'f1';

        // BUILD SELECT OPTIONS
        $select_options = '<option id="opt_"data-mysql-create-opts="" value="">Select Column:</option>';
        foreach($db->getArray('SELECT `'.$name_field.'` FROM `rex_xform_field` WHERE `table_name`=\''.$data['table_name'].'\';') as $k => $v)
        {
          $selected = (isset($data['selected_column']) && $data['selected_column']==$v[$name_field]) ? ' selected="selected"' : '' ;
          $select_options .= '<option id="opt_'.$v[$name_field].'" data-mysql-create-opts="'.$column_options[$v[$name_field]].'" value="'.$v[$name_field].'" '.$selected.'>'.$v[$name_field].'</option>';
        }
        column_edit_reply(array('html'=>$select_options));
      break;

      default:
        //
    }
  }


}); // rex_register_extension
