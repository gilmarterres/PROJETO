// --- DADOS DA TABELA DE COEFICIENTES (P, R, S, T, U) ---
const COEFICIENTES_TABELA = [
    { min: 0.0000, R: -0.0024620000, S: 0.0032150000, T: -0.0000101400, U: 0.0000173800 },
    { min: 0.4980, R: -0.0023910000, S: 0.0030740000, T: -0.0000084100, U: 0.0000139800 },
    { min: 0.5180, R: -0.0022940000, S: 0.0028870000, T: -0.0000083900, U: 0.0000138700 },
    { min: 0.5390, R: -0.0021460000, S: 0.0026150000, T: -0.0000054600, U: 0.0000085500 },
    { min: 0.5590, R: -0.0019200000, S: 0.0022140000, T: -0.0000055100, U: 0.0000085900 },
    { min: 0.5790, R: -0.0023580000, S: 0.0029620000, T: -0.0000122500, U: 0.0002015000 },
    { min: 0.6000, R: -0.0013610000, S: 0.0013000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6150, R: -0.0012370000, S: 0.0011000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6350, R: -0.0010770000, S: 0.0008500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6550, R: -0.0010110000, S: 0.0007500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6750, R: -0.0009770000, S: 0.0007000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.6950, R: -0.0010050000, S: 0.0007400000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7460, R: -0.0012380000, S: 0.0010500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7660, R: -0.0010840000, S: 0.0008500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.7860, R: -0.0009650000, S: 0.0007000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8060, R: -0.0008435000, S: 0.0005500000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8260, R: -0.0007190000, S: 0.0004000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8460, R: -0.0006170000, S: 0.0002800000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8710, R: -0.0005120000, S: 0.0001600000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.8960, R: -0.0003948000, S: 0.0003000000, T: -0.0000004900, U: 0.0000006000 },
    { min: 0.9960, R: -0.0005426000, S: 0.0001778000, T: 0.0000023100, U: -0.0000022000 }
].sort((a, b) => a.min - b.min);

/**
 * Função auxiliar que calcula as constantes V61, X61, W61, Y61 (dependem da Gravidade Observada).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @returns {object} Um objeto contendo as constantes V61, X61, W61, Y61, e Z61.
 */
function getCorrecaoConstantes(gravidadeObservada, tempAmostra) {
    // 1. Simulação do PROCV (VLOOKUP) com 'busca aproximada'
    let linhaCoeficiente = COEFICIENTES_TABELA[0];
    for (const row of COEFICIENTES_TABELA) {
        if (gravidadeObservada >= row.min) {
            linhaCoeficiente = row;
        } else {
            break;
        }
    }

    const R61 = linhaCoeficiente.R; // Coeficiente TAB1A
    const S61 = linhaCoeficiente.S; // Coeficiente TAB2A
    const T61 = linhaCoeficiente.T; // Coeficiente TAB1B
    const U61 = linhaCoeficiente.U; // Coeficiente TAB2B
    
    // Variável Delta T (usada apenas para Z61)
    const deltaT_amostra = tempAmostra - 20;

    // Denominador Comum para V61, X61, W61, Y61
    const denominadorComum = 1 + 8 * S61 + 64 * U61;

    // V61
    const numeradorV61 = R61 + 16 * T61 - (8 * R61 + 64 * T61) * U61;
    const V61 = (9 / 5) * 0.999042 * numeradorV61 / denominadorComum;

    // X61
    const numeradorX61 = T61 - (8 * R61 + 64 * T61) * U61;
    const X61 = (81 / 25) * 0.999042 * numeradorX61 / denominadorComum;

    // W61
    const W61 = (9 / 5) * (S61 + 16 * U61) / denominadorComum;

    // Y61
    const Y61 = (81 / 25) * U61 / denominadorComum;

    // Z61 (Calculado com base na temperatura da amostra)
    const Z61 = 1 - 0.000023 * deltaT_amostra - 0.00000002 * Math.pow(deltaT_amostra, 2);

    return { V61, X61, W61, Y61, Z61 };
}

// -----------------------------------------------------------
// 1. FUNÇÃO ANTERIOR (Gravity Corrected 20C - Q70)
// -----------------------------------------------------------

/**
 * Calcula a Gravidade Corrigida para 20°C (Gravity Corrected 20C - Q70).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @param {number} tempAmostra - A Temperatura da Amostra (O67).
 * @returns {number} O valor da Gravidade Corrigida a 20C (Q70).
 */
function calcularGravidadeCorrigida(gravidadeObservada, tempAmostra) {
    const { V61, X61, W61, Y61, Z61 } = getCorrecaoConstantes(gravidadeObservada, tempAmostra);
    const deltaT = tempAmostra - 20;

    // Q70 = (O66 - V61*deltaT - X61*deltaT^2) / (1 + W61*deltaT + Y61*deltaT^2) * Z61
    const numeradorQ70 = gravidadeObservada - V61 * deltaT - X61 * Math.pow(deltaT, 2);
    const denominadorQ70 = 1 + W61 * deltaT + Y61 * Math.pow(deltaT, 2);
    
    return (numeradorQ70 / denominadorQ70) * Z61;
}

// -----------------------------------------------------------
// 2. NOVA FUNÇÃO (Volume Conversion Factor - Q71)
// -----------------------------------------------------------

/**
 * Calcula o Fator de Conversão de Volume (Volume Conversion Factor - Q71).
 *
 * @param {number} gravidadeObservada - O valor da Gravidade Observada (O66/Q66).
 * @param {number} tempAmostra - A Temperatura da Amostra (O67).
 * @param {number} tempAmbiente - A Temperatura Ambiente (Q68).
 * @returns {number} O Fator de Conversão de Volume (Q71).
 */
function calcularFatorConversaoVolume(gravidadeObservada, tempAmostra, tempAmbiente) {
    // 1. Obter o resultado Q70
    const Q70 = calcularGravidadeCorrigida(gravidadeObservada, tempAmostra);
    
    // 2. Obter as constantes que dependem apenas da Gravidade (V61, X61, W61, Y61)
    const { V61, X61, W61, Y61 } = getCorrecaoConstantes(gravidadeObservada, tempAmostra);
    
    const deltaT_ambiente = tempAmbiente - 20;

    // Numerador Q71 (parte do termo 2): (V61*deltaT_ambiente + X61*deltaT_ambiente^2)
    const numerador_termo2 = V61 * deltaT_ambiente + X61 * Math.pow(deltaT_ambiente, 2);

    // Termo 1: (1 + W61*deltaT_ambiente + Y61*deltaT_ambiente^2)
    const termo1 = 1 + W61 * deltaT_ambiente + Y61 * Math.pow(deltaT_ambiente, 2);
    
    // Termo 2: (Numerador_termo2 / Q70)
    const termo2 = numerador_termo2 / Q70;

    // Q71 = Termo 1 + Termo 2
    return termo1 + termo2;
}

// --- EXEMPLO DE USO COM OS SEUS DADOS DE ENTRADA ---
const GRAVIDADE = 0.8760;        // Q66
const TEMPERATURA_AMOSTRA = 24.300; // Q67
const TEMPERATURA_AMBIENTE = 49.0;  // Q68

// A. Calculando a Gravidade Corrigida (Q70)
const Q70_RESULTADO = calcularGravidadeCorrigida(GRAVIDADE, TEMPERATURA_AMOSTRA);

// B. Calculando o Fator de Conversão de Volume (Q71)
const Q71_RESULTADO = calcularFatorConversaoVolume(GRAVIDADE, TEMPERATURA_AMOSTRA, TEMPERATURA_AMBIENTE);

console.log(`Gravidade Corrigida 20C (Q70): ${Q70_RESULTADO}`); 
console.log(`Fator de Conversão de Volume (Q71): ${Q71_RESULTADO}`);