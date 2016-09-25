<?php
/**
 * api/getflowboarditems.php fetch Flow Board.
 *
 * Api fetch Flow Board.
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

$u_id = $_REQUEST['providerID'];   //Provider ID (developer)
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
        $appointments = getAppointmentList($u_id);  //Fetch Flow Board Data for Given User ID (Not For Patient ID)
          if ($appointments) {
            $xml_array['status'] = 0;
            $xml_array['reason'] = "Success Flow Board Processing";

            //-------------------------------------Appointment START----------------------------
            foreach ($appointments as $key => $appointment) {
                $xml_array["FlowBoard"]["appointmentlist"]["appointment-$key"] = $appointment;
            }
            //-------------------------------------Appointment END-------------------------------
        } else {
            $xml_array['status'] = -1;
            $xml_array['reason'] = "Appointment not found.";
        }
    } else {
        $xml_array['status'] = -2;
        $xml_array['reason'] = 'You are not Authorized to perform this action';
    }
} else {
    $xml_array['status'] = -2;
    $xml_array['reason'] = 'Invalid Token';
}

echo ArrayToXML::toXml($xml_array, 'FlowBoardList');

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

function getAppointmentList($u_id){
  $appointmentArray = array();
  if ($date == "") {
      $date = date('Y-m-d');
  }
  $todate = date('Y-m-d');
  $appointments = fetchAppointments($date, $todate, null, $u_id, $facility_id = null, null, null, null, null, true);
  $appointments = sortAppointments( $appointments, 'time' );

    	foreach ( $appointments as &$appointment ) {
        # Collect appt date and set up squashed date for use below
        $date_appt = $appointment['pc_eventDate'];
        $date_squash = str_replace("-","",$date_appt);
        # Collect variables and do some processing
        $docname  = $appointment['ulname'] . ', ' . $appointment['ufname'] . ' ' . $appointment['umname'];
        if (strlen($docname)<= 3 ) continue;
        $ptname = $appointment['lname'] . ', ' . $appointment['fname'] . ' ' . $appointment['mname'];
        $appt_enc = $appointment['encounter'];
        if($appt_enc != 0) {
          $appointment['str_encounter'] = text($appt_enc);
        }else{
          $appointment['str_encounter'] = '';
        }
        $appt_eid = (!empty($appointment['eid'])) ? $appointment['eid'] : $appointment['pc_eid'];
        $appt_pid = (!empty($appointment['pid'])) ? $appointment['pid'] : $appointment['pc_pid'];
        if ($appt_pid ==0 ) continue; // skip when $appt_pid = 0, since this means it is not a patient specific appt slot
        $status = (!empty($appointment['status'])) ? $appointment['status'] : $appointment['pc_apptstatus'];
        $appointment['str_status'] = text(getListItemTitle("apptstat",$status));
        $appt_room = (!empty($appointment['room'])) ? $appointment['room'] : $appointment['pc_room'];
        $appt_time = (!empty($appointment['appttime'])) ? $appointment['appttime'] : $appointment['pc_startTime'];
        $tracker_id = $appointment['id'];
        $newarrive = collect_checkin($tracker_id);
        $newend = collect_checkout($tracker_id);
        $colorevents = (collectApptStatusSettings($status));
        $bgcolor = $colorevents['color'];
        $appointment['str_bgcolor'] = $bgcolor;
        $statalert = $colorevents['time_alert'];
        # process the time to allow items with a check out status to be displayed
        if ( is_checkout($status) && ($GLOBALS['checkout_roll_off'] > 0) ) {
                $to_time = strtotime($newend);
                $from_time = strtotime($datetime);
                $display_check_out = round(abs($from_time - $to_time) / 60,0);
                if ( $display_check_out >= $GLOBALS['checkout_roll_off'] ) continue;
        }

        $appointment['str_room'] = getListItemTitle('patient_flow_board_rooms', $appt_room);
        $appointment['str_arrive'] = $newarrive;

        #time in current status - START
        $to_time = strtotime(date("Y-m-d H:i:s"));
        $yestime = '0';
        if (strtotime($newend) != '') {
          $from_time = strtotime($newarrive);
          $to_time = strtotime($newend);
          $yestime = '0';
        }
        else
        {
          $from_time = strtotime($appointment['start_datetime']);
          $yestime = '1';
        }

        $timecheck = round(abs($to_time - $from_time) / 60,0);
         if ($timecheck >= $statalert && ($statalert != '0')) { # Determine if the time in status limit has been reached.
            $appointment['str_blikclass'] ='js-blink-infinite';
         }
         else
         {
            $appointment['str_blikclass'] ='detail';
         }
         if (($yestime == '1') && ($timecheck >=1) && (strtotime($newarrive)!= '')) {
             $appointment['str_currentstatustime']= text($timecheck . ' ' .($timecheck >=2 ? xl('minutes'): xl('minute')));
         }else{
             $appointment['str_currentstatustime'] = '';
         }

         # total time in practice
    		 if (strtotime($newend) != '') {
     			  $from_time = strtotime($newarrive);
    			  $to_time = strtotime($newend);
    		 }
         else
         {
			      $from_time = strtotime($newarrive);
 		        $to_time = strtotime(date("Y-m-d H:i:s"));
         }
         $timecheck2 = round(abs($to_time - $from_time) / 60,0);
         if (strtotime($newarrive) != '' && ($timecheck2 >=1)) {
            $appointment['str_totaltime'] = text($timecheck2 . ' ' .($timecheck2 >=2 ? xl('minutes'): xl('minute')));
         }else{
            $appointment['str_totaltime'] = '';
         }
         # end total time in practice
         if (strtotime($newend) != '') {
            $appointment['str_checkouttime'] = text(substr($newend,11));
         }else{
            $appointment['str_checkouttime'] = '';
         }
    }

  return $appointments;
}

?>
