document.addEventListener('DOMContentLoaded', () => {
    buscarLeaderboard();
});

async function buscarLeaderboard(){
    const retorno = await fetch('../php/leaderboard_get.php');
    const resposta = await retorno.json();

    if(resposta.status === 'ok'){
        preencherLeaderboard(resposta.data);
    } else {
        document.getElementById('leaderboard').innerHTML = '<p class="text-muted">Não foi possível carregar o ranking. Tente novamente mais tarde.</p>';
    }
}

function preencherLeaderboard(tabela){
    if(!tabela.length){
        document.getElementById('leaderboard').innerHTML = '<p class="text-muted">Nenhuma pontuação registrada ainda.</p>';
        return;
    }

    let html = `
        <div class="card card-md" style="padding:1rem;">
            <table class="w-full text-sm text-left border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Posição</th>
                        <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Funcionário</th>
                        <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Equipe</th>
                        <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Pontuação</th>
                        <th class="px-6 py-3 border-b border-gray-300 font-semibold text-gray-900">Respostas esta semana</th>
                    </tr>
                </thead>
                <tbody>
    `;

    tabela.forEach((linha, index) => {
        const rank = index + 1;
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${rank}</td>
                <td class="px-6 py-4 text-gray-900">${linha.email}</td>
                <td class="px-6 py-4 text-gray-900">${linha.equipe}</td>
                <td class="px-6 py-4 text-gray-900">${linha.pontuacao} 🔥 </td>
                <td class="px-6 py-4 text-gray-900">${linha.respostas_semana}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('leaderboard').innerHTML = html;
}
