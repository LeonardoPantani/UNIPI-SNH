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

CREATE TABLE novel (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) CHARACTER SET ascii NOT NULL,
    title VARCHAR(100) NOT NULL,
    form_type BINARY NOT NULL, -- 0 per testo, 1 per file
    form_text_id INTEGER, -- id della tabella text_form (se form_type = 0)
    form_file_id INTEGER, -- id della tabella file_form (se form_type = 1)
    created_at TIMESTAMP NOT NULL,
    FOREIGN KEY (form_text_id) REFERENCES text_form(id),
    FOREIGN KEY (form_file_id) REFERENCES file_form(id),
    CHECK ((form_type = 0 AND form_text_id IS NOT NULL AND form_file_id IS NULL) OR 
           (form_type = 1 AND form_file_id IS NOT NULL AND form_text_id IS NULL))
) ENGINE=InnoDB;

CREATE TABLE novel_user (
    novel_id INTEGER NOT NULL, -- id della novel
    user_id INTEGER NOT NULL, -- id dell'utente
    PRIMARY KEY (novel_id, user_id),
    FOREIGN KEY (novel_id) REFERENCES novel(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE password_challenge (
    user_id INTEGER PRIMARY KEY, -- id dell'utente
    random_string VARCHAR(255) NOT NULL,
    expire_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
