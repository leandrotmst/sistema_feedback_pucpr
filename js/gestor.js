class Gestor {
    async valida_sessao(){
        const retorno = await fetch("../php/valida_sessao.php");
        const resposta = await retorno.json();
        if(resposta.status === "nok"){
            window.location.href = '../login/';
        }    
    }

    async login() {
        const email = document.getElementById("email").value;
        const senha   = document.getElementById("senha").value;
        
        const fd = new FormData();
        fd.append("email", email);
        fd.append("senha", senha);
        
        const retorno = await fetch("../php/gestor_login.php", {
                method: "POST",
                body: fd
            }
        );
        const resposta = await retorno.json();
        if(resposta.status === "ok"){
            window.location.href = "../home/";
        } else{
            alert("Credenciais inválidas.");
        }
    }

    async novo() {
        const email = document.getElementById("email").value;
        const senha = document.getElementById("senha").value;

        const fd = new FormData();
        fd.append("email", email);
        fd.append("senha", senha);
    
        const retorno = await fetch("../php/gestor_novo.php",
            {
              method: 'POST',
              body: fd
            });
        const resposta = await retorno.json();

        if(resposta.status === "ok"){
            alert("SUCESSO: " + resposta.mensagem);
            window.location.href = "../home/";
        }else{
            alert("ERRO: " + resposta.mensagem);
        }
    }
    
    async alterar(){
        const senha   = document.getElementById("senha").value;
        const email   = document.getElementById("email").value;
        const id      = document.getElementById("id").value;

        const fd = new FormData();
        fd.append("senha", senha);
        fd.append("email", email);
    
        const retorno = await fetch("../php/gestor_alterar.php?id="+id,
            {
              method: 'POST',
              body: fd  
            });
        const resposta = await retorno.json();
        if(resposta.status === "ok"){
            alert("SUCESSO: " + resposta.mensagem);
            window.location.href = '../home/'
        }else{
            alert("ERRO: " + resposta.mensagem);
        }    
    }

    async buscarLista() {
        const retorno = await fetch("../php/gestor_get.php");
        const resposta = await retorno.json();

        if(resposta.status === "ok"){
            this.preencherTabela(resposta.data);    
        }
    }

    async excluir(id) {
        const retorno = await fetch("../php/gestor_excluir.php?id="+id);
        const resposta = await retorno.json();

        if(resposta.status === "ok"){
            alert("SUCESSO: " + resposta.mensagem);
            window.location.reload();
        }else{
            alert("ERRO: " + resposta.mensagem);
        } 
    }

    async logoff() {
        const retorno  = await fetch("../php/gestor_logoff.php");
        const resposta = await retorno.json();

        if (resposta.status === "ok") {
            window.location.href = "../login/";
        }
    }

    preencherTabela(tabela) {
        let html = `
            <table>
                <tr>
                    <th> E-mail </th>
                    <th> Senha </th>
                    <th> # </th>
                </tr>
        `;
        for (let i = 0; i < tabela.length; i++) {
            html += `
                <tr>
                    <td>${tabela[i].email}</td>
                    <td>${tabela[i].senha}</td>
                    <td>
                        <a href='gestor_alterar.html?id=${tabela[i].id}'>Alterar</a>
                        <a href='#' data-id='${tabela[i].id}' class='link-excluir'>Excluir</a>
                    </td>
                </tr>
            `;
        }
        html += "</table>";
        document.getElementById("lista").innerHTML = html;
        
        // adiciona eventos de excluir nos links criados dinamicamente
        document.querySelectorAll(".link-excluir").forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                const id = link.getAttribute("data-id");
                this.excluir(id);
            });
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const gestor = new Gestor();

    const formLogin = document.getElementById("form-login");
    if (formLogin) {
        formLogin.addEventListener("submit", (event) => {
            event.preventDefault();
            gestor.login();
        });
    }

    if (document.getElementById("form-gestor-alterar")) {
        gestor.valida_sessao();
        gestor.carregarGestorParaEdicao();
        document.getElementById("enviar").addEventListener("click", () => {
            gestor.alterar();
        });
    }

    if (document.getElementById("lista")) {
        gestor.valida_sessao();
        gestor.buscarLista();
        
        const btnNovo = document.getElementById("novo");
        if (btnNovo) {
            btnNovo.addEventListener("click", () => {
                window.location.href = "cliento_novo.html";
            });
        }

        const btnLogoff = document.getElementById("logoff");
        if (btnLogoff) {
            btnLogoff.addEventListener("click", () => {
                gestor.logoff();
            })
        }
    }
});