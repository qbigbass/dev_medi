create table if not exists measoft_cities
  (
  	ID int(5) NOT NULL auto_increment,
  	BITRIX_ID varchar(7),
  	MEASOFT_ID int(5),
  	NAME varchar(50),
  	PRIMARY KEY(ID)
  );