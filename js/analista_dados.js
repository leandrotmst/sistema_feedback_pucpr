document.addEventListener('DOMContentLoaded', () => {
    gestor_valida_sessao();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "../analista_dados/analista_dados_novo.html";
});

async function buscar(){
    const retorno = await fetch ("../php/analista_dados_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

async function excluir(id_analista_dados){
    const retorno = await fetch('../php/analista_dados_excluir.php?id='+id_analista_dados);
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
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">E-mail</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${tabela[i].email}</td>
                <td class="px-6 py-4 flex gap-2">
                    <button onclick="window.location.href='../analista_dados/analista_dados_alterar.html?id=${tabela[i].id}'" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded transition duration-200">Alterar</button>
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