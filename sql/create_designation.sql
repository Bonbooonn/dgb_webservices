CREATE TABLE designations (
	id INT(12) NOT NULL AUTO_INCREMENT,
	designation VARCHAR(100) NULL DEFAULT NULL,
	created VARCHAR(255) NULL DEFAULT NULL,
	modified VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY(id),
	UNIQUE idx_unq_designation(designation)
);