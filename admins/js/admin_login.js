document.getElementById("login").addEventListener("click", () => {
    login_admin();
})

async function login_admin(){
    var usuario = document.getElementById('usuario').value;
    var senha = document.getElementById('senha').value;
    const fd = new FormData();
    fd.append("usuario", usuario);
    fd.append("senha", senha);

    const retorno = await fetch('../admins/php/admin_login.php',{
            method: "POST",
            body: fd
        }
    );
    const resposta = await retorno.json();
    if(resposta.status=='ok'){
        window.location.href = "home/admin.html";
    }else{
        alert('Credenciais inválidas');
    }
}