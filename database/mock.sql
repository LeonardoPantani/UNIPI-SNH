-- Inserting roles
INSERT INTO role (name) 
VALUES 
('admin'), 
('premium'),
('nonpremium');

-- Inserting users
INSERT INTO users (uuid, email, username, password_hash, created_at, role_id)
VALUES
('d14d3baf-52b6-4d9b-9c7c-857b5095b704', 'admin@example.com',          'adminuser',      '$2y$10$BfCsmfbxxjk/aUxWoI8JhelfGV.g4NWtnhWZ6S9GH/rD4bt5zxHlu', NOW(), 1), -- password: ciaobello
('2f5d7cfa-88f2-4a34-ae3b-48d62248e875', 'premium@example.com',        'premiumuser',    '$2y$10$7yDti.6HAR4dVrCBdf/m5.9uvHajuidSFUrJi4EstGWKiJofG8pXe', NOW(), 2), -- password: ciaobello
('0bb07b4e-4a7f-45f8-bc0f-9c2b2f7b7e83', 'nonpremium@example.com',     'nonpremiumuser', '$2y$10$qN5wlm6z/bUHDzTlJtmx8uNWU.VxOtqDGFRK9Ocs9z2XpQ/gV95Sa', NOW(), 3), -- password: ciaobello
('fee41807-0991-4a00-aaab-7c6ac09ce127', 'leonardo.pantani@gmail.com', 'leonardo',       '$2y$10$rJXgkYhqdKwKwINb0qo5WO2wfCACOsM7TqtrcqTGBeScU6ln4BojS', NOW(), 1); -- password: leonardo

-- Inserting text contents
INSERT INTO text_form (content)
VALUES
('Non ho letto, né leggerò, finché ragione mi assista, il romanzo Il Padrino; già il fatto che un libro sia romanzo non depone a suo favore, è un connotato lievemente losco, come i berretti dei ladruncoli, i molli feltri dei killers, gli impermeabili delle spie. Quando poi un libro è delle dimensioni dei Promessi Sposi, lo si può leggere solo se è I Promessi Sposi; ora, di libri grossi come I Promessi Sposi che siano I Promessi Sposi, ne esiste solo uno, ed è appunto I Promessi Sposi; e non ultima menda del Padrino è appunto quella di non essere I Promessi Sposi. Tuttavia, il piacere che un lettore di professione prova a non leggere Il Padrino è di natura modesta, di qualità semplice, di intensità mediocre. Un lettore di professione è in primo luogo chi sa quali libri non leggere; è colui che sa dire, come scrisse una volta mirabilmente Scheiwiller, "non l''ho letto e non mi piace". Il vero, estremo lettore di professione potrebbe essere un tale che non legge quasi nulla, al limite un semianalfabeta che compita a fatica i nomi delle strade, e solo con luce favorevole. Per un lettore medio, scartare Il Padrino è un gioco da ragazzi. È un blando piacere negativo, come quello di non venire arrestati, che ci succede quasi tutti i giorni.');

-- Inserting files
INSERT INTO file_form (path)
VALUES
('/var/www/html/uploads/x1rBOTg6MaQ4QTWtfaGzK+vldg4YUpxvjA.pdf');

-- Inseriamo dei romanzi
INSERT INTO novel (uuid, title, premium, form_type, form_id, created_at, user_id)
VALUES
('b3b2aee8-2924-4fc1-bbb5-7ad9d501e3c7', 'Il Padrino',     0, 'text_form', 1, NOW(), 2),
('0107678f-af7e-4c90-a4eb-0f058da76b16', 'Controcorrente', 0, 'file_form', 1, NOW(), 3);

-- Inserting password challenges
INSERT INTO password_challenge (user_id, random_string, expire_at)
VALUES
(1, '5TUP1', NOW() + INTERVAL 1 MONTH),
(2, 'D0C0G', NOW() + INTERVAL 1 MONTH),
(3, 'L10N3', NOW() + INTERVAL 1 MONTH);
