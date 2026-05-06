CREATE DATABASE IF NOT EXISTS projeto;
USE projeto;


CREATE TABLE IF NOT EXISTS gestor (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_gestor_email (email)
);


CREATE TABLE IF NOT EXISTS funcionarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    equipe VARCHAR(255) NOT NULL,
    gestor_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_funcionarios_email (email),
    CONSTRAINT fk_funcionarios_gestor FOREIGN KEY (gestor_id) REFERENCES gestor(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS respostas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    emocional TINYINT UNSIGNED NULL COMMENT 'Escala 0-5 (emocional / estresse na semana)',
    texto TEXT NULL,
    email_do_funcionario VARCHAR(255) NOT NULL,
    equipe_do_funcionario VARCHAR(255) NOT NULL,
    funcionarios_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_respostas_email (email_do_funcionario),
	CONSTRAINT fk_funcionarios_id FOREIGN KEY (funcionarios_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS analista_dados (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    gestor_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_analista_dados_email (email),
    CONSTRAINT fk_analista_dados_gestor FOREIGN KEY (gestor_id) REFERENCES gestor(id) ON DELETE CASCADE
);