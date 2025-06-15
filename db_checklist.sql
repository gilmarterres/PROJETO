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
    ('Gilmar Ferreira Terres Correa', 'gilmar', '123456', 0),
    ('Carlos Santos', 'paulo', '123456', 2),
    ('Ana Souza', 'fran', '123456', 1);
GO

SELECT * FROM db_checklist.dbo.tb_users;
GO

DROP TABLE tb_marking;
GO

CREATE TABLE tb_marking(
    id INT PRIMARY KEY IDENTITY(1,1),
    flow INT NOT NULL,
	ticket INT NOT NULL,
    name_us_bal NVARCHAR(64) NOT NULL,
    plate NVARCHAR(32) NOT NULL,
    driver NVARCHAR(32) NOT NULL,    
    name_us_exp NVARCHAR(32),
    seals NVARCHAR(128)
);
GO

INSERT INTO tb_marking(flow, ticket, name_us_bal, plate, driver, name_us_exp, seals)
VALUES
    (1,78526, 'fran', 'XDS-7367','Antonio de oliveira','borges','6489458 548545 54854 51854 54843');
GO

SELECT * FROM db_checklist.dbo.tb_marking;
GO
