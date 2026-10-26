<?php
require_once __DIR__ . '/../TesteBase.php';

// Mariana - Análise com IA (CT41 a CT46)
// O CT45 (Quadro de Pontuação no celular) é feito no DevTools: ver o Roteiro de Execução
// O CT47 e o CT48 são do JavaScript e rodam no Jest: testes/js/mariana_front.test.js
//
// No CT43 e no CT44 a gente não usa a IA de verdade (precisa de chave paga e internet).
// O arquivo ia_falsa.php faz o papel da IA e responde do jeito que o teste pedir.
class MarianaAnaliseIATest extends TesteBase
{
    private static $iaFalsa;

    // Liga a "IA falsa" na porta 8765 antes dos testes da Mariana
    public static function setUpBeforeClass(): void
    {
        $comando = escapeshellarg(PHP_BINARY) . ' -S 127.0.0.1:8765 ' . escapeshellarg(dirname(__DIR__) . '/ia_falsa.php');
        $nada    = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        self::$iaFalsa = proc_open($comando, [1 => ['file', $nada, 'w'], 2 => ['file', $nada, 'w']], $pipes);
        sleep(1);
    }

    // Desliga a "IA falsa" no final
    public static function tearDownAfterClass(): void
    {
        proc_terminate(self::$iaFalsa);
    }

    /** @testdox CT41 - Gerar análise sem a chave da IA configurada */
    public function testCT41(): void
    {
        // tira o .env do lugar durante o teste (é nele que fica a chave da IA)
        $env = $this->pastaDoSistema() . '/.env';
        $tinhaEnv = file_exists($env);
        if ($tinhaEnv) { rename($env, $env . '.guardado'); }

        try {
            $analista = $this->login('analista', 'analista1@empresa.com', 'an1senha');
            $resposta = $this->respostaJson($this->abrir('php/gerar_analise_ia.php', [], $analista));

            $this->assertSame('nok', $resposta['status']);
            $this->assertSame('Erro ao conectar com a IA: API Key da Perplexity não configurada no arquivo .env', $resposta['mensagem']);
        } finally {
            if ($tinhaEnv) { rename($env . '.guardado', $env); }   // devolve o .env
        }
    }

    /** @testdox CT42 - Gerar análise sem respostas na última semana */
    public function testCT42(): void
    {
        // deixa todas as respostas com 10 dias atrás
        $this->executar('UPDATE respostas SET criado_em = DATE_SUB(NOW(), INTERVAL 10 DAY)');
        $analista = $this->login('analista', 'analista1@empresa.com', 'an1senha');

        $resposta = $this->respostaJson($this->abrir('php/gerar_analise_ia.php', [], $analista));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Não há respostas na última semana para gerar a análise.', $resposta['mensagem']);
    }

    /** @testdox CT43 - IA responde corretamente */
    public function testCT43(): void
    {
        $ia = $this->analisadorComIaFalsa('certo');   // a IA falsa responde um JSON válido

        $resultado = $ia->analisarRespostasSemanais([['emocional' => 2, 'resumo_semana' => 'Semana tranquila']]);

        $this->assertSame('Clima estável', $resultado['resumo']);
        $this->assertSame('Nenhum', $resultado['alertas']);
        $this->assertSame('Manter rituais', $resultado['solucoes_propostas']);
    }

    /** @testdox CT44 - IA responde em formato errado */
    public function testCT44(): void
    {
        $ia = $this->analisadorComIaFalsa('errado');  // a IA falsa responde um texto qualquer

        try {
            $ia->analisarRespostasSemanais([['emocional' => 2, 'resumo_semana' => 'Semana tranquila']]);
            $this->fail('Era para dar erro de formato inesperado');
        } catch (Exception $erro) {
            $this->assertStringStartsWith('A IA retornou um formato inesperado', $erro->getMessage());
        }
    }

    /** @testdox CT46 - Escala do formulário igual à usada pela IA */
    public function testCT46(): void
    {
        // escala que o funcionário vê na tela
        $formulario = file_get_contents($this->pastaDoSistema() . '/formulario/resposta_nova.html');
        $this->assertStringContainsString('0 — Ótimo', $formulario);
        $this->assertStringContainsString('5 — Crítico', $formulario);

        // escala que o sistema explica para a IA
        $codigoIa = file_get_contents($this->pastaDoSistema() . '/php/analise_ia.php');
        $this->assertStringNotContainsString('0 (pior) a 5 (melhor)', $codigoIa,
            'Na tela 0 = Ótimo e 5 = Crítico, mas a IA recebe "0 (pior) a 5 (melhor)": a escala está invertida');
    }

    // Carrega a classe AnalisadorIA do sistema trocando só o endereço da IA de verdade pelo da IA falsa
    private function analisadorComIaFalsa(string $modo)
    {
        $codigo = file_get_contents($this->pastaDoSistema() . '/php/analise_ia.php');
        $classe = 'AnalisadorIA_' . $modo;
        $codigo = str_replace('https://api.perplexity.ai/chat/completions', "http://127.0.0.1:8765/?modo={$modo}", $codigo);
        $codigo = str_replace("require_once 'config_env.php';", "require_once '" . $this->pastaDoSistema() . "/php/config_env.php';", $codigo);
        $codigo = str_replace('class AnalisadorIA', "class {$classe}", $codigo);
        if (!class_exists($classe)) {
            eval('?>' . $codigo);
        }
        putenv('PERPLEXITY_API_KEY=chave-de-teste');   // chave de mentira, a IA falsa não confere
        $_ENV['PERPLEXITY_API_KEY'] = 'chave-de-teste';
        return new $classe();
    }
}
