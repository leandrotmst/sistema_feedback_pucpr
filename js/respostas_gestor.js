document.addEventListener('DOMContentLoaded', () => {
    gestor_valida_sessao();
    buscar();
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
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Texto</th>
                    <th>Emocional</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr>
                <td> ${tabela[i].texto} </td>
                <td> ${tabela[i].emocional} </td>
            </tr>
        `;
    }

    html += `
            </tbody>
        </table>
    `;
    document.getElementById('lista').innerHTML = html;
}