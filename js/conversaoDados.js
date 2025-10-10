let v1 = 0.02;
let v2 = 0.01;
console.log(v1+v2);



//////////////////////////////////////////////////////////////////////////
const Q66 = Number(window.APP_CONFIG.densidade);
const Q67 = Number(window.APP_CONFIG.temperaturaAmostra);
const Q68 = Number(window.APP_CONFIG.temperaturaCarreta);

 console.log("Densidade: "+Q66);
 console.log("Temperatura da amostra: "+Q67);
 console.log("Temperatura da carreta: "+Q68);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const bb = Q66;
const R61 = tab1a(bb);
const S61 = tab2a(bb);
const T61 = tab1b(bb);
const U61 = tab2b(bb);
console.log("tab1a: " + R61);
console.log("tab2a: " + S61);
console.log("tab1b: " + T61);
console.log("tab2b: " + U61);

//////  
let V61 = (9/5) * 0.999042 * (R61 + 16 * T61 - (((8 * R61 + 64 * T61 ) * (S61 + 16 * U61))/( 1 + 8 * S61 + 64 * U61 )));
console.log("P1: " + V61);

let W61 = (9/5)*(S61+16*U61)/(1+8*S61+64*U61);
console.log("P2: " + W61);

let X61 = 81/25*0.999042*(T61-((8*R61+64*T61)*U61)/(1+8*S61+64*U61));
console.log("P3: " + X61);

let Y61 = 81/25*(U61/(1+8*S61+64*U61));
console.log("P4: " + Y61);

let Z61 = 1-0.000023 * (Q67-20) -0.00000002 * ((Q67-20) ** 2);
console.log("hyc: " + Z61);

let Q70 =(Q66-V61*(Q67-20)-X61*((Q67-20)**2))/(1+W61*(Q67-20)+Y61*((Q67-20)**2))*Z61;
console.log("Gravity Corrected 20oC: " + Q70);

let Q71 = 1+W61*(Q68-20)+Y61*((Q68-20)**2)+(V61*(Q68-20)+X61*((Q68-20)**2))/Q70;
console.log("Volume Conversion Factor: " + Q71);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function atualizarValores() {

    //dados fixos
    const densidade20 = Q70;
    const fatorCorrecao20 = Q71;

    //captura valores de densidade, temperaturas e volume da carreta.
    // const volumeCarreta = <?php echo json_encode($dados_expedicao['volumeCarreta'] ?? '-'); ?>;
    // const densidade = <?php echo json_encode($dados_expedicao['densidade'] ?? '-'); ?>;
    // const temperaturaAmostra = <?php echo json_encode($dados_expedicao['temperaturaAmostra'] ?? '-'); ?>;
    // const temperaturaCarreta = <?php echo json_encode($dados_expedicao['temperaturaCarreta'] ?? '-'); ?>;

    const volumeCarreta = window.APP_CONFIG.volumeCarreta;
    const densidade = window.APP_CONFIG.densidade;
    const temperaturaAmostra = window.APP_CONFIG.temperaturaAmostra;
    const temperaturaCarreta = window.APP_CONFIG.temperaturaCarreta;
    
    // 1. Pega o elemento de entrada (MASSA DA CARRETA)
    const massaCarretaInput = document.getElementById('massaCarreta');
    
    // 2. Pega o valor digitado
    const novoValor = massaCarretaInput.value;

    // 3. Pega os elementos que precisam ser atualizados
    const densidadeA = document.getElementById('densidade');
    const fatorCorrecaoA = document.getElementById('fatorCorrecao');
    densidadeA.textContent = densidade20;
    fatorCorrecaoA.textContent = fatorCorrecao20;

    const volumeConvertido = document.getElementById('volumeConvertido');
    const volumeConvertidoBalanca = document.getElementById('volumeConvertidoBalanca');
    const deltaVolume = document.getElementById('deltaVolume');
    const valorEmbarque = document.getElementById('valorEmbarque');

    // 4. Atualiza o conteúdo de cada elemento com o novo valor
    // Se o campo estiver vazio, podemos exibir um texto padrão ou um zero
    const valorFormatado = novoValor === '' ? '0' : novoValor;

    //cáuculos de conversão:
    calcVolumeConvertidoBalanca = novoValor/densidade20;

    calcVolumeConvertido = (volumeCarreta/1000*fatorCorrecao20);
    valorComPonto = calcVolumeConvertido.toFixed(3);
    valorFinal = valorComPonto.replace('.', ',');

    calcVolume = (calcVolumeConvertidoBalanca-(calcVolumeConvertido*1000));

    //valor embarque
    const resultado = Math.round(calcVolumeConvertido * 1000);
    const calcValorEmbarque = (resultado * 0.85) - novoValor;

    //////////////////////////////////////////////////////////
    //volume convertido
    volumeConvertido.textContent = valorFinal;
    //Volume convertido balança
    volumeConvertidoBalanca.textContent = Math.trunc(calcVolumeConvertidoBalanca);
    // delta volume
    deltaVolume.textContent = Math.trunc(calcVolume);

    valorEmbarque.textContent = Math.trunc(calcValorEmbarque);
    //////////////////////////////////////////////////////////

    //trocar de cor
    const v1 = volumeCarreta * 0.005;
    const v2 = volumeCarreta * -0.005;

    console.log(v1);
    console.log(v2);
    console.log(calcVolume);


    const divResultado = document.getElementById('resultadoConversao');

    if (calcVolume < v1 && calcVolume > v2){
        divResultado.style.backgroundColor = 'blue';
        console.log("azul");
    }else{
        console.log("vermelho");
        divResultado.style.backgroundColor = 'red';
    }

}
