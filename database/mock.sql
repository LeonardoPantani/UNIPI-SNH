-- Inserting roles
INSERT INTO role (name) VALUES ('admin');
INSERT INTO role (name) VALUES ('premium');
INSERT INTO role (name) VALUES ('nonpremium');

-- Inserting users
INSERT INTO users (uuid, email, username, password_hash, created_at, role_id)
VALUES
('d14d3baf-52b6-4d9b-9c7c-857b5095b704', 'admin@example.com', 'adminuser', '$2y$10$BfCsmfbxxjk/aUxWoI8JhelfGV.g4NWtnhWZ6S9GH/rD4bt5zxHlu', NOW(), 1), -- password: ciaobello
('2f5d7cfa-88f2-4a34-ae3b-48d62248e875', 'premium@example.com', 'premiumuser', '$2y$10$7yDti.6HAR4dVrCBdf/m5.9uvHajuidSFUrJi4EstGWKiJofG8pXe', NOW(), 2), -- password: ciaobello
('0bb07b4e-4a7f-45f8-bc0f-9c2b2f7b7e83', 'nonpremium@example.com', 'nonpremiumuser', '$2y$10$qN5wlm6z/bUHDzTlJtmx8uNWU.VxOtqDGFRK9Ocs9z2XpQ/gV95Sa', NOW(), 3); -- password: ciaobello

-- Inserting text contents
INSERT INTO text_form (content)
VALUES
('Questo è il contenuto di esempio per un romanzo in formato testo.'),
('Questo è il contenuto di esempio 2 per un romanzo in formato testo 2.'),
('Un altro esempio di contenuto testuale, da inserire nel database.');

-- Inserting files
INSERT INTO file_form (path)
VALUES
('/files/novel1.pdf'),
('/files/novel2.pdf');

-- Inserting novels
INSERT INTO novel (uuid, title, form_type, form_text_id, form_file_id, created_at)
VALUES
('b3b2aee8-2924-4fc1-bbb5-7ad9d501e3c7', 'Romanzo Testuale 1', 0, 1, NULL, NOW()),
('b3b2aee8-2924-4fc1-bbb5-7ad9d501e3c7', 'Romanzo Testuale 2', 0, 1, NULL, NOW()),
('a9b3c88e-4bc9-4372-9737-0707319c2b53', 'Romanzo File 1', 1, NULL, 1, NOW());

-- Associating users to novels
INSERT INTO novel_user (novel_id, user_id)
VALUES
(1, 1),  -- admin       has made Romanzo Testuale 1
(3, 2),  -- premium     has made Romanzo File 1
(2, 3);  -- nonpremium  has made Romanzo Testuale 2

-- Inserting password challenges
INSERT INTO password_challenge (user_id, random_string, expire_at)
VALUES
(1, 'randomstring1', NOW() + INTERVAL 1 DAY),
(2, 'randomstring2', NOW() + INTERVAL 1 DAY),
(3, 'randomstring3', NOW() + INTERVAL 1 DAY);
