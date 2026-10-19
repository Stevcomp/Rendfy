USE Rendify_Calc_Invest;

-- Criar Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    contato VARCHAR(100) NOT NULL UNIQUE,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Criar da Tabela de Ativos
CREATE TABLE IF NOT EXISTS ativos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT NOT NULL,
    nome        VARCHAR(50) NOT NULL,
    categoria   ENUM('fiis', 'acao') NOT NULL,
    tipo_fundo  VARCHAR(50) NOT NULL DEFAULT 'Outro',
    quantidade  INT NOT NULL DEFAULT 1,
    vPago       DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    vRecebido   DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ativos_usuarios
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;