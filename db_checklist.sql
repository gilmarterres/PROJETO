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
Name NVARCHAR(255) NOT NULL,
Login NVARCHAR(50) NOT NULL,
PasswordHash NVARCHAR(255) NOT NULL,
AccessLevel INT NOT NULL
);
GO

INSERT INTO tb_users (Name, Login, PasswordHash, AccessLevel)
VALUES
    ('Maria Oliveira', 'maria.o', 'hash_senha_maria', 1),
    ('Carlos Santos', 'carlos.s', 'hash_senha_carlos', 2),
    ('Ana Souza', 'ana.s', 'hash_senha_ana', 1),
    ('Pedro Costa', 'pedro.c', 'hash_senha_pedro', 2);
GO

SELECT * FROM db_checklist.dbo.tb_users;