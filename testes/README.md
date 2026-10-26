# Testes do AlignUp - RA2 (Verificação e Validação)

São 48 casos de teste, 8 por pessoa. Em cada um: 7 são automáticos (PHPUnit ou Jest) e 1 é feito com outra ferramenta ou na mão (JMeter, Lighthouse, DevTools, navegadores ou heurísticas de Nielsen). O passo a passo completo está no Roteiro de Execução (PDF).

| Pessoa   | Casos       | Arquivo dos testes automáticos            | Caso feito fora do PHPUnit             |
|----------|-------------|-------------------------------------------|----------------------------------------|
| Fernanda | CT01 a CT08 | unit/FernandaAutenticacaoTest.php         | CT06 - carga no login (JMeter)         |
| Igor     | CT09 a CT16 | unit/IgorFuncionariosTest.php             | CT14 - 3 navegadores                   |
| Leandro  | CT17 a CT24 | unit/LeandroGestoresAnalistasTest.php     | CT24 - desempenho (Lighthouse)         |
| Yumi     | CT25 a CT32 | unit/YumiRespostasTest.php                | CT30 - usabilidade (Nielsen)           |
| Gustavo  | CT33 a CT40 | unit/GustavoSolucoesFeedbacksTest.php     | CT40 - stress no relatório (JMeter)    |
| Mariana  | CT41 a CT48 | unit/MarianaAnaliseIATest.php e js/       | CT45 - Quadro de Pontuação no celular  |

## O que tem nesta pasta

- `TesteBase.php`: funções que todos os testes usam (login, abrir uma página do sistema, consultar o banco)
- `unit/`: testes em PHP de cada pessoa (PHPUnit)
- `js/`: testes do JavaScript das telas (Jest), da Mariana
- `jmeter/`: planos de carga (CT06) e stress (CT40)
- `ia_falsa.php`: uma "IA de mentira" para os testes CT43 e CT44 (não precisa de internet nem de chave)
- `massa_de_dados.sql`: os dados de teste (Ana, Bruno, Carla, gestores e analistas)

## Como rodar

1. Coloque esta pasta `testes` dentro do projeto (ao lado das pastas `php` e `sql`).
2. Ligue o **Apache** e o **MySQL** no XAMPP. Os testes abrem as páginas do sistema igual ao navegador.
3. Instale o PHPUnit uma vez: `php composer.phar install` (ver o roteiro).
4. Rode os seus testes:
   - Windows: `%PHP% vendor\phpunit\phpunit\phpunit --testsuite Yumi`
   - Mac: `php vendor/phpunit/phpunit/phpunit --testsuite Yumi`
5. Mariana (Jest): `npm install` e depois `npx jest --verbose`

**Cuidado:** antes de cada teste o banco `projeto` é apagado e criado de novo. Não use com dados que vocês queiram guardar.

Se o projeto estiver em outra pasta ou porta (ex.: `http://localhost:8080/alignup`), mude o endereço no `phpunit.xml`.

## Lendo o resultado

- ✔ = o sistema fez o que era esperado → **OK** na planilha
- ✘ = o sistema fez diferente do esperado → **NOK**. A frase embaixo do ✘ explica o que aconteceu.

Os testes de mutação (CT08 e CT32) mexem num arquivo do sistema de propósito e desfazem a mudança no final. Eles demoram uns segundos a mais.
