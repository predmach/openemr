<?php
/**
 * api/getpatientrecord.php fetch patient record.
 *
 * Api fetch complete patient record.
 *
 * Copyright (C) 2012 Karl Englund <karl@mastermobileproducts.com>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-3.0.html>;.
 *
 * @package OpenEMR
 * @author  Karl Englund <karl@mastermobileproducts.com>
 * @link    http://www.open-emr.org
 */
header("Content-Type:text/xml");
$ignoreAuth = true;
require_once('classes.php');

$p_id = $_REQUEST['patientID'];
$token = $_REQUEST['token'];

$xml_array = array();

if ($userId = validateToken($token)) {
    $user_data = getUserData($userId);
    $user = $user_data['user'];
    $emr = $user_data['emr'];
    $username = $user_data['username'];
    $password = $user_data['password'];

    $acl_allow = acl_check('patientportal', 'portal', $username);
    if ($acl_allow) {
        $patient = getPatientData($p_id);
        $xml_array['status'] = 0;
        $xml_array['reason'] = "Success patient processing record";
        if ($patient) {
            $xml_array['Patient']['demographics'] = $patient;

            $ethencity_query = "SELECT option_id, title FROM list_options WHERE list_id  = 'ethnicity' AND `option_id` = ?";
            $patientData = $patient["ethnicity"];
            $ethencity_result = sqlQuery($ethencity_query, array($patientData));

            if ($ethencity_result) {
                $xml_array['Patient']['demographics']['ethnicityvalue'] = $ethencity_result['title'];
            } else {
                $xml_array['Patient']['demographics']['ethnicityvalue'] = '';
            }

            $p_insurance = getInsuranceData($p_id);
            $s_insurance = getInsuranceData($p_id, 'secondary');
            $o_insurance = getInsuranceData($p_id, 'tertiary');

            if ($p_insurance || $s_insurance) {
                $xml_array['Patient']['insurancelist']['status'] = 0;
                $xml_array['Patient']['insurancelist']['insuranceitem-1'] = $p_insurance;
                $xml_array['Patient']['insurancelist']['insuranceitem-2'] = $s_insurance;
                $xml_array['Patient']['insurancelist']['insuranceitem-3'] = $o_insurance;
            } else {
                $xml_array['Patient']['insurancelist']['status'] = 1;
                $xml_array['Patient']['insurancelist']['reason'] = 'No insurance data found';
            }

            $patient_hisory = getHistoryData($p_id);
            if ($patient_hisory) {
                $xml_array['Patient']['history']['status'] = 0;
                $xml_array['Patient']['history'] = $patient_hisory;
            } else {
                $xml_array['Patient']['history']['status'] = 1;
                $xml_array['Patient']['history']['reason'] = 'No history data found';
            }

            $list_data_mp = getListByType($p_id, 'medical_problem');

            if ($list_data_mp) {
                $xml_array['Patient']['problemlist']['status'] = 0;
                foreach ($list_data_mp as $key => $list_data1) {
                    $xml_array['Patient']['problemlist']['problem-' . $key] = $list_data1;
                    $diagnosis_title = getDrugTitle($list_data1['diagnosis'], $db);
                    $xml_array['Patient']['problemlist']['problem-' . $key]['diagnosis_title'] = $diagnosis_title;
                }
            } else {
                $xml_array['Patient']['problemlist']['status'] = 1;
                $xml_array['Patient']['problemlist']['reason'] = 'No Medical Problem data found';
            }

            $list_data_m = getListByType($p_id, 'medication');

            if ($list_data_m) {
                $xml_array['Patient']['medicationlist']['status'] = 0;
                foreach ($list_data_m as $key => $list_data1_m) {
                    $xml_array['Patient']['medicationlist']['medication-' . $key] = $list_data1_m;
                    $diagnosis_title = getDrugTitle($list_data1_m['diagnosis'], $db);
                    $xml_array['Patient']['medicationlist']['medication-' . $key]['diagnosis_title'] = $diagnosis_title;
                }
            } else {
                $xml_array['Patient']['medicationlist']['status'] = 1;
                $xml_array['Patient']['medicationlist']['reason'] = 'No Medication data found';
            }

            $list_data_a = getListByType($p_id, 'allergy');
            if ($list_data_a) {
                $xml_array['Patient']['allergylist']['status'] = 0;
                foreach ($list_data_a as $key => $list_data1_a) {
                    $xml_array['Patient']['allergylist']['allergy-' . $key] = $list_data1_a;
                    $diagnosis_title = getDrugTitle($list_data1_a['diagnosis'], $db);
                    $xml_array['Patient']['allergylist']['allergy-' . $key]['diagnosis_title'] = $diagnosis_title;
                }
            } else {
                $xml_array['Patient']['allergylist']['status'] = 1;
                $xml_array['Patient']['allergylist']['reason'] = 'No Allergy data found';
            }

            $list_data_d = getListByType($p_id, 'dental');
            if ($list_data_d) {
                $xml_array['Patient']['dentallist']['status'] = 0;
                foreach ($list_data_d as $key => $list_data1_d) {
                    $xml_array['Patient']['dentallist']['dental-' . $key] = $list_data1_d;
                    $diagnosis_title = getDrugTitle($list_data1_d['diagnosis'], $db);
                    $xml_array['Patient']['dentallist']['dental-' . $key]['diagnosis_title'] = $diagnosis_title;
                }
            } else {
                $xml_array['Patient']['dentallist']['status'] = 1;
                $xml_array['Patient']['dentallist']['reason'] = 'No Dental data found';
            }

            $list_data_s = getListByType($p_id, 'surgery');
            if ($list_data_s) {
                $xml_array['Patient']['surgerylist']['status'] = 0;
                foreach ($list_data_s as $key => $list_data1_s) {
                    $xml_array['Patient']['surgerylist']['surgery-' . $key] = $list_data1_s;
                    $diagnosis_title = getDrugTitle($list_data1_s['diagnosis'], $db);
                    $xml_array['Patient']['surgerylist']['surgery-' . $key]['diagnosis_title'] = $diagnosis_title;
                }
            } else {
                $xml_array['Patient']['surgerylist']['status'] = 1;
                $xml_array['Patient']['surgerylist']['reason'] = 'No surgery data found';
            }

            $patient_data = getPatientNotes($p_id);
            if ($patient_data) {
                $xml_array['Patient']['notelist']['status'] = 0;
                foreach ($patient_data as $key => $patient_data_a) {
                    $xml_array['Patient']['notelist']['note-' . $key] = $patient_data_a;
                }
            } else {
                $xml_array['Patient']['notelist']['status'] = 1;
                $xml_array['Patient']['notelist']['reason'] = 'No Patient Data found';
            }
            //-----------------------------------CLINICAL ALERTS -----------------------------
            $dateTarget = ($dateTarget) ? $dateTarget : date('Y-m-d H:i:s');
            // Collect active actions
            $actions = test_rules_clinic('','passive_alert',$dateTarget,'reminders-all', $p_id,'','default', array(),'primary',NULL,NULL,$user);
            $alertsArray = fetchDataArray($actions, $dateTarget);
            if($alertsArray){
            /*$sqlQuery = "SELECT `date`, `pid`, `uid`, `category`, `value`, `new_value` FROM `clinical_rules_log` WHERE `pid` = ". $p_id;
            $resData = sqlStatement($sqlQuery);
            if($resData){
                $all = array();
                for($iter = 0;$row = sqlFetchArray($resData);$iter++){
                  $all_alerts = json_decode($row['value'], true);
                  $new_alerts = json_decode($row['newvalue'], true);
                  $rules = array();
                  $count = 0;
                  foreach ($all_alerts as $targetInfo => $alert) {
                    if ( ($row['category'] == 'clinical_reminder_widget') || ($row['category'] == 'active_reminder_popup') ) {
                        $rule_title = getListItemTitle("clinical_rules",$alert['rule_id']);
                        $catAndTarget = explode(':',$targetInfo);
                        $category = $catAndTarget[0];
                        $target = $catAndTarget[1];
                        $description = generate_display_field(array('data_type'=>'1','list_id'=>'rule_action_category'),$category) .
                        ": " . generate_display_field(array('data_type'=>'1','list_id'=>'rule_action'),$target) .
                        " (" . generate_display_field(array('data_type'=>'1','list_id'=>'rule_reminder_due_opt'),$alert['due_status']) . ")";
                        $rules['rule-'.$count]['title'] = $rule_title;
                        $rules['rule-'.$count]['category'] = $category;
                        $rules['rule-'.$count]['description'] = $description;
                        $count++;
                    }
                  }
                  $row['rules'] = $rules;
                  $row -> newvalue = '';
                  unset($row['newvalue']);
                  unset($row['value']);
                  unset($row['category']);
                  $all[$iter] = $row;
                }
                $xml_array['Patient']['alertlist']['status'] = 0;
                foreach ($all as $key => $alert_data_a) {
                    $xml_array['Patient']['alertlist']['alert-' . $key] = $alert_data_a;
                }
                */
                $xml_array['Patient']['alertlist']['status'] = 0;
                $xml_array['Patient']['alertlist']['alerts'] = $alertsArray;
            }else{
              $xml_array['Patient']['alertlist']['status'] = 1;
              $xml_array['Patient']['alertlist']['reason'] = 'No Alert Data found';
            }
            //-------------------------------------ALERTS - END --------------------------------
            //-------------------------------------Appointment START----------------------------
            $appointments = getAppointmentList($p_id);
            if ($appointments) {
                foreach ($appointments as $key => $appointment) {
                    $xml_array["Patient"]["appointmentlist"]["appointment-$key"] = $appointment;
                }
            } else {
                $xml_array["Patient"]["appointmentlist"]['status'] = -1;
                $xml_array["Patient"]["appointmentlist"]['reason'] = 'Appointment not found.';
            }
            //-------------------------------------Appointment END-------------------------------
            $strQuery8 = "select date as vitalsdate, bps, bpd, weight, height, temperature, temp_method,
				pulse, respiration, note as vitalnote, bmi, bmi_status, waist_circ, head_circ,
				oxygen_saturation
				FROM form_vitals
				WHERE pid = ?
				ORDER BY DATE DESC";

            $dbresult8 = sqlStatement($strQuery8, array($p_id));
            if ($dbresult8) {
                $counter8 = 0;
                $xml_array['Patient']['vitalslist']['status'] = 0;
                while ($row8 = sqlFetchArray($dbresult8)) {
                    foreach ($row8 as $fieldname => $fieldvalue8) {
                        $rowvalue = xmlsafestring($fieldvalue8);
                        $xml_array['Patient']['vitalslist']['vitals-' . $counter8][$fieldname] = $rowvalue;
                    } // foreach
                    $counter8++;
                }
            } else {
                $xml_array['Patient']['vitalslist']['status'] = 1;
                $xml_array['Patient']['vitalslist']['reason'] = 'No Patient Vital Data found';
            }


            $strQuery1 = "SELECT d.date,d.size,d.url,d.docdate,d.mimetype,c2d.category_id
                                FROM `documents` AS d
                                INNER JOIN `categories_to_documents` AS c2d ON d.id = c2d.document_id
                                WHERE foreign_id = ?
                                AND category_id = 13
                                ORDER BY category_id, d.date DESC
                                LIMIT 1";

            $result1 = sqlQuery($strQuery1, array($p_id));

            if ($result1) {
                $xml_array['Patient']['demographics']['profile_image'] = getUrl($result1['url']);
            } else {
                $xml_array['Patient']['demographics']['profile_image'] = '';
            }
        } else {
            $xml_array['Patient']['patientdata']['status'] = 1;
            $xml_array['Patient']['patientdata']['reason'] = 'Error processing patient records';
        }
    } else {
        $xml_array['status'] = -2;
        $xml_array['reason'] = 'You are not Authorized to perform this action';
    }
} else {
    $xml_array['status'] = -2;
    $xml_array['reason'] = 'Invalid Token';
}

echo ArrayToXML::toXml($xml_array, 'PatientList');

//-----------------------------------------Added Supporting Functions-----------------------------------
function display_array($your_array){
  foreach ($your_array as $key => $value)
  {
      if(is_array($value))
      {
          display_array($value);
      }
      else
      {
           echo "Key: $key; Value: $value<br />\n";
      }
  }
}

function getAppointmentList($patient_id){
  $appointmentArray = array();
  if ($date == "") {
      $date = date('Y-m-d');
  }
  $todate = strtotime('+1 month',strtotime ($date));
  $todate = date('Y-m-d', $todate);
  $appointments = fetchAppointments($date, $todate, $patient_id, $provider_id = null, $facility_id = null);
  return $appointments;
}

function fetchDataArray($actions,$dateTarget)
{

    $current_targets = array();
    $rules = array();
    $counter = 0;
    foreach ($actions as $action) {

      // Deal with plan names first
      if (isset($action['is_plan']) && $action['is_plan'])  {
        //echo "<br><b>";
        //echo htmlspecialchars( xl("Plan"), ENT_NOQUOTES) . ": ";
        //echo generate_display_field(array('data_type'=>'1','list_id'=>'clinical_plans'),$action['id']);
        //echo "</b><br>";
        continue;
      }

      // Collect the Rule Title, Rule Developer, Rule Funding Source, and Rule Release and show it when hover over the item.
      $tooltip = '';
      if (!empty($action['rule_id'])) {
        $reminder_interval_type = "patient_reminder";
        $target_dates = calculate_reminder_dates($action['rule_id'], $dateTarget, $reminder_interval_type);
        //display_array($target_dates);
        $rule_title = getListItemTitle("clinical_rules",$action['rule_id']);
        $ruleData = sqlQuery("SELECT `developer`, `funding_source`, `release_version`, `web_reference` " .
                             "FROM `clinical_rules` " .
                             "WHERE  `id`=? AND `pid`=0", array($action['rule_id']) );
        $developer = $ruleData['developer'];
        $funding_source = $ruleData['funding_source'];
        $release = $ruleData['release_version'];
        $web_reference = $ruleData['web_reference'];
        if (!empty($rule_title)) {
          $tooltip = xla('Rule Title') . ": " . attr($rule_title);
        }
        if (!empty($developer)) {
          $tooltip .= xla('Rule Developer') . ": " . attr($developer);
        }
        if (!empty($funding_source)) {
          $tooltip .= xla('Rule Funding Source') . ": " . attr($funding_source);
        }
        if (!empty($release)) {
          $tooltip .= xla('Rule Release') . ": " . attr($release);
        }
        /*if ( (!empty($tooltip)) || (!empty($web_reference)) ) {
          if (!empty($web_reference)) {
            $tooltip = "<a href='".attr($web_reference)."' target='_blank' style='white-space: pre-line;' title='".$tooltip."'>?</a>";
          }
          else {
            $tooltip = "<span style='white-space: pre-line;' title='".$tooltip."'>?</span>";
          }
        }*/
      }

      if ($action['custom_flag']) {
        // Start link for reminders that use the custom rules input screen
        //$url = "../rules/patient_data.php?category=".htmlspecialchars( $action['category'], ENT_QUOTES);
        //$url .= "&item=".htmlspecialchars( $action['item'], ENT_QUOTES);
        //echo "<a href='".$url."' class='iframe medium_modal' onclick='top.restoreSession()'>";
      }
      else if ($action['clin_rem_link']) {
        // Start link for reminders that use the custom rules input screen
      $pieces_url = parse_url($action['clin_rem_link']);
      $url_prefix = $pieces_url['scheme'];
      if($url_prefix == 'https' || $url_prefix == 'http'){
      //echo "<a href='" . $action['clin_rem_link'] .
      //    "' class='iframe  medium_modal' onclick='top.restoreSession()'>";
      }else{
      //echo "<a href='../../../" . $action['clin_rem_link'] .
      //    "' class='iframe  medium_modal' onclick='top.restoreSession()'>";
      }
      }
      else {
        // continue since no link is needed
      }

      // Display Reminder Details
      $rule_desc =  generate_display_field(array('data_type'=>'1','list_id'=>'rule_action_category'),$action['category']) .
        ": " . generate_display_field(array('data_type'=>'1','list_id'=>'rule_action'),$action['item']);


      //echo "Rule Desc::". $rule_desc;

      if ($action['custom_flag'] || $action['clin_rem_link']) {
        // End link for reminders that use an html link
        //echo "</a>";
      }

      // Display due status
      if ($action['due_status']) {
        // Color code the status (red for past due, purple for due, green for not due and black for soon due)
        if ($action['due_status'] == "past_due") {
          //echo "&nbsp;&nbsp;(<span style='color:red'>";
        }
        else if ($action['due_status'] == "due") {
          //echo "&nbsp;&nbsp;(<span style='color:purple'>";
        }
        else if ($action['due_status'] == "not_due") {
          //echo "&nbsp;&nbsp;(<span style='color:green'>";
        }
        else {
          //echo "&nbsp;&nbsp;(<span>";
        }
        //echo array('data_type'=>'1','list_id'=>'rule_reminder_due_opt');
        //echo "<br>Due Status::".$action['due_status'];
        $due_status = generate_display_field(array('data_type'=>'1','list_id'=>'rule_reminder_due_opt'),$action['due_status']);
        //echo $due_status;
      }

      // Display the tooltip
      if (!empty($tooltip)) {
        //echo "&nbsp;".$tooltip."<br>";
        //echo "<br>".$tooltip."<br>";
      }
      else {
        //echo "<br>";
      }

      // Add the target(and rule id and room for future elements as needed) to the $current_targets array.
      // Only when $mode is reminders-due
      if ($mode == "reminders-due" && $GLOBALS['enable_alert_log']) {
        $target_temp = $action['category'].":".$action['item'];
        $current_targets[$target_temp] =  array('rule_id'=>$action['rule_id'],'due_status'=>$action['due_status']);
      }
      $rules['alert'.$counter]["description"] = $rule_desc;
      $rules['alert'.$counter]["title"] = $tooltip;
      $rules['alert'.$counter]["duestatus"] = $due_status;
      $datecounter = 0;
      foreach ($target_dates as $target_date) {
        $rules['alert'.$counter]["targetdates"]['date-'.$datecounter] = $target_date;
        $datecounter++;
      }
      $counter++;

    }

    return $rules;

}
?>
