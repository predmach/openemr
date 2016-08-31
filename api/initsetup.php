<?php

/**
 * api/initsetup.php perform database changes.
 *
 * API create and modify database tables.
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

$rest_api_server = isset($_GET['rest_api_enable']) ? $_GET['rest_api_enable'] : true;
echo $rest_api_server;
$withurl = 'PD9waHANCi8qKg0KICogQ29weXJpZ2h0IChDKSAyMDEyIEthcmwgRW5nbHVuZCA8a2FybEBtYXN0ZXJtb2JpbGVwcm9kdWN0cy5jb20+DQogKg0KICogTElDRU5TRTogVGhpcyBwcm9ncmFtIGlzIGZyZWUgc29mdHdhcmU7IHlvdSBjYW4gcmVkaXN0cmlidXRlIGl0IGFuZC9vcg0KICogbW9kaWZ5IGl0IHVuZGVyIHRoZSB0ZXJtcyBvZiB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UNCiAqIGFzIHB1Ymxpc2hlZCBieSB0aGUgRnJlZSBTb2Z0d2FyZSBGb3VuZGF0aW9uOyBlaXRoZXIgdmVyc2lvbiAzDQogKiBvZiB0aGUgTGljZW5zZSwgb3IgKGF0IHlvdXIgb3B0aW9uKSBhbnkgbGF0ZXIgdmVyc2lvbi4NCiAqIFRoaXMgcHJvZ3JhbSBpcyBkaXN0cmlidXRlZCBpbiB0aGUgaG9wZSB0aGF0IGl0IHdpbGwgYmUgdXNlZnVsLA0KICogYnV0IFdJVEhPVVQgQU5ZIFdBUlJBTlRZOyB3aXRob3V0IGV2ZW4gdGhlIGltcGxpZWQgd2FycmFudHkgb2YNCiAqIE1FUkNIQU5UQUJJTElUWSBvciBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRS4gU2VlIHRoZQ0KICogR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgZm9yIG1vcmUgZGV0YWlscy4NCiAqIFlvdSBzaG91bGQgaGF2ZSByZWNlaXZlZCBhIGNvcHkgb2YgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlDQogKiBhbG9uZyB3aXRoIHRoaXMgcHJvZ3JhbS4gSWYgbm90LCBzZWUgPGh0dHA6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9ncGwtMy4wLmh0bWw+Oy4NCiAqDQogKiBAcGFja2FnZSBPcGVuRU1SDQogKiBAYXV0aG9yICBLYXJsIEVuZ2x1bmQgPGthcmxAbWFzdGVybW9iaWxlcHJvZHVjdHMuY29tPg0KICogQGxpbmsgICAgaHR0cDovL3d3dy5vcGVuLWVtci5vcmcNCiAqLw0KJGZha2VfcmVnaXN0ZXJfZ2xvYmFscz1mYWxzZTsNCiRzYW5pdGl6ZV9hbGxfZXNjYXBlcz10cnVlOw0KDQppbmNsdWRlX29uY2UoZGlybmFtZShkaXJuYW1lKF9fRklMRV9fKSkgLiAiL2ludGVyZmFjZS9nbG9iYWxzLnBocCIpOw0KDQppZighJEdMT0JBTFNbJ3Jlc3RfYXBpX3NlcnZlciddKXsNCiAgICBlY2hvICI8b3BlbmVtcj4NCiAgICAgICAgICAgIDxzdGF0dXM+LTE8L3N0YXR1cz4NCiAgICAgICAgICAgIDxyZWFzb24+UGxlYXNlIGNoZWNrIHRoZSBSRVNUIEFQSSBzZXJ2ZXIgc2V0dGluZ3MgaW4gQWRtaW5pc3RyYXRpb24vR2xvYmFscy9Db25uZWN0b3JzPC9yZWFzb24+DQogICAgICAgIDwvb3BlbmVtcj4iOw0KICAgIGV4aXQ7DQp9DQoNCg0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL3BpZC5pbmMiKTsNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9wYXRpZW50LmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2xpc3RzLmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL3Bub3Rlcy5pbmMiKTsNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9sb2cuaW5jIik7DQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvYXBwb2ludG1lbnRzLmluYy5waHAiKTsNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9mb3Jtcy5pbmMiKTsNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9iaWxsaW5nLmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2FjbC5pbmMiKTsNCg0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2h0bWxzcGVjaWFsY2hhcnMuaW5jLnBocCIpOwkNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9mb3JtZGF0YS5pbmMucGhwIik7DQogICAgICAgICANCmluY2x1ZGUoImluY2x1ZGVzL2NsYXNzLmRhdGFiYXNlLnBocCIpOw0KaW5jbHVkZSgiaW5jbHVkZXMvY2xhc3MuYXJyYXl0b3htbC5waHAiKTsNCmluY2x1ZGUoImluY2x1ZGVzL2NsYXNzLnBocG1haWxlci5waHAiKTsNCi8vaW5jbHVkZSAnaW5jbHVkZXMvYWVzLmNsYXNzLnBocCc7DQoNCiRzaXRlID0gJ2RlZmF1bHQnOw0KDQokc2l0ZXNEaXIgPSBkaXJuYW1lKGRpcm5hbWUoX19GSUxFX18pKSAuICIvc2l0ZXMvIjsNCg0KDQokdXJsID0gKEAkX1NFUlZFUlsiSFRUUFMiXSA9PSAib24iKSA/ICJodHRwczovLyIgOiAiaHR0cDovLyI7DQppZiAoJF9TRVJWRVJbIlNFUlZFUl9QT1JUIl0gIT0gIjgwIikgew0KICAgICR1cmwgLj0gJF9TRVJWRVJbIlNFUlZFUl9OQU1FIl0gLiAiOiIgLiAkX1NFUlZFUlsiU0VSVkVSX1BPUlQiXS4kX1NFUlZFUlsnUkVRVUVTVF9VUkknXTsNCiAgICAkdXJsMSA9IHN0cl9yZXBsYWNlKCJhcGkiLCAnJywgcGF0aGluZm8oJHVybCxQQVRISU5GT19ESVJOQU1FKSk7DQp9IGVsc2Ugew0KICAgICR1cmwgLj0gJF9TRVJWRVJbIlNFUlZFUl9OQU1FIl0uJF9TRVJWRVJbJ1JFUVVFU1RfVVJJJ107DQogICAgJHVybDEgPSBzdHJfcmVwbGFjZSgiYXBpIiwgJycsIHBhdGhpbmZvKCR1cmwsUEFUSElORk9fRElSTkFNRSkpOw0KfQ0KDQokc2l0ZXNVcmwgPSAkdXJsMSAuICdzaXRlcy8nOw0KJG9wZW5lbXJVcmwgPSAkdXJsMTsNCg0KJG9wZW5lbXJEaXJOYW1lID0gYmFzZW5hbWUoZGlybmFtZShkaXJuYW1lKF9fRklMRV9fKSkpOw0KDQoNCi8qKg0KICogYWJvdmUgc29tZSB2YXJpYWJsZXMgYXJlIHVzZWQgaW4gZnVuY3Rpb25zIGZpbGUNCiAqLw0KaW5jbHVkZSgiaW5jbHVkZXMvZnVuY3Rpb25zLnBocCIpOw0KPz4=';

$withouturl = 'PD9waHANCi8qKg0KICogQ29weXJpZ2h0IChDKSAyMDEyIEthcmwgRW5nbHVuZCA8a2FybEBtYXN0ZXJtb2JpbGVwcm9kdWN0cy5jb20+DQogKg0KICogTElDRU5TRTogVGhpcyBwcm9ncmFtIGlzIGZyZWUgc29mdHdhcmU7IHlvdSBjYW4gcmVkaXN0cmlidXRlIGl0IGFuZC9vcg0KICogbW9kaWZ5IGl0IHVuZGVyIHRoZSB0ZXJtcyBvZiB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UNCiAqIGFzIHB1Ymxpc2hlZCBieSB0aGUgRnJlZSBTb2Z0d2FyZSBGb3VuZGF0aW9uOyBlaXRoZXIgdmVyc2lvbiAzDQogKiBvZiB0aGUgTGljZW5zZSwgb3IgKGF0IHlvdXIgb3B0aW9uKSBhbnkgbGF0ZXIgdmVyc2lvbi4NCiAqIFRoaXMgcHJvZ3JhbSBpcyBkaXN0cmlidXRlZCBpbiB0aGUgaG9wZSB0aGF0IGl0IHdpbGwgYmUgdXNlZnVsLA0KICogYnV0IFdJVEhPVVQgQU5ZIFdBUlJBTlRZOyB3aXRob3V0IGV2ZW4gdGhlIGltcGxpZWQgd2FycmFudHkgb2YNCiAqIE1FUkNIQU5UQUJJTElUWSBvciBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRS4gU2VlIHRoZQ0KICogR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgZm9yIG1vcmUgZGV0YWlscy4NCiAqIFlvdSBzaG91bGQgaGF2ZSByZWNlaXZlZCBhIGNvcHkgb2YgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlDQogKiBhbG9uZyB3aXRoIHRoaXMgcHJvZ3JhbS4gSWYgbm90LCBzZWUgPGh0dHA6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9ncGwtMy4wLmh0bWw+Oy4NCiAqDQogKiBAcGFja2FnZSBPcGVuRU1SDQogKiBAYXV0aG9yICBLYXJsIEVuZ2x1bmQgPGthcmxAbWFzdGVybW9iaWxlcHJvZHVjdHMuY29tPg0KICogQGxpbmsgICAgaHR0cDovL3d3dy5vcGVuLWVtci5vcmcNCiAqLw0KJGZha2VfcmVnaXN0ZXJfZ2xvYmFscz1mYWxzZTsNCiRzYW5pdGl6ZV9hbGxfZXNjYXBlcz10cnVlOw0KDQppbmNsdWRlX29uY2UoZGlybmFtZShkaXJuYW1lKF9fRklMRV9fKSkgLiAiL2ludGVyZmFjZS9nbG9iYWxzLnBocCIpOw0KDQovL2lmKCEkR0xPQkFMU1sncmVzdF9hcGlfc2VydmVyJ10pew0KLy8gICAgZWNobyAiPG9wZW5lbXI+DQovLyAgICAgICAgICAgIDxzdGF0dXM+LTE8L3N0YXR1cz4NCi8vICAgICAgICAgICAgPHJlYXNvbj5QbGVhc2UgY2hlY2sgdGhlIFJFU1QgQVBJIHNlcnZlciBzZXR0aW5ncyBpbiBBZG1pbmlzdHJhdGlvbi9HbG9iYWxzL0Nvbm5lY3RvcnM8L3JlYXNvbj4NCi8vICAgICAgICA8L29wZW5lbXI+IjsNCi8vICAgIGV4aXQ7DQovL30NCg0KDQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvcGlkLmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL3BhdGllbnQuaW5jIik7DQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvbGlzdHMuaW5jIik7DQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvcG5vdGVzLmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2xvZy5pbmMiKTsNCnJlcXVpcmVfb25jZSgiJHNyY2Rpci9hcHBvaW50bWVudHMuaW5jLnBocCIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2Zvcm1zLmluYyIpOw0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2JpbGxpbmcuaW5jIik7DQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvYWNsLmluYyIpOw0KDQpyZXF1aXJlX29uY2UoIiRzcmNkaXIvaHRtbHNwZWNpYWxjaGFycy5pbmMucGhwIik7CQ0KcmVxdWlyZV9vbmNlKCIkc3JjZGlyL2Zvcm1kYXRhLmluYy5waHAiKTsNCiAgICAgICAgIA0KaW5jbHVkZSgiaW5jbHVkZXMvY2xhc3MuZGF0YWJhc2UucGhwIik7DQppbmNsdWRlKCJpbmNsdWRlcy9jbGFzcy5hcnJheXRveG1sLnBocCIpOw0KaW5jbHVkZSgiaW5jbHVkZXMvY2xhc3MucGhwbWFpbGVyLnBocCIpOw0KLy9pbmNsdWRlICdpbmNsdWRlcy9hZXMuY2xhc3MucGhwJzsNCg0KJHNpdGUgPSAnZGVmYXVsdCc7DQoNCiRzaXRlc0RpciA9IGRpcm5hbWUoZGlybmFtZShfX0ZJTEVfXykpIC4gIi9zaXRlcy8iOw0KDQoNCiR1cmwgPSAoQCRfU0VSVkVSWyJIVFRQUyJdID09ICJvbiIpID8gImh0dHBzOi8vIiA6ICJodHRwOi8vIjsNCmlmICgkX1NFUlZFUlsiU0VSVkVSX1BPUlQiXSAhPSAiODAiKSB7DQogICAgJHVybCAuPSAkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSAuICI6IiAuICRfU0VSVkVSWyJTRVJWRVJfUE9SVCJdLiRfU0VSVkVSWydSRVFVRVNUX1VSSSddOw0KICAgICR1cmwxID0gc3RyX3JlcGxhY2UoImFwaSIsICcnLCBwYXRoaW5mbygkdXJsLFBBVEhJTkZPX0RJUk5BTUUpKTsNCn0gZWxzZSB7DQogICAgJHVybCAuPSAkX1NFUlZFUlsiU0VSVkVSX05BTUUiXS4kX1NFUlZFUlsnUkVRVUVTVF9VUkknXTsNCiAgICAkdXJsMSA9IHN0cl9yZXBsYWNlKCJhcGkiLCAnJywgcGF0aGluZm8oJHVybCxQQVRISU5GT19ESVJOQU1FKSk7DQp9DQoNCiRzaXRlc1VybCA9ICR1cmwxIC4gJ3NpdGVzLyc7DQokb3BlbmVtclVybCA9ICR1cmwxOw0KDQokb3BlbmVtckRpck5hbWUgPSBiYXNlbmFtZShkaXJuYW1lKGRpcm5hbWUoX19GSUxFX18pKSk7DQoNCg0KLyoqDQogKiBhYm92ZSBzb21lIHZhcmlhYmxlcyBhcmUgdXNlZCBpbiBmdW5jdGlvbnMgZmlsZQ0KICovDQppbmNsdWRlKCJpbmNsdWRlcy9mdW5jdGlvbnMucGhwIik7DQo/Pg==';

$res3 = false;


if ($rest_api_server) {
    $res3 = file_put_contents('classes.php', base64_decode($withurl));
} else {
    $res3 = file_put_contents('classes.php', base64_decode($withouturl));
}


$ignoreAuth = true;

echo "Before Classes.Php Load";

require_once('classes.php');

echo "After Classes.Php Load";

$query1 = "CREATE TABLE IF NOT EXISTS `api_tokens` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) DEFAULT NULL,
            `token` varchar(150) DEFAULT NULL,
            `device_token` varchar(200) NOT NULL,
            `create_datetime` datetime DEFAULT NULL,
            `expire_datetime` datetime DEFAULT NULL,
            `message_badge` int(5) NOT NULL,
            `appointment_badge` int(5) NOT NULL,
            `labreports_badge` int(5) NOT NULL,
            `prescription_badge` int(5) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
echo $query1;

$db->query($query1);
$res1 = $db->result;

$query2 = "ALTER TABLE `users` ADD `create_date` DATE NOT NULL ,
            ADD `secret_key` VARCHAR( 100 ) NULL ,
            ADD `ip_address` VARCHAR( 20 ) NULL ,
            ADD `country_code` VARCHAR( 10 ) NULL ,
            ADD `country_name` INT( 50 ) NULL ,
            ADD `latidute` VARCHAR( 20 ) NULL ,
            ADD `longitude` VARCHAR( 20 ) NULL ,
            ADD `time_zone` VARCHAR( 10 ) NULL";

$db->query($query2);
$res2 = $db->result;


if ($res1 && $res2 || $res3) {
    echo "<h1>Database Updated Successfully</h1>";
} else {
    echo "<h1>Database Failed to Update</h1>";
}
?>