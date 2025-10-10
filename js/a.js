console.log("Gilmar")

function tab1a(a){
let b = a;
let c = null;

if(b < 0.498){
    c = -0.002462;
} else if(b < 0.518){
    c = -0.0023910;
} else if(b < 0.539){
    c = -0.0022940;
} else if(b < 0.559){
    c = -0.0021460;
} else if(b < 0.579){
    c = -0.0019200;
} else if(b < 0.600){
    c = -0.0023580;
} else if(b < 0.615){
    c = -0.0013610;
} else if(b < 0.635){
    c = -0.0012370;
} else if(b < 0.655){
    c = -0.0010770;
} else if(b < 0.675){
    c = -0.0010110;
} else if(b < 0.695){
    c = -0.0009770;
} else if(b < 0.746){
    c = -0.0010050;
} else if(b < 0.766){
    c = -0.0012380;
} else if(b < 0.786){
    c = -0.0010840;
} else if(b < 0.806){
    c = -0.0009650;
} else if(b < 0.826){
    c = -0.0008435;
} else if(b < 0.846){
    c = -0.0007190;
} else if(b < 0.871){
    c = -0.0006170;
} else if(b < 0.896){
    c = -0.0005120;
} else if(b < 0.996){
    c = -0.0003948;
} else if(b < 2.000){
    c = -0.0005426;
} else {
   c = 0.0;
}

return c;

}

/////////////////////////////////////////

function tab2a(a){
    let b = a;
    let c = null;

    if(b < 0.498){
        c = 0.0032150; // TAB2A, 1ª linha
    } else if(b < 0.518){
        c = 0.0030740; // TAB2A, 2ª linha
    } else if(b < 0.539){
        c = 0.0028870; // TAB2A, 3ª linha
    } else if(b < 0.559){
        c = 0.0026150; // TAB2A, 4ª linha
    } else if(b < 0.579){
        c = 0.0022140; // TAB2A, 5ª linha
    } else if(b < 0.600){
        c = 0.0029620; // TAB2A, 6ª linha
    } else if(b < 0.615){
        c = 0.0013000; // TAB2A, 7ª linha
    } else if(b < 0.635){
        c = 0.0011000; // TAB2A, 8ª linha
    } else if(b < 0.655){
        c = 0.0008500; // TAB2A, 9ª linha
    } else if(b < 0.675){
        c = 0.0007500; // TAB2A, 10ª linha
    } else if(b < 0.695){
        c = 0.0007000; // TAB2A, 11ª linha
    } else if(b < 0.746){
        c = 0.0007400; // TAB2A, 12ª linha
    } else if(b < 0.766){
        c = 0.0010500; // TAB2A, 13ª linha
    } else if(b < 0.786){
        c = 0.0008500; // TAB2A, 14ª linha
    } else if(b < 0.806){
        c = 0.0007000; // TAB2A, 15ª linha
    } else if(b < 0.826){
        c = 0.0005500; // TAB2A, 16ª linha
    } else if(b < 0.846){
        c = 0.0004000; // TAB2A, 17ª linha
    } else if(b < 0.871){
        c = 0.0002800; // TAB2A, 18ª linha
    } else if(b < 0.896){
        c = 0.0001600; // TAB2A, 19ª linha
    } else if(b < 0.996){
        c = 0.0000300; // TAB2A, 20ª linha
    } else if(b < 2.000){
        c = 0.0001778; // TAB2A, 21ª linha (a última na tabela original)
    } else {
        // Caso b seja 2.000 ou maior, ou fora do primeiro limite (0.00000 a 0.40000)
        c = 0.0;
    }

    return c;
}

/////////////////////////////////////////

function tab1b(a){
    let b = a;
    let c = null;

    if(b < 0.498){
        c = -0.000010140; // TAB1B, 1ª linha
    } else if(b < 0.518){
        c = -0.000008410; // TAB1B, 2ª linha
    } else if(b < 0.539){
        c = -0.000008390; // TAB1B, 3ª linha
    } else if(b < 0.559){
        c = -0.000005460; // TAB1B, 4ª linha
    } else if(b < 0.579){
        c = -0.000005510; // TAB1B, 5ª linha
    } else if(b < 0.600){
        c = -0.0000012250; // TAB1B, 6ª linha
    } else if(b < 0.615){
        c = -0.0000000490; // TAB1B, 7ª linha
    } else if(b < 0.635){
        c = -0.0000000490; // TAB1B, 8ª linha
    } else if(b < 0.655){
        c = -0.0000000490; // TAB1B, 9ª linha
    } else if(b < 0.675){
        c = -0.0000000490; // TAB1B, 10ª linha
    } else if(b < 0.695){
        c = -0.0000000490; // TAB1B, 11ª linha
    } else if(b < 0.746){
        c = -0.0000000490; // TAB1B, 12ª linha
    } else if(b < 0.766){
        c = -0.0000000490; // TAB1B, 13ª linha
    } else if(b < 0.786){
        c = -0.0000000490; // TAB1B, 14ª linha
    } else if(b < 0.806){
        c = -0.0000000490; // TAB1B, 15ª linha
    } else if(b < 0.826){
        c = -0.0000000490; // TAB1B, 16ª linha
    } else if(b < 0.846){
        c = -0.0000000490; // TAB1B, 17ª linha
    } else if(b < 0.871){
        c = -0.0000000490; // TAB1B, 18ª linha
    } else if(b < 0.896){
        c = -0.0000000490; // TAB1B, 19ª linha
    } else if(b < 0.996){
        c = -0.0000000490; // TAB1B, 20ª linha
    } else if(b < 2.000){
        c = 0.0000002310; // TAB1B, 21ª linha (a última na tabela original)
    } else {
        // Caso b seja 2.000 ou maior, ou fora do primeiro limite.
        c = 0.0;
    }

    return c;
}

/////////////////////////////////////////
function tab2b(a){
    let b = a;
    let c = null;

    if(b < 0.498){
        c = 0.00001738; // TAB2B, 1ª linha
    } else if(b < 0.518){
        c = 0.00001398; // TAB2B, 2ª linha
    } else if(b < 0.539){
        c = 0.00001387; // TAB2B, 3ª linha
    } else if(b < 0.559){
        c = 0.00000855; // TAB2B, 4ª linha
    } else if(b < 0.579){
        c = 0.00000855; // TAB2B, 5ª linha
    } else if(b < 0.600){
        c = 0.00020150; // TAB2B, 6ª linha
    } else if(b < 0.615){
        c = 0.00000060; // TAB2B, 7ª linha
    } else if(b < 0.635){
        c = 0.00000060; // TAB2B, 8ª linha
    } else if(b < 0.655){
        c = 0.00000060; // TAB2B, 9ª linha
    } else if(b < 0.675){
        c = 0.00000060; // TAB2B, 10ª linha
    } else if(b < 0.695){
        c = 0.00000060; // TAB2B, 11ª linha
    } else if(b < 0.746){
        c = 0.00000060; // TAB2B, 12ª linha
    } else if(b < 0.766){
        c = 0.00000060; // TAB2B, 13ª linha
    } else if(b < 0.786){
        c = 0.00000060; // TAB2B, 14ª linha
    } else if(b < 0.806){
        c = 0.00000060; // TAB2B, 15ª linha
    } else if(b < 0.826){
        c = 0.00000060; // TAB2B, 16ª linha
    } else if(b < 0.846){
        c = 0.00000060; // TAB2B, 17ª linha
    } else if(b < 0.871){
        c = 0.00000060; // TAB2B, 18ª linha
    } else if(b < 0.896){
        c = 0.00000060; // TAB2B, 19ª linha
    } else if(b < 0.996){
        c = 0.00000060; // TAB2B, 20ª linha
    } else if(b < 2.000){
        c = -0.00000220; // TAB2B, 21ª linha (a última na tabela original)
    } else {
        // Caso b seja 2.000 ou maior, ou fora do primeiro limite.
        c = 0.0;
    }

    return c;
}