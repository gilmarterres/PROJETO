// server.js
const express = require('express');
const sql = require('mssql');
const cors = require('cors');

const app = express();
const port = 3000;

const config = {
    user: 'sa',
    password: 'SuaSenhaForte!',
    server: 'localhost',
    database: 'db_checklist',
    options: {
        encrypt: false,
        trustServerCertificate: true
    }
};

app.use(cors());

app.get('/users', async (req, res) => {
    try {
        await sql.connect(config);
        const result = await sql.query('SELECT id, Name, Login, AccessLevel FROM tb_users');
        res.json(result.recordset);
    } catch (err) {
        console.error('Erro ao buscar usuários:', err);
        res.status(500).send('Erro no servidor ao buscar usuários');
    } finally {
        sql.close();
    }
});

app.listen(port, () => {
    console.log(`Servidor Node.js rodando em http://localhost:${port}`);
});