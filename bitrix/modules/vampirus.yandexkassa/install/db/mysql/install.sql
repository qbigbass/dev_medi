CREATE TABLE vampirus_yandexkassa
(
	ID				INT(11) NOT NULL AUTO_INCREMENT,
	INVOICE_ID		VARCHAR(16)	DEFAULT '0'  ,
	ORDER_ID		INT(11)  NOT NULL,
	AMOUNT			DECIMAL(10,2) NOT NULL,
	ACTION_AMOUNT	DECIMAL(10,2) NOT NULL,
	DATE			DATETIME NOT NULL,
	STATUS			INT(11) NOT NULL,
	RECEIPT		TEXT NOT NULL,
	INDEX (ORDER_ID),
	PRIMARY KEY (ID)
);
CREATE TABLE `vampirus_yandexkassa_new` (
  `id` varchar(36)  NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `status` varchar(20)  NOT NULL,
  `refundable` int(1) NOT NULL DEFAULT '0',
  `rrn` varchar(12) DEFAULT NULL,
  `receipt` text NOT NULL,
  `date` datetime NOT NULL,
  `extra` text,
  `amount` decimal(10,2) NOT NULL,
  `second` int(1) NOT NULL DEFAULT '0',
  `saved` int(1) NOT NULL DEFAULT '0',
  `expires_at` datetime NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `vampirus_yandexkassa_refund` (
  `id` varchar(36)  NOT NULL,
  `payment_id` varchar(36 ) NOT NULL,
  `status` varchar(20)  NOT NULL,
  `description` varchar(255)  NOT NULL,
  `date` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `vampirus_yandexkassa_order` (
  `id` varchar(40) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `id` (`id`)
) ;