<?php

/**
 * api/logs.php Allow users to add new logs in system.
 *
 * Api allows to add new logs.
 *
 *
 * @package OpenEMR
 * @author  Chuck Pace
 * @link    http://www.open-emr.org
 */

header("Content-Type:text/xml");
$ignoreAuth = true;
require_once 'classes.php';
$xml_array = array();

$token = $_POST['token'];
$logtime = date("Y-m-d H:i:s", strtotime($_POST['logtime']));
$event = $_POST['event'];  /*Event Type Example. 'chat' */
$loguser = $_POST['user'];  /* User name of log. Not require to be exist in EMR*/
$groupname = $_POST['groupname'];
$comments = $_POST['comments'];

$usernotes = $_POST['usernotes'];
$patientid = $_POST['patientid']; /* Not Require if Log are general logs */
$checksum = $_POST['checksum'];
$crtuser = $_POST['crtuser'];
$logfrom = $_POST['logfrom'];
$menuitemid = $_POST['menuitemid'];
$ccdadocid = $_POST['ccdadocid'];

if ($userId = validateToken($token)) {
    $user_data = getUserData($userId);

    $user = $user_data['user'];
    $emr = $user_data['emr'];
    $username = $user_data['username'];
    $password = $user_data['password'];

    $acl_allow = acl_check('admin', 'super', $user);

    if ($acl_allow) {

        $strQuery = "INSERT INTO log
                            (date,event,user,groupname,comments,user_notes,patient_id,success,checksum,crt_user,log_from,menu_item_id,ccda_doc_id)
                                VALUES ('" . add_escape_custom($logtime) . "',
                                        '" . add_escape_custom($event) . "' ,
                                        '" . add_escape_custom($loguser) . "' ,
                                        '" . add_escape_custom($groupname) . "',
                                        '" . add_escape_custom($comments) . "',
                                        '" . add_escape_custom($usernotes) . "',
                                        " . add_escape_custom($patientid) . ",
                                        " . add_escape_custom(1) . ",
                                        '" . add_escape_custom($checksum) . "',
                                        '" . add_escape_custom($crtuser) . "',
                                        '" . add_escape_custom($logfrom) . "',
                                        " . add_escape_custom($menuitemid) . ",
                                        " . add_escape_custom($ccdadocid) . "
                                        )";
        //echo $strQuery;
        $result = sqlStatement($strQuery);
        if ($result) {
            $xml_array['status'] = 0;
            $xml_array['reason'] = 'The log has been added.';
        } else {
            $xml_array['status'] = -1;
            $xml_array['reason'] = 'ERROR: Sorry, there was an error processing your request. Please re-submit the information again.';
        }
    } else {
        $xml_array['status'] = -2;
        $xml_array['reason'] = 'You are not Authorized to perform this action';
    }
} else {
    $xml_array['status'] = -2;
    $xml_array['reason'] = 'Invalid Token';
}

$xml = ArrayToXML::toXml($xml_array, 'logs');
echo $xml;
?>
