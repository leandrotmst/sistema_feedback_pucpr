document.addEventListener('DOMContentLoaded', () => {
    admin_valida_sessao();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "gestor_novo.html";
});

async function buscar(){
    const retorno = await fetch ("../php/gestores/gestor_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

async function excluir(id_gestor){
    if (!confirm("Deseja realmente excluir este gestor?")) return;
    
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
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th style="padding: 10px;">ID Gestor</th>
                    <th style="padding: 10px;">E-mail</th>
                    <th style="padding: 10px;">Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;"> ${tabela[i].id} </td>
                <td style="padding: 10px;"> ${tabela[i].email} </td>
                <td style="padding: 10px; display: flex; gap: 5px;">
                    <a href='gestor_alterar.html?id=${tabela[i].id}' class='btn btn-secondary'>Alterar</a>
                    <a href='#' onClick='excluir(${tabela[i].id})' class='btn btn-danger'>Excluir</a>
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