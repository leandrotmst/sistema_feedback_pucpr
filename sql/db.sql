-- AlignUp - Modelo Relacional v7
-- Uso: cole no phpMyAdmin (aba SQL)

CREATE DATABASE IF NOT EXISTS AlignUp;
USE AlignUp;

-- Equipes / Times
CREATE TABLE IF NOT EXISTS equipe (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome  VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_equipe_nome (nome)
) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci;

-- Gestores (acesso administrativo)
-- O gestor cria e gerencia os perfis dos funcionários
CREATE TABLE IF NOT EXISTS gestor (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_gestor_email (email)
) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci;

-- Usuários / Funcionários
-- Criados pelo gestor, vinculados a uma equipe
CREATE TABLE IF NOT EXISTS funcionario (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email     VARCHAR(255) NOT NULL,
    senha     VARCHAR(255) NOT NULL,
    id_equipe INT UNSIGNED NOT NULL,
    id_gestor INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_funcionario_email (email),
    KEY idx_funcionario_equipe (id_equipe),
    KEY idx_funcionario_gestor (id_gestor),
    CONSTRAINT fk_funcionario_equipe
        FOREIGN KEY (id_equipe) REFERENCES equipe (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_funcionario_gestor
        FOREIGN KEY (id_gestor) REFERENCES gestor (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci;

-- Perguntas do formulário (cadastradas no banco, não hardcoded)
CREATE TABLE IF NOT EXISTS pergunta (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    descricao VARCHAR(500) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci;

-- Respostas do formulário
-- Uma linha por resposta de cada pergunta

CREATE TABLE IF NOT EXISTS respostas (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    id_funcionario  INT UNSIGNED     NOT NULL,
    id_pergunta INT UNSIGNED     NOT NULL,
    valor       TINYINT UNSIGNED NULL     COMMENT 'Escala 1-5 (perguntas de escala)',
    texto       TEXT             NULL     COMMENT 'Texto livre (perguntas abertas)',
    criado_em   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_respostas_funcionario  (id_funcionario),
    KEY idx_respostas_pergunta (id_pergunta),
    CONSTRAINT fk_respostas_funcionario
        FOREIGN KEY (id_funcionario)  REFERENCES funcionario (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_respostas_pergunta
        FOREIGN KEY (id_pergunta) REFERENCES pergunta (id)
        ON UPDATE CASCADE,
    CONSTRAINT chk_valor
        CHECK (valor IS NULL OR valor BETWEEN 1 AND 5)
) ENGINE=InnoDB COLLATE=utf8mb4_unicode_ci;