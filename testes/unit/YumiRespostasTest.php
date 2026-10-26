<?php
require_once __DIR__ . '/../TesteBase.php';

// Yumi - Responder Formulário (CT25 a CT32)
// O CT30 (usabilidade com as heurísticas de Nielsen) é manual: ver o Roteiro de Execução
class YumiRespostasTest extends TesteBase
{
    /** @testdox CT25 - Enviar resposta semanal válida */
    public function testCT25(): void
    {
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        $resposta = $this->respostaJson($this->abrir('php/resposta_nova.php',
            ['nivel' => '3', 'texto' => 'Semana produtiva'], $ana));

        $this->assertSame('ok', $resposta['status']);
        $salva = $this->consultar("SELECT * FROM respostas WHERE texto='Semana produtiva'");
        $this->assertEquals(3, $salva['emocional']);
        $this->assertSame('ana@empresa.com', $salva['email_do_funcionario']);
        // a Ana tinha 1 ponto e ganha mais 1
        $this->assertEquals(2, $this->consultar('SELECT pontuacao FROM funcionarios WHERE id=1')['pontuacao']);
    }

    /** @testdox CT26 - Enviar resposta sem estar logado */
    public function testCT26(): void
    {
        $resposta = $this->respostaJson($this->abrir('php/resposta_nova.php',
            ['nivel' => '3', 'texto' => 'tentativa sem login']));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Sessão inválida', $resposta['mensagem']);
        $this->assertEquals(3, $this->consultar('SELECT COUNT(*) AS total FROM respostas')['total']);
    }

    /** @testdox CT27 - Alterar a própria resposta */
    public function testCT27(): void
    {
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        // a resposta 1 é da Ana: muda o nível de 2 para 4 e o texto
        $resposta = $this->respostaJson($this->abrir('php/resposta_alterar.php?id=1',
            ['emocional' => '4', 'texto' => 'Semana mais puxada que o normal'], $ana));

        $this->assertSame('ok', $resposta['status']);
        $alterada = $this->consultar('SELECT emocional, texto FROM respostas WHERE id=1');
        $this->assertEquals(4, $alterada['emocional']);
        $this->assertSame('Semana mais puxada que o normal', $alterada['texto']);
        $this->assertEquals(1, $this->consultar('SELECT pontuacao FROM funcionarios WHERE id=1')['pontuacao'], 'Alterar não pode dar ponto');
    }

    /** @testdox CT28 - Enviar resposta vazia (burlando a tela) */
    public function testCT28(): void
    {
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        // manda direto para o PHP, sem nível e sem texto (a tela não deixaria)
        $resposta = $this->respostaJson($this->abrir('php/resposta_nova.php', ['nivel' => '', 'texto' => ''], $ana));

        $pontos = $this->consultar('SELECT pontuacao FROM funcionarios WHERE id=1')['pontuacao'];
        $this->assertSame('nok', $resposta['status'], "O servidor aceitou a resposta vazia e a Ana foi para {$pontos} pontos");
    }

    /** @testdox CT29 - Alterar resposta de outro funcionário */
    public function testCT29(): void
    {
        $this->assertTrue($this->anaNaoConsegueAlterarRespostaDoBruno());
    }

    /** @testdox CT31 - Excluir resposta com pontuação zerada */
    public function testCT31(): void
    {
        // 1. A Ana (1 ponto) exclui a resposta dela e fica com 0
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');
        $resposta = $this->respostaJson($this->abrir('php/resposta_excluir.php?id=1', [], $ana));
        $this->assertSame('ok', $resposta['status']);
        $this->assertEquals(0, $this->consultar('SELECT pontuacao FROM funcionarios WHERE id=1')['pontuacao']);

        // 2 e 3. O Bruno já está com 0 pontos e exclui a resposta dele: tem que continuar 0
        $this->executar('UPDATE funcionarios SET pontuacao=0 WHERE id=2');
        $bruno = $this->login('funcionario', 'bruno@empresa.com', 'bruno123');

        $resposta = $this->respostaJson($this->abrir('php/resposta_excluir.php?id=2', [], $bruno));

        $this->assertSame('ok', $resposta['status']);
        $this->assertEquals(0, $this->consultar('SELECT pontuacao FROM funcionarios WHERE id=2')['pontuacao'], 'A pontuação não pode ficar negativa');
    }

    /** @testdox CT32 - Teste de mutação na proteção da resposta */
    public function testCT32(): void
    {
        // 1. No código original, o CT29 passa
        $this->assertTrue($this->anaNaoConsegueAlterarRespostaDoBruno());

        // 2. Estragamos de propósito a verificação de dono da resposta
        $original = $this->estragarCodigo('php/resposta_alterar.php', 'num_rows == 0', 'num_rows < 0');
        try {
            // 3. Agora o CT29 tem que falhar (= mutante eliminado)
            $this->assertFalse($this->anaNaoConsegueAlterarRespostaDoBruno(), 'O mutante sobreviveu: o CT29 não percebeu o erro');
        } finally {
            $this->consertarCodigo('php/resposta_alterar.php', $original);   // sempre volta o código original
        }
    }

    // Usado no CT29 e no CT32: a Ana tenta alterar a resposta 2, que é do Bruno
    private function anaNaoConsegueAlterarRespostaDoBruno(): bool
    {
        $this->setUp();   // banco limpo de novo
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');
        $r   = $this->abrir('php/resposta_alterar.php?id=2', ['emocional' => '0', 'texto' => 'alterado pela Ana'], $ana);

        $textoDoBruno = $this->consultar('SELECT texto FROM respostas WHERE id=2')['texto'];
        return is_array($r['json'])
            && $r['json']['status'] === 'nok'
            && $r['json']['mensagem'] === 'Acesso negado: resposta não pertence ao usuário'
            && $textoDoBruno === 'Muita pressão com o prazo da sprint';
    }
}
