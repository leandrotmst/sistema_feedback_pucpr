document.addEventListener('DOMContentLoaded', () => {
    gestor_valida_sessao();
    buscar();
});

async function buscar(){
    const retorno = await fetch ("../php/gestor_resposta_get.php");
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
                    <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Equipe</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${tabela[i].texto}</td>
                <td class="px-6 py-4 text-gray-900">${tabela[i].emocional}</td>
                <td class="px-6 py-4 text-gray-900">${tabela[i].equipe}</td>
            </tr>
        `;
    }

    html += `
            </tbody>
        </table>
    `;
    document.getElementById('lista').innerHTML = html;
}