console.log("Gilmar");

const btNew= document.getElementById("bt_new");
if (btNew){
btNew.addEventListener("click", function(){
    window.location.href = "../pages/new_checklist.php";
});
}

const btConsult = document.getElementById("bt_consult");
if (btConsult){
btConsult.addEventListener("click", function(){
    window.location.href = "../pages/consult_checklist.php";
});
}

const btBack = document.getElementById("bt_back");
if (btBack){
    btBack.addEventListener("click", function(){
        window.location.href = "../pages/balance.php";    
});
}

const btLgscreen = document.getElementById("bt_lg_screen");
if (btLgscreen){
    btLgscreen.addEventListener("click", function(){
        window.location.href = "../index.html";    
});
}

const btbkexp = document.getElementById("bt_bk_exp");
if (btbkexp){
    btbkexp.addEventListener("click", function(){
        console.log("Voltar")
        window.location.href = "./expedition.php";    
});
}

