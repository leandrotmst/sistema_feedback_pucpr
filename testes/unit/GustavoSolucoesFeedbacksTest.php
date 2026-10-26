<?php
require_once __DIR__ . '/../TesteBase.php';

// Gustavo - Soluções e Feedbacks (CT33 a CT40)
// O CT40 (stress no relatório) é feito no JMeter: testes/jmeter/CT40_stress_relatorio.jmx
class GustavoSolucoesFeedbacksTest extends TesteBase
{
    /** @testdox CT33 - Cadastrar proposta de melhoria */
    public function testCT33(): void
    {
        $analista = $this->login('analista', 'analista1@empresa.com', 'an1senha');

        $resposta = $this->respostaJson($this->abrir('php/solucao_nova.php',
            ['titulo' => 'Pausas programadas', 'equipe' => 'Qualidade', 'descricao' => 'Pausas de 15 minutos'], $analista));

        $this->assertSame('ok', $resposta['status']);
        $this->assertEquals(1, $this->consultar("SELECT analista_id FROM solucoes WHERE titulo='Pausas programadas'")['analista_id']);
    }

    /** @testdox CT34 - Cadastrar proposta sem título */
    public function testCT34(): void
    {
        $analista = $this->login('analista', 'analista1@empresa.com', 'an1senha');

        $resposta = $this->respostaJson($this->abrir('php/solucao_nova.php',
            ['titulo' => '', 'equipe' => 'Qualidade', 'descricao' => 'desc'], $analista));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Todos os campos são obrigatórios.', $resposta['mensagem']);
    }

    /** @testdox CT35 - Cadastrar proposta com título "0" */
    public function testCT35(): void
    {
        $analista = $this->login('analista', 'analista1@empresa.com', 'an1senha');

        $resposta = $this->respostaJson($this->abrir('php/solucao_nova.php',
            ['titulo' => '0', 'equipe' => 'Qualidade', 'descricao' => 'Prioridade zero'], $analista));

        // "0" é um texto válido, então deveria salvar
        $this->assertSame('ok', $resposta['status'], 'O título "0" foi recusado com: ' . $resposta['mensagem']);
    }

    /** @testdox CT36 - Excluir proposta sem estar logado */
    public function testCT36(): void
    {
        // ninguém logado tenta excluir a proposta 2
        $this->abrir('php/solucao_excluir.php?id=2');

        $this->assertNotNull($this->consultar('SELECT id FROM solucoes WHERE id=2'), 'A proposta 2 foi apagada por alguém sem login');
    }

    /** @testdox CT37 - Analista alterando proposta de outro analista */
    public function testCT37(): void
    {
        // analista 1 tenta alterar a proposta 2, que é do analista 2
        $analista1 = $this->login('analista', 'analista1@empresa.com', 'an1senha');

        $resposta = $this->respostaJson($this->abrir('php/solucao_alterar.php?id=2',
            ['titulo' => 'X', 'equipe' => 'X', 'descricao' => 'X'], $analista1));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Acesso negado: solução não pertence a este analista', $resposta['mensagem']);
        $this->assertSame('Revisar metas', $this->consultar('SELECT titulo FROM solucoes WHERE id=2')['titulo']);
    }

    /** @testdox CT38 - Gestor vê só as propostas da sua equipe */
    public function testCT38(): void
    {
        $gestor1 = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/gestor_solucoes_get.php', [], $gestor1));

        $this->assertSame('ok', $resposta['status']);
        $this->assertCount(1, $resposta['data']);   // só a proposta do analista dele
        $this->assertSame('Rodízio de plantão', $resposta['data'][0]['titulo']);
    }

    /** @testdox CT39 - Gestor vê só os feedbacks dos seus funcionários */
    public function testCT39(): void
    {
        $gestor1 = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/gestor_resposta_get.php', [], $gestor1));

        $this->assertSame('ok', $resposta['status']);
        $emails = array_column($resposta['data'], 'email_do_funcionario');
        sort($emails);
        $this->assertSame(['ana@empresa.com', 'bruno@empresa.com'], $emails);   // a Carla é do gestor 2
    }
}
