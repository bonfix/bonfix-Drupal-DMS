<?php

define('NID_personnel', 41);
define('NID_contract', 28);
define('NID_position', 31);
define('NID_leave_balance', 102);
define('KEY_secs_per_day', 86400);
define('NID_telephone', 124);
define('NID_duty_station', 30);
define('NID_holiday', 32);

class Util
{


// public static function get_ssa_leave_balance_addition_valid()
// {
//   $indices = array();
//  //Done only at the end of the month - cron at 1st day of each month at 0000hrs
//   if(date('d') != 1)
//   {
//     return $indices;
//   }
//    $records = Util::get_record_from_db(NID_contract, 9, 'cid', false);
//     //d($records, true);
//     foreach ($records as $record)
//       {
//         if($record[2] == 'SSA')
//         {
//           $nte = Util::toTime($record[9]);
//           $eod = Util::toTime($record[19]);
//           $after6 = strtoTime('+6 Months', $eod);
//            $now = strtoTime('now');
//           //contract valid?
//           //contract more than 6 months?
//           if($nte >= $after6 && $nte >= $now)
//           {
//              $indices[] = $record[18];
//           }

//         }
        
//       }

//   return $indices;
// }

public static function get_sc_leave_balance_addition_valid()
{
  $indices = array();
  $days = array(
    4,10,16,22,28
    );
 //Done only at $days - 4,10,16,22,28
  $date = date('d');
  if(!in_array($date, $dates))
  {
    return $indices;
  }
   $records = Util::get_record_from_db(NID_contract, 9, 'cid', false);
    //d($records, true);
    foreach ($records as $record)
      {
        $contractType = $record[2];
        if($contractType == 'FT' || $contractType == 'CONT' || $contractType == 'SC')
        {
          $nte = Util::toTime($record[9]);
          $eod = Util::toTime($record[19]);
          $after6 = strtoTime('+6 Months', $eod);
          $now = strtoTime('now');
          //contract valid?
          if($nte >= $now)
          {
             $indices[] = $record[18];
          }

        }
        
      }

  return $indices;
}

public static function get_ssa_leave_balance_addition_valid()
  {
    //check date
    $indices = array();
    $days = array(
      1
      );
   //Done only at $days - 4,10,16,22,28
    $date = date('d');
    if(!in_array($date, $dates))
    {
      return $indices;
    }

    $records = Util::get_record_from_db(NID_contract,  'SSA', 'data', false);
    //d($records, true);

    //check for records valid for SSA leave increment
    /*
      "Up to one (1) day per month, for those hired for at least six
    months’ duration, or a combination of successive contracts exceeding six
    months. For contract periods shorter than six months, the annual leave will be effective starting with the seventh month if further extension is granted beyond
    six months."

    */

    //get contracts more than 6 months and not expired
    $data = array();
    foreach ($records as $record) {
      $nte = Util::toTime($record[9]);
      $nte = Util::toTime('+24 hours', $nte); //emp works on the last day of nte
      $eod = Util::toTime($record[19]);
      $theDate = strtoTime('+6 Months', $eod);
      $now = strtoTime("now");
      if($nte >= $theDate && $nte > $now) //more than 6 months
      {
        $data[] = $record[18];
      }
    }
    // d($data,true);
    return $data;
  }


public static function get_perf_eval_reminders()
  {
    $records = Util::get_record_from_db(NID_contract, 9, 'cid', false);
    //d($records, true);
    $due = array();
    foreach ($records as $record)
      {
        $nte = $record[9];
        $nte = Util::toTime($nte);
        
        
        if ($record[2] == "SC")
          {
            $theDate = strtoTime('+2 Months');
            $diff    = floor(abs($nte - $theDate) / KEY_secs_per_day);
            if ($diff == 0)
              {
                $due[] = $record[18]; //save index number
              }
          }
        else if ($record[2] == "SSA")
          {
            $theDate = strtoTime('+1 Months');
            $diff    = floor(abs($nte - $theDate) / KEY_secs_per_day);
            if ($diff == 0)
              {
                $due[] = $record[18]; //save index number
              }
          }
        
      }
    
    return $due;
    
  }



public static function get_record_from_db($nid, $ref_data = null, $ref_field = 'data', $one = true)
  {
    // db query to get all sids (submitted webforms) present in nid = '12345' (put your webform's nid here instead of 12345)
    $data = array();
    
    // get the submission id of that perticular record
    $select = db_select('webform_submitted_data', 't');
    $select->fields('t', array(
        'nid',
        'sid',
        'cid',
        'data'
    ));
    
    $select->condition('t.nid', $nid);
    if ($ref_data)
      {
        $select->condition('t.' . $ref_field, $ref_data);
      }
    $select->orderBy('t.sid', 'DESC');
    
    // $select->groupBy('data');
    
    // Now, loop all these entries and show them in a table. Note that there is no
    // db_fetch_* object or array public static function being called here. Also note that the
    // following line could have been written as
    // $entries = $select->execute()->fetchAll() which would return each selected
    // record as an object instead of an array.
    $res = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
    // d($entries);
    if (empty($res))
      {
        return $data;
      }
    foreach ($res as $entry)
      {
        # code...
        //read record
        $sid = $entry['sid'];
        
        $select = db_select('webform_submitted_data', 't');
        $select->fields('t', array(
            'nid',
            'sid',
            'cid',
            'data'
        ));
        
        $select->condition('t.sid', $sid);
        
        $entries = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
        // d($entries);
        // if (!empty($entries)) {
        //   $rows = array();
        $record  = array();
        $record[0] = $sid;
        foreach ($entries as $entry)
          {
            $record[$entry['cid']] = $entry['data'];
          }
        
        if ($one)
          {
            return $record;
          }
        else
          {
            $data[] = $record;
          }
        
      }
    
    //   d($rows);
    
    return $data;
  }

public static function get_records_from_db($nid, $ref_data = null, $ref_field = 'data')
  {
    // db query to get all sids (submitted webforms) present in nid = '12345' (put your webform's nid here instead of 12345)
    $results = array();
    
    // get the submission id of that perticular record
    $select = db_select('webform_submitted_data', 't');
    $select->fields('t', array(
        'nid',
        'sid',
        'cid',
        'data'
    ));
    
    $select->condition('t.nid', $nid);
    if (ref_data)
      {
        $select->condition('t.' . $ref_field, $ref_data);
      }
    
    // $select->groupBy('data');
    
    // Now, loop all these entries and show them in a table. Note that there is no
    // db_fetch_* object or array public static function being called here. Also note that the
    // following line could have been written as
    // $entries = $select->execute()->fetchAll() which would return each selected
    // record as an object instead of an array.
    $entries = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
    // d($entries);
    if (empty($entries))
      {
        return $results;
      }
    
    //read record
    $sid = $entries[0]['sid'];
    
    $select = db_select('webform_submitted_data', 't');
    $select->fields('t', array(
        'nid',
        'sid',
        'cid',
        'data'
    ));
    
    $select->condition('t.sid', $sid);
    
    $entries = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
    // d($entries);
    // if (!empty($entries)) {
    //   $rows = array();
    $data    = array();
    foreach ($entries as $entry)
      {
        $data[$entry['cid']] = $entry['data'];
      }
    
    //   d($rows);
    
    return $data;
  }



public static function get_data_basic_details($index)
  {
    // db query to get all sids (submitted webforms) present in nid = '12345' (put your webform's nid here instead of 12345)
    //
    // get_perf_eval_reminders();
    
    $person   = Util::get_record_from_db(NID_personnel, $index);
    $contract =  Util::get_record_from_db(NID_contract, $index);
    $position =  Util::get_record_from_db(NID_position, $person[6]);
    
    //   d($rows);
    $data = array(
        'person' => $person,
        'contract' => $contract,
        'position' => $position
    );
    
    return $data;
  }



// public static function get_data_basic_details($index)
// {
//       // db query to get all sids (submitted webforms) present in nid = '12345' (put your webform's nid here instead of 12345)
//     //
//     $results = array();

//      // get the submission id of that perticular record
//     $select = db_select('webform_submitted_data', 't');
//     $select->fields('t', array('nid', 'sid', 'cid', 'data'));

//     $select->condition('t.nid', NID_personnel);
//     $select->condition('t.data', $index);

//  // $select->groupBy('data');

// // Now, loop all these entries and show them in a table. Note that there is no
//   // db_fetch_* object or array public static function being called here. Also note that the
//   // following line could have been written as
//   // $entries = $select->execute()->fetchAll() which would return each selected
//   // record as an object instead of an array.
//     $entries = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
//   // d($entries);
//     if (empty($entries)) {
//         return $results;
//     }

//   //read records
//     $sid = $entries[0]['sid'];

//     $select = db_select('webform_submitted_data', 't');
//     $select->fields('t', array('nid', 'sid', 'cid', 'data'));

//    // $select->condition('t.nid', NID_personnel);
//    // $select->condition('t.data', $index);
//     $select->condition('t.sid', $sid);

//     $entries = $select->execute()->fetchAll(PDO::FETCH_ASSOC);
//    // d($entries);
//   // if (!empty($entries)) {
//   //   $rows = array();
//     $data = array();
//     foreach ($entries as $entry) {
//         $data[$entry['cid']] = $entry['data'];
//     }

//   //   d($rows);

//     return $data;
// }


/*public static function to output debug code*/
public static function d($var, $is_die = FALSE)
  {
    print '<pre><br>***************************** *****************************<br>';
    // print '<br>***************************** *****************************<br>';
    Util::d_($var);
    print '<br>***************************** **********************<br></pre>';
    // print '<br>***************************** **********************<br>';
    if ($is_die)
      {
        die();
      }
    
  }


/*public static function to output debug code*/
public static function d_($var, &$spaces_count = 0)
  {
    if(is_object($var))
    {
      $var =  (array) $var;
      //json_decode(json_encode($nested_object), true);
    }
    if (is_array($var) or ($var instanceof Traversable))
      {
        Util::d__('<br>');
        Util::d__('[', $spaces_count, true);
        //d__($spaces_count . ' => ', $spaces_count);
        $spaces_count += 4;
        
        //print(''.'number is :'.$spaces_count.'<br>');
        
        array_walk($var, function(&$value, &$key) use (&$spaces_count)
          {
            Util::d__($key . ' => ', $spaces_count);
            Util::d_($value, $spaces_count);
          });
        $spaces_count -= 4;
        
        Util::d__(']', $spaces_count, true);
      }
    else
      {
        Util::d__($var, $spaces_count, true);
      }
    
    
  }

/*public static function to output debug code*/
public static function d__($var, $spaces_count = 0, $add_new_line = false)
  {
    //print(str_repeat('&nbsp;', $spaces_count));
    //for($i=0; $i<$spaces_count; $i++){print ('<pre> </pre>');}
    // print($spaces_count . '&#58;' . str_repeat('&nbsp;', $spaces_count) . $var );
    print(str_repeat('&nbsp;', $spaces_count) . $var);
    if ($add_new_line)
      {
        print('<br>');
      }
    
  }

  public static function toTime($str)
  {
    $time = str_replace('/', '-', $str);
    return strtoTime($time);
  }

public static function getWebformSubmission($nodeId, $submissionId=null)
  {
    // Load the webform submissions file. The webform_get_submission() and
    // webform_submission_update() functions are located here.
    module_load_include('inc', 'webform', 'includes/webform.submissions');

    // Load the node and submission.
    // $node = node_load($nodeId);
    // $submission = webform_get_submission($node->nid, $submissionId);
    // $submission = null;
    if($submissionId)
    {
     return webform_get_submission($nodeId, $submissionId);
    }
    else
    {
      return webform_get_submissions(array('nid'=> $nodeId));
    }
    // return $submission;
  }

// public static function getWebformSubmissions($nodeId)
//   {
//     module_load_include('inc','webform','includes/webform.submissions');
//     $submissions = webform_get_submissions(array('nid'=> $nodeId));

//     // foreach ($submissions as $s=>$submission){
//     //   d($s);
//     //     foreach ($submission->data as $row=>$data){
//     //         print '<pre>';print_r($row);print '</pre>';
//     //          // d($row);
//     //        d($data);
//     //     }

//     // }
//     return $submissions;
//   }


public static function updateWebform($nodeId, $submissionId, $data)
  {
    // Load the webform submissions file. The webform_get_submission() and
    // webform_submission_update() functions are located here.
    module_load_include('inc', 'webform', 'includes/webform.submissions');

    // Load the node and submission.
    $node = node_load($nodeId);
    $submission = webform_get_submission($node->nid, $submissionId);

    // Change submission data.
    //
    // To see what's available, install Devel and run dpm($submission);
    foreach ($data as $key => $value) {
      $submission->data[$key][0] = $value;
    }
    
    // Finally, update the submission.
    webform_submission_update($node, $submission);
  }


public static function insertWebform($nodeId, $data, $transform=true)
  {
    global $user;
    $node_web = node_load($nodeId);
    if($transform)
    {
      $trans = array();
      foreach ($data as $key => $value) {
        $trans[$key] = array($value);
      }
      $data = $trans;
    }
        // $data = array(
        //     1 => array( '<COMPONENT 1 VALUE>'),
        //     2 => array('<COMPONENT 2 VALUE>'),
        // );
        $submission = (object) array(
            'nid' => $node_web->nid,
            'uid' => $user->uid,
            'submitted' => REQUEST_TIME,
            'remote_addr' => ip_address(),
            'is_draft' => FALSE,
            'data' => $data,
            'serial' => Util::_webform_service_submission_serial_next_value($node_web->nid)
        );
        module_load_include('inc', 'webform', 'includes/webform.submissions');
        webform_submission_insert($node_web, $submission);
        // webform_submission_send_mail($node_web, $submission); 
  }

  public static function _webform_service_submission_serial_next_value($nid, $do_increment = TRUE) {
        if ($do_increment) {
            db_transaction();
        }
        $next_serial = db_select('webform', 'w')
        ->forUpdate()
        ->fields('w', array('next_serial'))
        ->condition('nid', $nid)
        ->execute()
        ->fetchField();

        if ($next_serial && $do_increment) {
            $increment_amount = 1;
            db_update('webform')
            ->fields(array('next_serial' => $next_serial + $increment_amount))
            ->condition('nid', $nid)
            ->execute();
        }
        return $next_serial;
}

  
// Unless you have one of those quantum-static thingies, you can't get a truly random number. On Unix-based OSes, however, /dev/urandom works for "more randomness", if you really need that.
// Anyway, if you want an n-digit number, that's exactly what you should get: n individual digits.
  public static function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}



// /**
//  * Implements hook_token_info().
//  */
// public static function hrsfix_token_info() {
// // you will find this in the node section in path auto replacement pattrens
// // then you could use [node:relatedtype]/[node:title] as a url pattern
//     $info['tokens']['node']['relatedtype'] = array(
//         'name' => t('Related Content type'),
//     'description' => t('Related content type LLLLLLLLLLLLLOOOOOOOOOOOOOOOKKKKKKKKKKKKKKKKKK'), // this is so you see it in the big list
//     );
//     return $info;
// }


// /**
//  * Implements hook_tokens().
//  */

// public static function hrsfix_tokens($type, $tokens, array $data = array(), array $options = array()) {
//     $replacements = array();
//     $sanitize = !empty($options['sanitize']);
//     if ($type == 'node' && !empty($data['node'])) {
//         $node = $data['node'];

//         foreach ($tokens as $name => $original) {
//             switch ($name) {
//                 case 'relatedtype':
//                 $toreplace = hrsfix_set_mytoken();
//                 $replacements[$original] = $toreplace;
//                 break;
//             }
//         }
//     }
//     return $replacements;
// }

// // however we will need to call this public static function from our code or see above
// public static function hrsfix_set_mytoken() {
//     // query to set the url path of allready created node by the user

//     global $user;
//     $uid = $user->uid;

//     $type = "page"; /// change to your content type

//     $q = "SELECT * FROM {node} WHERE uid = :uid AND type = :type LIMIT 1";
//     $result = db_query($q, array(':uid' => $uid , ':type'=>$type));

//     foreach ($result as $row) {
//         if(isset($row->nid)){
//             $nid = $row->nid;
//         }else{
//             $nid = false;
//         }
//     }

//     if($nid ==false){
//         // could not find one wtf.
//         drupal_set_message(t("Please create a 'page' first "), 'error');
//         return false;
//     }else{
//     // get the path of that "page"
//         $path = drupal_get_path_alias("node/".$nid);
//         return $path;
//     }

// }
}

//compatibility
/*public static function to output debug code*/
function d($var, $is_die = FALSE)
{
  Util::d($var,$is_die);
}

?>