-- Inserting roles
INSERT INTO role (name) 
VALUES 
('admin'), 
('premium'),
('nonpremium');

-- Inserting users
INSERT INTO users (uuid, email, username, password_hash, created_at, role_id)
VALUES
('d14d3baf-52b6-4d9b-9c7c-857b5095b704', 'admin@example.com',          'adminuser',      '$2y$10$7cUQWc6y7PFtGIE/Bf9ZVOeIs0eAO5q73mibxmpYk0BcjLMtKK6sW', NOW(), 1), -- password: passwordA1!
('2f5d7cfa-88f2-4a34-ae3b-48d62248e875', 'premium@example.com',        'premiumuser',    '$2y$10$bhj2MW7n.HFHvLWEiFwZVOnHU5uv3snEe3SPY1v/QON1qh4b8wIyq', NOW(), 2), -- password: passwordA1!
('0bb07b4e-4a7f-45f8-bc0f-9c2b2f7b7e83', 'nonpremium@example.com',     'nonpremiumuser', '$2y$10$TBGT4t5Rrb/gUZ4g17Q70uy3xDAza57ibcepGgwpkTYj/6.olteCG', NOW(), 3), -- password: passwordA1!
('fee41807-0991-4a00-aaab-7c6ac09ce127', 'leonardo.pantani@gmail.com', 'leonardo',       '$2y$10$XCL2xrFz2J3BgXyO1Hh7E.paFl3Re.zSeGXGvzgYYm0DgEsn8wzzC', NOW(), 1); -- password: leonardoA1!

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
INSERT INTO password_challenge (random_string, expire_at, user_id)
VALUES
('5TUP1', NOW() + INTERVAL 1 MONTH, 1),
('D0C0G', NOW() + INTERVAL 1 MONTH, 2),
('L10N3', NOW() + INTERVAL 1 MONTH, 3);
