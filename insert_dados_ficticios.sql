-- =============================================
-- Script de Inserção de Dados Fictícios
-- =============================================

-- Populando Tb_banco
INSERT INTO Tb_banco (nome) VALUES
('Banco do Brasil'),
('Caixa Econômica Federal'),
('Bradesco'),
('Itaú Unibanco'),
('Santander'),
('Banco Inter'),
('Nubank'),
('Banco Safra');

-- Populando Tb_convenio
INSERT INTO Tb_convenio (convenio, verba, banco) VALUES
('INSS - Instituto Nacional do Seguro Social', 1500000.00, 1),
('Prefeitura Municipal de São Paulo', 850000.00, 2),
('Governo do Estado do Rio de Janeiro', 2300000.00, 3),
('SIAPE - Sistema Integrado de Administração de Recursos Humanos', 1800000.00, 4),
('Forças Armadas Brasileiras', 950000.00, 5),
('Tribunal de Justiça de Minas Gerais', 670000.00, 1),
('Prefeitura Municipal do Rio de Janeiro', 1200000.00, 2),
('Governo do Estado de São Paulo', 3500000.00, 4),
('Assembleia Legislativa da Bahia', 420000.00, 6),
('Receita Federal do Brasil', 2800000.00, 3);

-- Populando Tb_convenio_servico
INSERT INTO Tb_convenio_servico (convenio, servico) VALUES
(1, 'Empréstimo Consignado'),
(1, 'Cartão de Crédito Consignado'),
(1, 'Refinanciamento de Dívidas'),
(2, 'Empréstimo Pessoal'),
(2, 'Antecipação de Férias'),
(3, 'Empréstimo Consignado'),
(3, 'Saque Aniversário FGTS'),
(4, 'Empréstimo Consignado'),
(4, 'Cartão Benefício'),
(5, 'Empréstimo Militar'),
(5, 'Financiamento Imobiliário'),
(6, 'Empréstimo Consignado'),
(7, 'Empréstimo Pessoal'),
(7, 'Antecipação 13º Salário'),
(8, 'Empréstimo Consignado'),
(8, 'Cartão de Crédito Consignado'),
(9, 'Empréstimo Pessoal'),
(10, 'Empréstimo Consignado'),
(10, 'Refinanciamento'),
(10, 'Portabilidade de Crédito');

-- Populando Tb_contrato
INSERT INTO Tb_contrato (prazo, valor, data_inclusao, convenio_servico) VALUES
-- Contratos de janeiro de 2024
(24, 5000.00, '2024-01-05 10:30:00', 1),
(36, 8500.00, '2024-01-08 14:20:00', 1),
(48, 12000.00, '2024-01-10 09:15:00', 2),
(12, 3000.00, '2024-01-15 16:45:00', 3),
(60, 15000.00, '2024-01-18 11:30:00', 4),

-- Contratos de fevereiro de 2024
(24, 6200.00, '2024-02-02 08:20:00', 5),
(36, 9800.00, '2024-02-07 13:40:00', 6),
(48, 14500.00, '2024-02-12 10:25:00', 7),
(24, 7300.00, '2024-02-15 15:10:00', 8),
(12, 2500.00, '2024-02-20 09:50:00', 9),

-- Contratos de março de 2024
(36, 10500.00, '2024-03-01 11:15:00', 10),
(48, 18000.00, '2024-03-05 14:30:00', 11),
(24, 5500.00, '2024-03-10 08:45:00', 12),
(60, 22000.00, '2024-03-15 16:20:00', 13),
(12, 4000.00, '2024-03-20 10:10:00', 14),

-- Contratos de abril de 2024
(36, 11200.00, '2024-04-03 09:30:00', 15),
(48, 16500.00, '2024-04-08 13:15:00', 16),
(24, 6800.00, '2024-04-12 11:40:00', 17),
(60, 25000.00, '2024-04-18 15:25:00', 18),
(36, 9500.00, '2024-04-25 08:50:00', 19),

-- Contratos de maio de 2024
(24, 7200.00, '2024-05-02 10:20:00', 1),
(48, 13500.00, '2024-05-07 14:35:00', 2),
(36, 10800.00, '2024-05-12 09:15:00', 3),
(12, 3500.00, '2024-05-18 16:50:00', 4),
(60, 28000.00, '2024-05-25 11:30:00', 5),

-- Contratos de junho de 2024
(24, 8200.00, '2024-06-03 08:40:00', 6),
(36, 12500.00, '2024-06-10 13:20:00', 7),
(48, 19000.00, '2024-06-15 10:55:00', 8),
(24, 6500.00, '2024-06-20 15:15:00', 9),
(60, 30000.00, '2024-06-28 09:25:00', 10),

-- Contratos de julho de 2024
(36, 11800.00, '2024-07-05 11:45:00', 11),
(48, 17500.00, '2024-07-10 14:10:00', 12),
(24, 7800.00, '2024-07-15 08:30:00', 13),
(12, 4500.00, '2024-07-22 16:40:00', 14),
(60, 32000.00, '2024-07-28 10:50:00', 15),

-- Contratos de agosto de 2024
(24, 9200.00, '2024-08-02 09:20:00', 16),
(36, 13800.00, '2024-08-08 13:45:00', 17),
(48, 20000.00, '2024-08-15 11:25:00', 18),
(60, 35000.00, '2024-08-22 15:30:00', 19),
(24, 8500.00, '2024-08-29 08:15:00', 20),

-- Contratos de setembro de 2024
(36, 14200.00, '2024-09-05 10:40:00', 1),
(48, 21500.00, '2024-09-12 14:55:00', 2),
(24, 9800.00, '2024-09-18 09:30:00', 3),
(12, 5000.00, '2024-09-25 16:20:00', 4),
(60, 38000.00, '2024-09-30 11:10:00', 5),

-- Contratos de outubro de 2024
(24, 10500.00, '2024-10-05 08:50:00', 6),
(36, 15800.00, '2024-10-12 13:25:00', 7),
(48, 23000.00, '2024-10-18 10:15:00', 8),
(60, 40000.00, '2024-10-25 15:45:00', 9),
(24, 11200.00, '2024-10-30 09:35:00', 10);

-- =============================================
-- Consultas para verificar os dados inseridos
-- =============================================

-- SELECT COUNT(*) AS Total_Bancos FROM Tb_banco;
-- SELECT COUNT(*) AS Total_Convenios FROM Tb_convenio;
-- SELECT COUNT(*) AS Total_Convenio_Servicos FROM Tb_convenio_servico;
-- SELECT COUNT(*) AS Total_Contratos FROM Tb_contrato;

-- Consulta com todos os relacionamentos
-- SELECT 
--     ct.codigo AS Contrato,
--     ct.prazo AS Prazo_Meses,
--     ct.valor AS Valor_Contrato,
--     ct.data_inclusao AS Data_Inclusao,
--     cs.servico AS Servico,
--     cv.convenio AS Convenio,
--     b.nome AS Banco
-- FROM Tb_contrato ct
-- INNER JOIN Tb_convenio_servico cs ON ct.convenio_servico = cs.codigo
-- INNER JOIN Tb_convenio cv ON cs.convenio = cv.codigo
-- INNER JOIN Tb_banco b ON cv.banco = b.codigo
-- ORDER BY ct.data_inclusao DESC;


