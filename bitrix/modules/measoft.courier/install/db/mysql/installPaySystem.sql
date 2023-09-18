create table if not exists measoft_pay_system
(
	ID int(5) NOT NULL auto_increment,
	BITRIX_ID varchar(7),
	PAYSYSTEM_ID int(5),
	CASH int(1),
	CARD int(1),
	PRIMARY KEY(ID)
);