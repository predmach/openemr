<?php
/**
 * api/resetpassword.php Reset user password.
 *
 * API is allowed to reset user password and send informations by email.
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

$token = $_POST['token'];
$currentpassword = isset($_POST['currentpassword']) && !empty($_POST['currentpassword']) ? $_POST['currentpassword'] : '';
$password = isset($_POST['newpassword']) && !empty($_POST['newpassword']) ? $_POST['newpassword'] : '';
$pin = isset($_POST['pin']) && !empty($_POST['pin']) ? $_POST['pin'] : '';


$xml_string = "<reset>";

if ($userId = validateToken($token)) {
    if (empty($password) && empty($pin)) {
        $xml_string .= "<status>-1</status>";
        $xml_string .= "<reason>Please provide password/pin values.</reason>";
    } else {
        $userInfo = getUserData($userId);
        $query1 = "UPDATE `users` SET ";

        $query2 = '';
        $success = true;
        if (!empty($password)) {
            //$new_password = sha1($password);
            //$query1 .= "`password`='{$new_password}' ";
            $errMsg='';
            $success=update_password($userId,$userId,$currentpassword,$password,$errMsg);
        }
        $result1 =true;
        if (!empty($pin) && $success) {
            $new_pin = sha1($pin);
            $query1 .= "`upin`='{$new_pin}' ";
            $query1 .= "WHERE id = {$userId}";
            $result1 = $db->query($query1);
        }
        /*if ($query2) {
            $result2 = $db->query($query2);
        }else{
            $result2 = 1;
        }*/
        if ($result1 && $success) {
            $xml_string .= "<status>0</status>";
            $xml_string .= "<reason>Successfully reset Password/Pin</reason>";
        } else {
            $xml_string .= "<status>-1</status>";
            $xml_string .= $errMsg ? $errMsg : "<reason>ERROR: Sorry, there was an error processing your data. Please re-submit the information again.</reason>";
            $xml_string .= $errMsg;
        }
    }
} else {
    $xml_string .= "<status>-2</status>";
    $xml_string .= "<reason>Invalid Token</reason>";
}
$xml_string .= "</reset>";
echo $xml_string;
?>
