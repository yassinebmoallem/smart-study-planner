
CREATE DATABASE IF NOT EXISTS smart_study CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_study;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    cree_le     DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS matieres (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id      INT NOT NULL,
    nom                 VARCHAR(150) NOT NULL,
    difficulte          ENUM('facile', 'moyen', 'difficile') NOT NULL DEFAULT 'moyen',
    date_examen         DATE NOT NULL,
    heures_totales      FLOAT NOT NULL DEFAULT 0,
    heures_par_semaine  FLOAT NOT NULL DEFAULT 0,
    heures_completees   FLOAT NOT NULL DEFAULT 0,
    cree_le             DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sessions_etude (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    matiere_id    INT NOT NULL,
    date_session  DATE NOT NULL,
    heures        FLOAT NOT NULL DEFAULT 0,
    fait          TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);
