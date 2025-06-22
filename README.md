# Sistema de Automação de Checklist Para Carregamento de Combusíveis

## Sobre este TCC
Projeto desenvolvido como parte do Trabalho de Conclusão de Curso (TCC II) na Unicesumar, com objetivo de criar uma solução prática para automação do processo de checklist.

## Descrição do Projeto
Este projeto consite no desenvolvimento de um sistema web para automação de checklist de carregamento de combustíveis em caminhões. Objetivo principal e substituir o método manual baseado em folhas de papel para um sistema digital eficiente, visando melhorar o controle e rastreamento.

## Tecnologias Utilizadas
Para o desenvolvimento foi utilizado as seguintes teccnologias e ferramentas:

* **Back-end:** PHP Version 8.2.28
* **Servidor Web:** Microsoft-IIS/10.0
* **Front-end:** HTML, CSS, JavaScript
* **Banco de Dados:** SQL Server Express 2019
* **Ambiente de Desenvolvimento (IDE):** Visual Studio Code (VS Code)

## Como executar o projeto localmente

1. **Pré-Requisitos**
    * Servidor Web IIS configurado ou outro compatível com PHP.
    * PHP Version 8.2.25 instalado e configurado com IIS.
    * SQL Server Express 2019 instalado ou outra versão.
    * Driver PHP para SQL Server (php_sqlsrv) configurado.

2. **Configuração do Banco de Dados:**
    * Executar o script sql "db_checklist.sql" disponível na raiz do projeto, onde vai criar bancos e tabelas necessárias para correto funcionamento do sistema.
    * Atualiza as credenciais no arquivo de conexão "assets/connection.php".

3. **Configurações do servidor Web:**
    * Configurar para que o PHP funcione no IIS e também apontar para o diretório onde está o projeto.

4. **Acesso:**
    * Após configuração, acessar aplicação pelo navegador, geralmente em "http://localhost/" ou outro configurado.

# Autor
* **Gilmar Ferreira Terres Correa**
    * https://github.com/gilmarterres/
    * gilmarftc@gmail.com

# Status do Projeto
    * Em desenvolvimento