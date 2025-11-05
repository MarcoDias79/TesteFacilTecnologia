-- =============================================
-- Script de Criação de Tabelas
-- =============================================

-- Criação da tabela Tb_banco
-- Esta tabela não possui FK, então é criada primeiro
CREATE TABLE Tb_banco (
    codigo INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    PRIMARY KEY (codigo)
);

-- Criação da tabela Tb_convenio
-- Possui FK para Tb_banco
CREATE TABLE Tb_convenio (
    codigo INT NOT NULL AUTO_INCREMENT,
    convenio VARCHAR(100) NOT NULL,
    verba DECIMAL(15, 2) NULL,
    banco INT NOT NULL,
    PRIMARY KEY (codigo),
    CONSTRAINT FK_convenio_banco 
        FOREIGN KEY (banco) 
        REFERENCES Tb_banco(codigo)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- Criação da tabela Tb_convenio_servico
-- Possui FK para Tb_convenio
CREATE TABLE Tb_convenio_servico (
    codigo INT NOT NULL AUTO_INCREMENT,
    convenio INT NOT NULL,
    servico VARCHAR(100) NOT NULL,
    PRIMARY KEY (codigo),
    CONSTRAINT FK_convenio_servico_convenio 
        FOREIGN KEY (convenio) 
        REFERENCES Tb_convenio(codigo)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- Criação da tabela Tb_contrato
-- Possui FK para Tb_convenio_servico
CREATE TABLE Tb_contrato (
    codigo INT NOT NULL AUTO_INCREMENT,
    prazo INT NOT NULL,
    valor DECIMAL(15, 2) NOT NULL,
    data_inclusao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    convenio_servico INT NOT NULL,
    PRIMARY KEY (codigo),
    CONSTRAINT FK_contrato_convenio_servico 
        FOREIGN KEY (convenio_servico) 
        REFERENCES Tb_convenio_servico(codigo)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- =============================================
-- Criação de Índices para melhor performance
-- =============================================

CREATE INDEX IDX_convenio_banco ON Tb_convenio(banco);
CREATE INDEX IDX_convenio_servico_convenio ON Tb_convenio_servico(convenio);
CREATE INDEX IDX_contrato_convenio_servico ON Tb_contrato(convenio_servico);
CREATE INDEX IDX_contrato_data_inclusao ON Tb_contrato(data_inclusao);
