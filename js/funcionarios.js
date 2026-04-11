document.addEventListener('DOMContentLoaded', () => {
    gestor_valida_sessao();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "../funcionario/funcionario_novo.html";
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
                    <th>Equipe</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr>
                <td> ${tabela[i].email} </td>
                <td> ${tabela[i].senha} </td>
                <td> ${tabela[i].equipe} </td>
                <td class="flex gap-2">
                    <button onclick="window.location.href='../funcionario/funcionario_alterar.html?id=${tabela[i].id}'" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded transition duration-200">Alterar</button>
                    <button onclick="excluir(${tabela[i].id})" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded transition duration-200">Excluir</button>
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