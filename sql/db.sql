CREATE DATABASE IF NOT EXISTS projeto;
USE projeto;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_admins_usuario (usuario)
);

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
    dados_dinamicos JSON NULL COMMENT 'Respostas flexíveis das perguntas dinâmicas',
    email_do_funcionario VARCHAR(255) NOT NULL,
    equipe_do_funcionario VARCHAR(255) NOT NULL,
    funcionarios_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_respostas_email (email_do_funcionario),
	CONSTRAINT fk_funcionarios_id FOREIGN KEY (funcionarios_id) REFERENCES funcionarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS perguntas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    texto_pergunta VARCHAR(500) NOT NULL,
    tipo_campo VARCHAR(50) NOT NULL DEFAULT 'text' COMMENT 'Pode ser text, select, etc',
    opcoes TEXT NULL COMMENT 'Opções separadas por vírgula se o tipo for select',
    equipe_alvo VARCHAR(255) NULL COMMENT 'Ex: TI, MKT ou Todas',
    gestor_id INT UNSIGNED NULL COMMENT 'Se NULL, é global (admin). Se preenchido, é do gestor específico.',
    ativa BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_perguntas_gestor FOREIGN KEY (gestor_id) REFERENCES gestor(id) ON DELETE CASCADE
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