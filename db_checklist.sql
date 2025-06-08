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
name NVARCHAR(255) NOT NULL,
login NVARCHAR(50) NOT NULL,
password NVARCHAR(255) NOT NULL,
accesslevel INT NOT NULL
);
GO

INSERT INTO tb_users (name, login, password, accesslevel)
VALUES
    ('Gilmar Ferreira Terres Correa', 'gilmar', '123456', 1),
    ('Carlos Santos', 'carlos.s', '123456', 2),
    ('Ana Souza', 'ana.s', '123456', 1),
    ('Pedro Costa', 'pedro.c', '123456', 2);
GO

SELECT * FROM db_checklist.dbo.tb_users;