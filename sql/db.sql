-- Banco completo do projeto (XAMPP / MySQL).
-- Uso: mysql -u root -p < sql/db.sql
--   ou cole no phpMyAdmin (aba SQL).
CREATE DATABASE IF NOT EXISTS projeto
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE projeto;

-- Usuários do sistema (login com e-mail, senha e vínculo à equipe)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    equipe VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gestores (acesso administrativo)
CREATE TABLE IF NOT EXISTS gestor (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_gestor_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Respostas do formulário (nível emocional, texto livre, e-mail de quem respondeu)
CREATE TABLE IF NOT EXISTS respostas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    emocional TINYINT UNSIGNED NULL COMMENT 'Escala 0-5 (emocional / estresse na semana)',
    texto TEXT NULL,
    email_do_usuario VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_respostas_email (email_do_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
