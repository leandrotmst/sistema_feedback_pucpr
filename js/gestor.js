document.addEventListener("DOMContentLoaded", ()=>{
    valida_gestor();
});

document.getElementById('sair').addEventListener('click', ()=>{
    logout();
});

async function logout(){
    const retorno = await fetch("../php/gestor_logout.php");
    const resposta = await retorno.json();
    if(resposta.status == "ok"){
        window.location.href = '../';   
    }
}