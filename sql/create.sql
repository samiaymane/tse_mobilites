USE projet_ntiers;

DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS mobilities;
DROP TABLE IF EXISTS destinations;
DROP TABLE IF EXISTS users;

CREATE TABLE destinations
(id INT NOT NULL AUTO_INCREMENT,
school VARCHAR(256) NOT NULL,
city VARCHAR(256) NOT NULL,
country VARCHAR(256) NOT NULL,
latitude REAL NOT NULL,
longitude REAL NOT NULL,
img_path VARCHAR(256),
status TINYINT(1) NOT NULL DEFAULT 1,
PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=LATIN1;

CREATE TABLE programs
(id INT NOT NULL AUTO_INCREMENT,
destination_id INT NOT NULL,
title VARCHAR(256) NOT NULL,
status TINYINT(1) NOT NULL DEFAULT 1,
PRIMARY KEY(id),
CONSTRAINT `fk_program_destination`
    FOREIGN KEY (destination_id) REFERENCES destinations (id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE=INNODB DEFAULT CHARSET=LATIN1;

CREATE TABLE users
(id INT NOT NULL AUTO_INCREMENT,
num_etudiant INT(10),
role TINYINT(1) NOT NULL DEFAULT 0,
username VARCHAR(45) NOT NULL,
password VARCHAR(256) NOT NULL,
name VARCHAR(45) NOT NULL,
surname VARCHAR(45) NOT NULL,
year_group VARCHAR(10),
image_path VARCHAR(256) NOT NULL DEFAULT 'default.jpg',
PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=LATIN1;

CREATE TABLE mobilities
(id INT NOT NULL AUTO_INCREMENT,
student_id INT NOT NULL,
program_id INT NOT NULL,
start_date DATE NOT NULL,
end_date DATE NOT NULL,
submission_date DATETIME NOT NULL,
status TINYINT(1) DEFAULT 0,
PRIMARY KEY(id),
CONSTRAINT `fk_mobility_user`
    FOREIGN KEY (student_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
CONSTRAINT `fk_mobility_program`
    FOREIGN KEY (program_id) REFERENCES programs (id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE=INNODB DEFAULT CHARSET=LATIN1;
