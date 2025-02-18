ALTER DATABASE snh_db ENCRYPTION='Y';

USE snh_db;

CREATE TABLE role (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name ENUM('admin', 'premium', 'nonpremium') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) CHARACTER SET ascii NOT NULL,
    email VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(256) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    role_id INTEGER NOT NULL, -- table role id
    UNIQUE(uuid),
    FOREIGN KEY (role_id) REFERENCES role(id)
) ENGINE=InnoDB;

CREATE TABLE text_form (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE file_form (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(300) NOT NULL
) ENGINE=InnoDB;

/*
BINARY and INTEGER have some problems.
  1) BINARY is not BOOLEAN. By definition, BINARY is similar to VARCHAR/CHAR except 
     that it stores byte strings rather than character strings, so we cannot use it to store
     integers or boolean values.
  2) if we decide to use booleans to store the info about novel form, we need to change this boolean type when 
     a new novel form's table is added (boolean cannot store more than 2 states).
  3) if we decide to use integers to store the info about novel form, we need also to remember how these integer
     values are mapped to the *_form tables (e.g. '0' means 'text_form' and '1' means 'file_form'). How can we 
     remember these relationships when we access the data directly from the db client?
Using ENUM("text_form", "file_form") we can set the range of accepted values (can we do the same thing with INTEGER?).
*/
CREATE TABLE novel (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) CHARACTER SET ascii NOT NULL,
    title VARCHAR(100) NOT NULL,
    premium BOOLEAN NOT NULL, -- 0 is non-premium, >1 is premium
    form_type ENUM("text_form", "file_form") NOT NULL,
    form_id INTEGER NOT NULL, -- id which belongs to one of the *_form tables
    created_at TIMESTAMP NOT NULL,
    user_id INTEGER NOT NULL,
    UNIQUE(uuid),
    UNIQUE(title, user_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE password_challenge (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    random_string VARCHAR(255) NOT NULL,
    expire_at TIMESTAMP NOT NULL,
    user_id INTEGER NOT NULL, -- table user id
    UNIQUE(user_id),
    UNIQUE(random_string),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
