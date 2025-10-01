CREATE DATABASE crud_users CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE crud_users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion de données de test
INSERT INTO users (nom, prenom, email, telephone) VALUES
('Dupont', 'Jean', 'jean.dupont@email.com', '0123456789'),
('Martin', 'Marie', 'marie.martin@email.com', '0234567890'),
('Durand', 'Pierre', 'pierre.durand@email.com', '0345678901'),
('Leroy', 'Sophie', 'sophie.leroy@email.com', '0456789012');
