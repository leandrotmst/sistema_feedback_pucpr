document.addEventListener('DOMContentLoaded', () => {
    funcionario_valida_sessao();
    buscar();
});

document.getElementById('novo').addEventListener('click', () => {
    window.location.href = "../formulario/resposta_nova.html";
});

async function buscar(){
    const retorno = await fetch ("../php/resposta_get.php");
    const resposta = await retorno.json();

    if(resposta.status=='ok'){
        preencherTabela(resposta.data);
    }
}

function preencherTabela(tabela){
    var html = `
        <table class="w-full text-sm text-left border-collapse border border-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Texto</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Emocional</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">E-mail do funcionário</th>
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Ações</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${tabela[i].texto}</td>
                <td class="px-6 py-4 text-gray-900">${tabela[i].emocional}</td>
                <td class="px-6 py-4 text-gray-900">${tabela[i].email_do_funcionario}</td>
                <td class="px-6 py-4">
                    <button onclick="window.location.href='../formulario/resposta_alterar.html?id=${tabela[i].id}'" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded transition duration-200">Alterar</button>
                    <!-- // Comentado o funcionamento abaixo -->
                    <!-- a href='#' onClick='excluir(${tabela[i].id})' class='btn-excluir'>Excluir</a -->
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