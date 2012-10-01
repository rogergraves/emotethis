drop table IF EXISTS survey_user_data_ext;

Create table survey_user_data_ext (
	survey_user_data_ext_id Bigint UNSIGNED NOT NULL AUTO_INCREMENT,
	survey_result_id Bigint UNSIGNED NOT NULL,
	name Varchar(255),
	val Varchar(255),
 Primary Key (survey_user_data_ext_id)) ENGINE = InnoDB
DEFAULT CHARACTER SET utf8;

Alter table survey_user_data_ext add Foreign Key (survey_result_id) references survey_result (survey_result_id) on delete cascade on update cascade;