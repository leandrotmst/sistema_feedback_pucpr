// Mariana - testes do JavaScript das telas (CT47 e CT48), feitos com Jest
// O Jest simula o navegador (jsdom), então não precisa abrir o sistema.
const fs = require('fs');
const path = require('path');

const PASTA_DO_SISTEMA = path.resolve(__dirname, '..', '..');

// Lê um arquivo .js do sistema e devolve as funções que a gente quer testar
function carregar(arquivo, funcoes) {
  const codigo = fs.readFileSync(path.join(PASTA_DO_SISTEMA, arquivo), 'utf8');
  return new Function(codigo + '\nreturn { ' + funcoes.join(', ') + ' };')();
}

beforeEach(() => {
  window.alert = jest.fn();   // "alert" de mentira, para saber se ele foi chamado
  global.fetch = jest.fn(() => Promise.resolve({ json: () => Promise.resolve({ status: 'ok', data: [] }) }));
});

test('CT47 - Enviar formulário sem marcar o nível', async () => {
  // tela "Nova Resposta Semanal": resumo escrito, mas nenhum nível marcado
  document.body.innerHTML = `
    <input type="radio" name="nivel" value="0"> <input type="radio" name="nivel" value="5">
    <textarea id="texto">Semana corrida</textarea>
    <input id="equipe"> <button id="enviar">Enviar Resposta</button>`;
  carregar('js/resposta_nova.js', ['nova']);

  document.getElementById('enviar').click();
  await new Promise((r) => setTimeout(r, 0));   // espera o clique terminar

  // tem que avisar o usuário...
  expect(window.alert).toHaveBeenCalledWith('Preencha todos os campos obrigatórios (humor e resumo).');
  // ...e não pode mandar nada para o servidor
  const envios = global.fetch.mock.calls.filter(([url]) => String(url).includes('resposta_nova.php'));
  expect(envios.length).toBe(0);
});

test('CT48 - Código HTML no nome da equipe (XSS)', () => {
  // tela "Quadro de Pontuação" com um funcionário cuja equipe tem um código escondido
  document.body.innerHTML = '<div id="leaderboard"></div>';
  const { preencherLeaderboard } = carregar('js/leaderboard.js', ['preencherLeaderboard']);

  preencherLeaderboard([
    { email: 'ana@empresa.com', equipe: '<img src=x onerror="alert(document.cookie)">', pontuacao: 3, respostas_semana: 1 },
  ]);

  // o certo é aparecer o texto, e não ser criada uma imagem com código
  expect(document.querySelector('#leaderboard img')).toBeNull();
});
