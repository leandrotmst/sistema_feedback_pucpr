document.addEventListener('DOMContentLoaded', () => {
    admin_valida_sessao();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "../gestor/gestor_novo.html";
});

async function buscar(){
    const retorno = await fetch ("../php/gestores/gestor_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

async function excluir(id_gestor){
    const retorno = await fetch('../php/gestores/gestor_excluir.php?id='+id_gestor);
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        alert(resposta.mensagem);
        window.location.reload();
    }else{
        alert(resposta.mensagem);
    }
}

function preencherTabela(tabela){
    var html = `
        <table class="table-custom">
            <thead>
                <tr>
                    <th>ID Gestor</th>
                    <th>E-mail</th>
                    <th>Senha</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr>
                <td> ${tabela[i].id} </td>
                <td> ${tabela[i].email} </td>
                <td> ${tabela[i].senha} </td>
                <td>
                    <a href='gestor_alterar.html?id=${tabela[i].id}' class='btn-alterar'>Alterar</a>
                    <a href='#' onClick='excluir(${tabela[i].id})' class='btn-excluir'>Excluir</a>
                </td>
            </tr>
        `;
    }

    html += `
            </tbody>
        </table>
    `;
    document.getElementById('lista').innerHTML = html;
}