CREATE DATABASE IF NOT EXISTS farmacia;
USE farmacia;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS entregas;
DROP TABLE IF EXISTS receitas;
DROP TABLE IF EXISTS lotes;
DROP TABLE IF EXISTS medicamentos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS role_permissao;
DROP TABLE IF EXISTS permissoes;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- ROLES
-- =========================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) UNIQUE
);

INSERT INTO roles VALUES
(1,'admin'),
(2,'supervisor'),
(3,'balconista');

-- =========================
-- PERMISSÕES
-- =========================
CREATE TABLE permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE
);

INSERT INTO permissoes (nome) VALUES
('gerenciar_usuarios'),
('gerenciar_clientes'),
('gerenciar_medicamentos'),
('gerenciar_lotes'),
('ver_relatorios'),
('registrar_entregas');

-- =========================
-- ROLE x PERMISSÃO
-- =========================
CREATE TABLE role_permissao (
    role_id INT,
    permissao_id INT,
    PRIMARY KEY (role_id, permissao_id),

    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    FOREIGN KEY (permissao_id) REFERENCES permissoes(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO role_permissao SELECT 1, id FROM permissoes;

INSERT INTO role_permissao
SELECT 2, id FROM permissoes
WHERE nome IN (
'gerenciar_clientes',
'gerenciar_medicamentos',
'gerenciar_lotes',
'ver_relatorios'
);

INSERT INTO role_permissao
SELECT 3, id FROM permissoes
WHERE nome = 'registrar_entregas';

-- =========================
-- USUÁRIOS
-- =========================
CREATE TABLE usuarios (
    cpf VARCHAR(11) PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100),
    telefone VARCHAR(20),
    senha VARCHAR(255),
    role_id INT NULL,
    status ENUM('ativo','inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_usuario_role ON usuarios(role_id);

-- =========================
-- CLIENTES
-- =========================
CREATE TABLE clientes (
    cpf VARCHAR(11) PRIMARY KEY,
    nome VARCHAR(100),
    endereco TEXT,
    telefone VARCHAR(20),
    status ENUM('ativo','inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_cliente_nome ON clientes(nome);

-- =========================
-- MEDICAMENTOS
-- =========================
CREATE TABLE medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE,
    composicao TEXT,
    posologia TEXT,
    fabricante VARCHAR(100),
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_medicamento_nome ON medicamentos(nome);

-- =========================
-- LOTES
-- =========================
CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicamento_id INT,
    numero_lote VARCHAR(50),
    validade DATE,
    fabricacao DATE,
    quantidade INT DEFAULT 0,

    UNIQUE(medicamento_id, numero_lote),

    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_lote_validade ON lotes(validade);

-- =========================
-- RECEITAS
-- =========================
CREATE TABLE receitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_cpf VARCHAR(11),
    arquivo VARCHAR(255),
    data_emissao DATE,
    validade DATE,
    medico VARCHAR(100),
    status ENUM('pendente','aprovada','rejeitada') DEFAULT 'pendente',

    FOREIGN KEY (cliente_cpf) REFERENCES clientes(cpf)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- =========================
-- ENTREGAS
-- =========================
CREATE TABLE entregas (
    id INT AUTO_INCREMENT PRIMARY KEY,

    cliente_cpf VARCHAR(11) NULL,
    medicamento_id INT NULL,
    lote_id INT NULL,

    quantidade INT,
    data_entrega DATETIME DEFAULT CURRENT_TIMESTAMP,

    balconista_cpf VARCHAR(11) NULL,
    responsavel VARCHAR(100),

    comprador VARCHAR(100) NULL,

    status ENUM('entregue','cancelado') DEFAULT 'entregue',

    FOREIGN KEY (cliente_cpf) REFERENCES clientes(cpf)
        ON DELETE SET NULL ON UPDATE CASCADE,

    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    FOREIGN KEY (lote_id) REFERENCES lotes(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    FOREIGN KEY (balconista_cpf) REFERENCES usuarios(cpf)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_entrega_data ON entregas(data_entrega);
CREATE INDEX idx_entrega_cliente ON entregas(cliente_cpf);