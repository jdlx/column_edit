<?php
/**
 * fieldname_edit - XFORM Plugin
 *
 * @author http://rexdev.de
 * @package redaxo4.3
 */


$myself           = 'fieldname_edit';
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

// XFORM SUBPAGE
////////////////////////////////////////////////////////////////////////////////
$REX['ADDON']['xform']['SUBPAGES'][] = array($myself , 'Fieldname Edit');


// ADD OWN XFORM CLASSES (DON'T INCLUDE DIR IF EMPTY)
////////////////////////////////////////////////////////////////////////////////
#$REX['ADDON']['xform']['classpaths']['value'][]    = $myroot.'/xform/classes/value/';
#$REX['ADDON']['xform']['classpaths']['validate'][] = $myroot.'/xform/classes/validate/';
#$REX['ADDON']['xform']['classpaths']['action'][]   = $myroot.'/xform/classes/action/';

// RECEIVER/API
////////////////////////////////////////////////////////////////////////////////
$data = rex_request('fieldname_edit','string',false);

if($data!==false)
{
  $data = json_decode(stripslashes($data),true);                                #FB::log($data,__CLASS__.'::'.__FUNCTION__.' $data');

  if(!is_array($data)) {
    return fieldname_edit_reply(array('error'=>'no valid POST data'));
  }

  switch ($data['action'])
  {
    case 'get-fieldnames':
      $db = new rex_sql;
      $options = '';
      foreach($db->getArray('SELECT `f1` FROM `rex_xform_field` WHERE `table_name`=\''.$data['table_name'].'\';') as $k => $v)
      {
        $options .= '<option value="'.$v['f1'].'">'.$v['f1'].'</option>';
      }
      fieldname_edit_reply(array('html'=>$options));
    break;

    default:
      //
  }
}

// GENERIC REPLY FUNC
////////////////////////////////////////////////////////////////////////////////
function fieldname_edit_reply($data=false)
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
