USE master;
GO

DROP DATABASE db_checklist;
GO

CREATE DATABASE db_checklist;
GO

USE db_checklist;
GO

DROP TABLE tb_users;
GO

create table tb_users (
id INT PRIMARY KEY IDENTITY(1,1),
name NVARCHAR(64) NOT NULL,
login NVARCHAR(50) NOT NULL,
password NVARCHAR(255) NOT NULL,
accesslevel INT NOT NULL
);
GO

INSERT INTO tb_users (name, login, password, accesslevel)
VALUES
    ('Gilmar Ferreira Terres Correa', 'gilmar', '123456', 0),
    ('Paulo Santos', 'paulo', '123456', 2),
    ('Francieli Lima Souza', 'fran', '123456', 1);
GO

SELECT * FROM db_checklist.dbo.tb_users;
GO

DROP TABLE tb_marking;
GO

CREATE TABLE tb_marking(
    id INT PRIMARY KEY IDENTITY(1,1),
    flow INT NOT NULL,
    circulacao NVARCHAR(64),
    produto NVARCHAR(64),
    transportadora NVARCHAR(64),
    nomeMotorista NVARCHAR(64),
    data NVARCHAR(64),
    placaCarreta NVARCHAR(64),
    cnhMotorista NVARCHAR(64),
    horaEntrada NVARCHAR(64),
    placaTanque1 NVARCHAR(64),
    destino NVARCHAR(64),
    responsavelBalanca NVARCHAR(64),
    placaTanque2 NVARCHAR(64),
    volumeCarreta NVARCHAR(64),
	farois NVARCHAR(8),
	vagoes NVARCHAR(8),
	cavalo NVARCHAR(8),
	extintores NVARCHAR(8),
	verificado NVARCHAR(8),
	lavar NVARCHAR(8),
	vedacao NVARCHAR(8),
	valvula NVARCHAR(8),
	transporte NVARCHAR(8),
	tubos NVARCHAR(8),
	carregamento NVARCHAR(8),
	responsavelExpedicao NVARCHAR(64),
	lacres NVARCHAR(64),
	obs NVARCHAR(64),
);
GO

SELECT * FROM db_checklist.dbo.tb_marking;
GO
