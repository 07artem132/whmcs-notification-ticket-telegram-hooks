<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 11.11.2017
 * Time: 15:19
 */

// Load composer

function NotificationTicketTelegram_config() {
	$configarray = array(
		"name" => "Уведомления о новых неотвеченных тикетах в телеграмме",
		"description" => "",
		"version" => "1.0",
		"author" => "service-voice",
		"fields" => array(
			"api_key" => array ("FriendlyName" => "api_key", "Type" => "text", "Size" => "25",
			                    "Description" => "", "Default" => "", ),
			"username" => array ("FriendlyName" => "username", "Type" => "text", "Size" => "25",
			                    "Description" => "", ),
			"group_name" => array ("FriendlyName" => "chat_id", "Type" => "text", "Size" => "25",
			                    "Description" => "", ),
			"ignore_deptid" => array ("FriendlyName" => "ignore_deptid", "Type" => "text", "Size" => "25",
			                       "Description" => "через запятую", ),
		));
	return $configarray;
}
