 <?php
    session_start();

    if (isset($_SESSION['message'])) {
        echo "<script>";
        echo "alert('Dados inseridos com sucesso!');";
        echo "</script>";
        unset($_SESSION['message']);
    }
    ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
    
    <style>
        /* Estilos Padrão para o Layout do Login */
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body.index {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #bbb; /* Cor de fundo atual */
            min-height: 100vh;
            margin: 0;
        }

        /* Container principal que agrupa a Logo e o Formulário */
/* Container principal que agrupa a Logo e o Formulário */
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: -14vh; /* <--- ADICIONE ESTA LINHA */
            /* -10vh significa "subir" 10% da altura da viewport (tela) */
        }

        /* Estilo da Logo */
        .login-logo {
            width: 350px; /* Tamanho da logo: Ajuste conforme necessário */
            margin-bottom: 25px; /* Espaçamento entre a logo e a caixa de login */
        }
        
        /* Estilo do Formulário */
        form {
            width: 300px;
            padding: 30px; /* Adicionado padding para criar a caixa branca */
            background-color: #fff; /* Fundo branco para a caixa de login */
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Sombra para destacar */
            text-align: center;
            /* Removido o 'margin: 100px;' para usar o flexbox do body */
        }

        h1 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }

        input {
            display: block;
            margin: 10px auto;
            width: 250px;
            height: 40px; /* Aumentado a altura para melhor usabilidade */
            padding: 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .bt-logar {
            background-color: #4CAF50; /* Um verde sólido */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .bt-logar:hover {
            background-color: #45a049;
        }

/* Estilo do Teclado Numérico Virtual */
.teclado-numerico {
    position: fixed; /* Fixa na tela */
    top: 50%;
    right: 20px;
    transform: translateY(-50%); /* Centraliza verticalmente */
    width: 250px; /* Largura do teclado */
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000; /* Garante que fique acima de outros elementos */
    display: none; /* COMEÇA OCULTO */
}

.teclado-numerico.ativo {
    display: block; /* Torna-se visível quando 'ativo' */
}

.teclado-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
    background-color: #f7f7f7;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

#tecladoDisplay {
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
}

.teclado-fechar {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 1em;
}

.teclado-botoes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    padding: 10px;
}

.teclado-btn {
    padding: 15px 0;
    font-size: 1.2em;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
    cursor: pointer;
    transition: background-color 0.1s;
    user-select: none; /* Impede a seleção de texto ao tocar */
}

.teclado-btn:active {
    background-color: #e0e0e0;
}

.teclado-btn.apagar, .teclado-btn.limpar {
    background-color: #f39c12;
    color: white;
}

.teclado-btn.enter {
    background-color: #2ecc71;
    color: white;
    font-weight: bold;
    grid-column: span 1; /* Ocupa uma coluna */
}

.teclado-btn.zero-duplo {
    grid-column: span 1;
}

/* Atualize estes estilos */
.teclado-numerico {
    position: fixed;
    /* top, right e transform serão manipulados pelo JS de arrastar */
    width: 250px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    cursor: move; /* Indica que o teclado é arrastável */
    
    /* Posição inicial (pode ser qualquer valor que você queira) */
    top: 150px; 
    right: 50px;
}

/* Ajustes no CSS para o novo botão fechar */
.teclado-btn.teclado-fechar {
    background: #e74c3c;
    color: white;
    font-size: 1.2em;
}

/* O resto do .teclado-botoes e .teclado-btn permanece igual */

/* Cor base dos botões: Fundo mais claro para contraste */
.teclado-btn {
    padding: 15px 0;
    font-size: 1.3em; /* Aumentado a fonte para melhor toque */
    border: 1px solid #005600; /* Borda forte para destacar */
    border-radius: 6px; /* Levemente mais arredondado */
    background-color: #e6ffe6; /* Verde muito claro */
    color: #000000; /* Cor preta para o texto/número */
    cursor: pointer;
    transition: background-color 0.1s;
    user-select: none;
    font-weight: bold; /* Números mais fortes */
}

/* Estado de clique/toque */
.teclado-btn:active {
    background-color: #ccffcc; /* Escurece um pouco ao ser tocado */
}

/* Botões de Ação Secundária (Limpar e Apagar) */
.teclado-btn.apagar, .teclado-btn.limpar {
    background-color: #ff9900; /* Laranja VIVO */
    color: white;
    border-color: #cc7a00;
}

/* Botão ENTER (Ação Principal/Positiva) */
.teclado-btn.enter {
    background-color: #27ae60; /* Um verde forte e sólido (tom da sua logo) */
    color: white;
    font-weight: bold;
    grid-column: span 1; 
    border-color: #1a7a44;
}

/* Botão Fechar (X) - Ação Negativa */
.teclado-btn.teclado-fechar {
    background: #c0392b; /* Vermelho forte para indicar fechar */
    color: white;
    font-size: 1.2em;
    border-color: #8c2a1f;
}

/* O resto do .teclado-botoes e .teclado-numerico permanece igual */




    </style>

   
</head>

<body class="index">
    
    <div class="login-container">
        
        <img src="logo.png" alt="Biopower Logo" class="login-logo">
        
        <form action="assets/login.php" method="POST">
            <h1>Login</h1>
            <input type="text" placeholder="login" id="login" name="login" autocomplete="off" class="input-numerico-virtual">
            <input type="password" placeholder="senha" id="senha" name="senha" autocomplete="off" class="input-numerico-virtual">
            <input class="bt-logar" type="submit" value="Logar">
        </form>
    </div>













<div id="tecladoNumerico" class="teclado-numerico">
    <div class="teclado-botoes">
        <button class="teclado-btn numero">1</button>
        <button class="teclado-btn numero">2</button>
        <button class="teclado-btn numero">3</button>
        <button id="tecladoFechar" class="teclado-btn teclado-fechar">X</button> <button class="teclado-btn numero">4</button>
        <button class="teclado-btn numero">5</button>
        <button class="teclado-btn numero">6</button>
        <button class="teclado-btn limpar">C</button>
        
        <button class="teclado-btn numero">7</button>
        <button class="teclado-btn numero">8</button>
        <button class="teclado-btn numero">9</button>
        <button class="teclado-btn apagar">←</button>
        
        <button class="teclado-btn numero-grande numero zero-duplo">00</button>
        <button class="teclado-btn numero zero">0</button>
        <button class="teclado-btn decimal">.</button>
        <button class="teclado-btn enter">OK</button>
    </div>
</div>




<script>
    // ===================================================================
    // LÓGICA DO TECLADO NUMÉRICO VIRTUAL (TOUCHSCREEN) ARRASTÁVEL
    // (Versão para a página de login)
    // ===================================================================
    const teclado = document.getElementById('tecladoNumerico');
    const botaoFechar = document.getElementById('tecladoFechar');
    const botoes = teclado.querySelectorAll('.teclado-btn');
    
    let campoAtivo = null; 
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    
    // --- 1. DEFINIÇÃO DA ORDEM DOS CAMPOS PARA LOGIN ---
    const ORDEM_CAMPOS = [
        'login',
        'senha'
    ];
    
    // Identifica SOMENTE os campos que existem nesta página e têm a classe
    const camposParaTeclado = document.querySelectorAll('.input-numerico-virtual');
    
    if (teclado && camposParaTeclado.length > 0) {
        
        // --- LÓGICA DE ARRASTAR (DRAGGABLE) ---
        teclado.onmousedown = dragMouseDown; 
    
        function dragMouseDown(e) {
            e = e || window.event;
            if (e.target.closest('.teclado-btn')) return; 
            
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            
            teclado.style.top = (teclado.offsetTop - pos2) + "px";
            teclado.style.left = (teclado.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
        
        // --- 2. FUNÇÕES DE NAVEGAÇÃO E FECHAMENTO ---
        
        function irParaProximoCampo(event) {
            teclado.classList.remove('ativo');
            
            if (campoAtivo) {
                const campoID = campoAtivo.id;
                const currentIndex = ORDEM_CAMPOS.indexOf(campoID);
                
                campoAtivo.blur(); 
                campoAtivo = null;

                if (currentIndex !== -1 && currentIndex < ORDEM_CAMPOS.length - 1) {
                    // Se não for o último campo, foca no próximo com um pequeno delay
                    const proximoID = ORDEM_CAMPOS[currentIndex + 1];
                    const proximoCampo = document.getElementById(proximoID);
                    
                    if (proximoCampo) {
                        setTimeout(() => {
                            proximoCampo.focus();
                        }, 20); 
                    }
                } else {
                    // Se for o último campo (senha), envia o formulário automaticamente.
                    // Opcional: Para evitar envio acidental, comente as duas linhas abaixo.
                    const form = document.querySelector('form');
                    if (form) form.submit();
                }
            }
        }
        
        function fecharTecladoSemAvancar() {
            teclado.classList.remove('ativo');
            if (campoAtivo) {
                campoAtivo.blur();
            }
            campoAtivo = null;
        }

        // --- 3. LÓGICA DE EVENTOS (FOCUS E TECLAS) ---
        camposParaTeclado.forEach(input => {
            input.addEventListener('focus', function() {
                campoAtivo = this;
                teclado.classList.add('ativo');
            });
            // Não precisa de blur complexo na página de login, o foco natural já funciona.
        });

        // Mapeamento dos botões:
        teclado.querySelector('.enter').addEventListener('click', irParaProximoCampo);
        botaoFechar.addEventListener('click', fecharTecladoSemAvancar);

        
        // Processa o toque nas teclas
        botoes.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!campoAtivo) return; 
                e.preventDefault();
                
                const tecla = this.textContent.trim();
                let valorAtual = campoAtivo.value;
                
                // Na página de login, não há decimais, então simplificamos a lógica.
                if (this.classList.contains('numero')) {
                    campoAtivo.value = valorAtual + tecla;
                    
                } else if (this.classList.contains('apagar')) {
                    campoAtivo.value = valorAtual.substring(0, valorAtual.length - 1);

                } else if (this.classList.contains('limpar')) {
                    campoAtivo.value = '';
                }
            });
        });
    } // Fim do if(teclado && camposParaTeclado.length > 0)
</script>


</body>

</html>