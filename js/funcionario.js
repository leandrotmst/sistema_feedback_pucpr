document.addEventListener('DOMContentLoaded', () => {
    valida_gestor();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "funcionario_novo.html";
});

async function buscar(){
    const retorno = await fetch ("../php/funcionario_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

async function excluir(id_funcionario){
    const retorno = await fetch('../php/funcionario_excluir.php?id='+id_funcionario);
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
                    <th>E-mail</th>
                    <th>Senha</th>
                    <th>ID Gestor</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr>
                <td> ${tabela[i].email} </td>
                <td> ${tabela[i].senha} </td>
                <td>
                    <a href='usuario_alterar.html?id_usuario=${tabela[i].id}' class='btn-alterar'>Alterar</a>
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