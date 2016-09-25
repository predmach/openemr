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
  return $appointments;
}

?>
