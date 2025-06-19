USE master;
GO

DROP DATABASE db_checklist;
GO

CREATE DATABASE db_checklist;
GO

USE db_checklist;
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
    ('Franciane Ana Souza', 'fran', '123456', 1);
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
    responsavelExpedicao NVARCHAR(64),
    laudo NVARCHAR(64),
    lacres NVARCHAR(128)
);
GO

INSERT INTO tb_marking(flow, circulacao, produto, transportadora, nomeMotorista, data, 
                        placaCarreta, cnhMotorista, horaEntrada, placaTanque1, destino, responsavelBalanca,
                        placaTanque2, volumeCarreta, laudo, lacres
                        )
VALUES
    (1,78526, 'fran', 'XDS-7367','Antonio de oliveira','borges','6489458 548545 54854 51854 54843');
GO

SELECT * FROM db_checklist.dbo.tb_marking;
GO
