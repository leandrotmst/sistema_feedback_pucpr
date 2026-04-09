document.getElementById('entrar').addEventListener('click', ()=>{
    login();
});

document.getElementById('salvar').addEventListener('click', ()=>{
    var prova = document.getElementById('provaAutoria').value;
    if(!prova.includes('@')){
        alert('erro');
    }
    else{
        localStorage.setItem('prova', JSON.stringify(prova));
        alert('Prova de autoria salva localmente');
    }
});

async function login(){
    var email = document.getElementById('email').value;
    var senha = document.getElementById('senha').value;
    const fd = new FormData();
    fd.append("email", email);
    fd.append("senha", senha);

    const retorno = await fetch('../php/gestor_login.php',{
            method: "POST",
            body: fd
        }
    );
    const resposta = await retorno.json();
    if(resposta.status=='ok'){
        window.location.href = "../gestor/index.html";
    }else{
        alert('Credenciais inválidas');
    }
}