/*
Created		12/4/2010
Modified		12/19/2010
Project		
Model		
Company		
Author		
Version		
Database		mySQL 5 
*/




drop table IF EXISTS survey_user_data;
drop table IF EXISTS survey_demo_result;
drop table IF EXISTS survey_result;




Create table survey_result (
	survey_result_id Bigint UNSIGNED NOT NULL AUTO_INCREMENT,
	start_time Datetime,
	end_time Datetime,
	ip Varchar(50),
	emote Varchar(255),
	intensity_level Int,
	verbatim Mediumtext,
	code Varchar(255),
 Primary Key (survey_result_id)) ENGINE = InnoDB
DEFAULT CHARACTER SET utf8;

Create table survey_demo_result (
	survey_demo_result_id Bigint UNSIGNED NOT NULL AUTO_INCREMENT,
	survey_result_id Bigint UNSIGNED NOT NULL,
	question Varchar(255),
	answer Mediumtext,
	question_field Varchar(255),
 Primary Key (survey_demo_result_id)) ENGINE = InnoDB
DEFAULT CHARACTER SET utf8;

Create table survey_user_data (
	survey_user_data_id Bigint UNSIGNED NOT NULL AUTO_INCREMENT,
	survey_result_id Bigint UNSIGNED NOT NULL,
	name Varchar(255),
	email Varchar(255),
	phone Varchar(255),
 Primary Key (survey_user_data_id)) ENGINE = InnoDB
DEFAULT CHARACTER SET utf8;









Alter table survey_demo_result add Foreign Key (survey_result_id) references survey_result (survey_result_id) on delete cascade on update cascade;
Alter table survey_user_data add Foreign Key (survey_result_id) references survey_result (survey_result_id) on delete cascade on update cascade;






