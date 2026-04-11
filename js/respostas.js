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
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Texto</th>
                    <th>Emocional</th>
                    <th>E-mail do funcionário</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    for(var i=0; i < tabela.length; i++){
        html += `
            <tr>
                <td> ${tabela[i].texto} </td>
                <td> ${tabela[i].emocional} </td>
                <td> ${tabela[i].email_do_funcionario} </td>
                <td>
                    <a href='../formulario/resposta_alterar.html?id=${tabela[i].id}' class='btn-alterar'>Alterar</a>
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