-- Banco completo do projeto (XAMPP / MySQL).
-- Uso: mysql -u root -p < sql/db.sql
--   ou cole no phpMyAdmin (aba SQL).
CREATE DATABASE IF NOT EXISTS projeto
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE projeto;

-- 1. Tabela de Gestores (acesso administrativo)
-- Criada primeiro para que a FK de funcionários possa referenciá-la
CREATE TABLE IF NOT EXISTS gestor (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_gestor_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Funcionários (com vínculo ao gestor)
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    equipe VARCHAR(255) NOT NULL,
    gestor_id INT UNSIGNED NOT NULL, -- Coluna para relacionar com a tabela gestor
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_funcionarios_email (email),
    -- Chave Estrangeira: Garante que o gestor exista e permite listar por id_gestor
    CONSTRAINT fk_funcionarios_gestor FOREIGN KEY (gestor_id) REFERENCES gestor(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Respostas do formulário
CREATE TABLE IF NOT EXISTS respostas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    emocional TINYINT UNSIGNED NULL COMMENT 'Escala 0-5 (emocional / estresse na semana)',
    texto TEXT NULL,
    email_do_funcionario VARCHAR(255) NOT NULL,
    equipe VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_respostas_email (email_do_funcionario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;