CREATE TABLE attendance (
	id INT(12) NOT NULL AUTO_INCREMENT,
	ee_id INT(12) NULL DEFAULT NULL,
	att_date VARCHAR(100) NULL DEFAULT NULL,
	overtime VARCHAR(100) NULL DEFAULT NULL,
	holiday_flag TINYINT(1) NULL DEFAULT NULL,
	created VARCHAR(255) NULL DEFAULT NULL,
	modified VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY(id),
	INDEX idx_ee_id(ee_id),
	INDEX idx_holiday_flag(holiday_flag)
);