document.addEventListener('DOMContentLoaded', () => {
    // valida_sessao ja e feito pelo analista_dados_valida_sessao.js
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "solucao_nova.html";
});

async function buscar(){
    const retorno = await fetch ("../php/solucao_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

async function excluir(id_solucao){
    const retorno = await fetch('../php/solucao_excluir.php?id='+id_solucao);
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
        <table class="w-full text-sm text-left border-collapse border border-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Título</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Equipe Alvo</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${tabela[i].titulo}</td>
                <td class="px-6 py-4 text-gray-900">${tabela[i].equipe}</td>
                <td class="px-6 py-4 flex gap-2">
                    <button onclick="window.location.href='solucao_alterar.html?id=${tabela[i].id}'" class='btn btn-secondary'">Alterar</button>
                    <button onclick="excluir(${tabela[i].id})" class='btn btn-danger' ">Excluir</button>
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
