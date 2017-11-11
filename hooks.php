<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 11.11.2017
 * Time: 15:34
 */

use WHMCS\Database\Capsule;

require __DIR__ . '/vendor/autoload.php';

function GetAdminPath() {
	global $customadminpath;

	if ( isset( $customadminpath ) && ! empty( $customadminpath ) ) {
		return $customadminpath;
	}

	return 'admin';
}

function GetConfigModule() {
	$AddonModuleConfig = Capsule::table( 'tbladdonmodules' )->where( 'module', '=', 'NotificationTicketTelegram' )->get();
	$config            = [];

	for ( $i = 0; $i < count( $AddonModuleConfig ); $i ++ ) //var_dump($vars);
	{
		$config[ $AddonModuleConfig[ $i ]->setting ] = $AddonModuleConfig[ $i ]->value;
	}

	return $config;
}

function GetSystemURL() {
	return Capsule::table( 'tblconfiguration' )->where( 'setting', '=', 'SystemURL' )->first()->value;
}

function FormaterTicketMessage( $message ) {
	return explode( '----------------------------', $message )[0];
}

add_hook( 'TicketOpen', 1, function ( $vars ) {
	$config = GetConfigModule();

	if ( array_key_exists( $vars['deptid'], array_flip( explode( ',', $config['ignore_deptid'] ) ) ) ) {
		return;
	}

	try {
		new Longman\TelegramBot\Telegram( $config['api_key'], $config['username'] ); //
	} catch ( \Exception $e ) {
		logActivity( 'NotificationTicketTelegram: ' . $e->getMessage() );
		logModuleCall( 'NotificationTicketTelegram', 'init', null, $e->getMessage() );

		return;
	}

	$Message = 'Был создан новый тикет ' . PHP_EOL
	           . 'Тема: ' . $vars['subject'] . PHP_EOL
	           . 'В отделе: ' . $vars['deptname'] . PHP_EOL
	           . 'Ссылка: ' . GetSystemURL() . GetAdminPath() . '/supporttickets.php?action=view&id=' . $vars['ticketid'] . PHP_EOL
	           . 'Сообщение: ' . FormaterTicketMessage( $vars['message'] );
	$data    = [
		'chat_id' => $config['group_name'],
		'text'    => $Message,
	];
	$result  = Longman\TelegramBot\Request::sendMessage( $data );

	if ( ! $result->isOk() ) {
		logModuleCall( 'NotificationTicketTelegram', 'sendMessage', $data, $result );

		return;
	}
} );

add_hook( 'TicketUserReply', 1, function ( $vars ) {
	$config = GetConfigModule();

	if ( array_key_exists( $vars['deptid'], array_flip( explode( ',', $config['ignore_deptid'] ) ) ) ) {
		return;
	}

	try {
		new Longman\TelegramBot\Telegram( $config['api_key'], $config['username'] ); //
	} catch ( \Exception $e ) {
		logActivity( 'NotificationTicketTelegram: ' . $e->getMessage() );
		logModuleCall( 'NotificationTicketTelegram', 'init', null, $e->getMessage() );

		return;
	}

	$Message = 'Пользователь ответил на тикет ' . PHP_EOL
	           . 'Тема: ' . $vars['subject'] . PHP_EOL
	           . 'В отделе: ' . $vars['deptname'] . PHP_EOL
	           . 'Ссылка: ' . GetSystemURL() . GetAdminPath() . '/supporttickets.php?action=view&id=' . $vars['ticketid'] . PHP_EOL
	           . 'Сообщение: ' . FormaterTicketMessage( $vars['message'] );
	$data    = [
		'chat_id' => $config['group_name'],
		'text'    => $Message,
	];
	$result  = Longman\TelegramBot\Request::sendMessage( $data );

	if ( ! $result->isOk() ) {
		logModuleCall( 'NotificationTicketTelegram', 'sendMessage', $data, $result );

		return;
	}
} );