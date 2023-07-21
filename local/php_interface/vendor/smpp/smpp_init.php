<?php
// подключение необходимых классов для отправки смс
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Exception/SmppException.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Service/Service.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Service/Sender.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Service/Listener.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Pdu/Pdu.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Pdu/Part/Address.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Transport/TransportInterface.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Transport/Exception/SocketTransportException.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Transport/SocketTransport.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Transport/SMPPSocketTransport.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/SMPP.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Client.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/Helper.php';


$host = 'lk.rapporto.ru';
$port = 2779;
$system_id = 'medi_rus_ooo_A';
$password = 'tyfg24xc';
$source_addr = 'medi';