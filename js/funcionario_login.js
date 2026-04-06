document.getElementById('entrar').addEventListener('click', ()=>{
    login();
});

async function login(){
    var email = document.getElementById('email').value;
    var senha = document.getElementById('senha').value;
    const fd = new FormData();
    fd.append("email", email);
    fd.append("senha", senha);

    const retorno = await fetch('../php/funcionario_login.php',{
            method: "POST",
            body: fd
        }
    );
    const resposta = await retorno.json();
    if(resposta.status=='ok'){
        window.location.href = "../formulario/resposta_nova.html";
    }else{
        alert('Credenciais inválidas');
    }
}