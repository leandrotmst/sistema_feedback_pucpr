-- Massa de dados de teste do AlignUp (usada pelos testes do PHPUnit e também nos testes manuais, JMeter e Lighthouse)
-- ATENÇÃO: apaga e cria de novo o banco "projeto".
DROP DATABASE IF EXISTS projeto;
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
    pontuacao INT UNSIGNED NOT NULL DEFAULT 0,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_funcionarios_email (email),
    KEY idx_funcionarios_pontuacao (pontuacao),
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

CREATE TABLE IF NOT EXISTS solucoes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    equipe VARCHAR(255) NULL,
    analista_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_solucoes_analista FOREIGN KEY (analista_id) REFERENCES analista_dados(id) ON DELETE CASCADE
);

INSERT INTO admins (id, usuario, senha) VALUES (1,'admin','admin123');
INSERT INTO gestor (id, email, senha) VALUES (1,'gestor1@empresa.com','g1senha'),(2,'gestor2@empresa.com','g2senha');
INSERT INTO funcionarios (id, email, senha, equipe, gestor_id, pontuacao) VALUES (1,'ana@empresa.com','ana123','Desenvolvimento',1,1), (2,'bruno@empresa.com','bruno123','Qualidade',1,1), (3,'carla@empresa.com','carla123','Financeiro',2,1);
INSERT INTO analista_dados (id, email, senha, gestor_id) VALUES (1,'analista1@empresa.com','an1senha',1),(2,'analista2@empresa.com','an2senha',2);
INSERT INTO respostas (id, emocional, texto, email_do_funcionario, equipe_do_funcionario, funcionarios_id) VALUES (1,2,'Semana tranquila, entregas no prazo','ana@empresa.com','Desenvolvimento',1), (2,4,'Muita pressão com o prazo da sprint','bruno@empresa.com','Qualidade',2), (3,1,'Fechamento do mês correu bem','carla@empresa.com','Financeiro',3);
INSERT INTO solucoes (id, titulo, descricao, equipe, analista_id) VALUES (1,'Rodízio de plantão','Distribuir plantões entre a equipe','Qualidade',1), (2,'Revisar metas','Ajustar metas do fechamento','Financeiro',2);
