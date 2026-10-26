<?php
require_once __DIR__ . '/../TesteBase.php';

// Leandro - Gestores e Analistas (CT17 a CT24)
// O CT24 (desempenho do Portal do Gestor) é feito no Lighthouse: ver o Roteiro de Execução
class LeandroGestoresAnalistasTest extends TesteBase
{
    /** @testdox CT17 - Ver lista de gestores sem estar logado */
    public function testCT17(): void
    {
        // ninguém logado
        $resposta = $this->respostaJson($this->abrir('admins/php/gestores/gestor_get.php'));

        $this->assertNotSame('ok', $resposta['status'],
            'Sem login, a página devolveu todos os gestores com as senhas: ' . json_encode($resposta['data']));
    }

    /** @testdox CT18 - Excluir gestor sem estar logado */
    public function testCT18(): void
    {
        // ninguém logado tenta excluir o gestor 2
        $this->abrir('admins/php/gestores/gestor_excluir.php?id=2');

        $this->assertNotNull($this->consultar('SELECT id FROM gestor WHERE id=2'),
            'O gestor 2 foi apagado por alguém sem login (e junto com ele os funcionários e respostas dele)');
    }

    /** @testdox CT19 - Cadastrar gestor com e-mail repetido */
    public function testCT19(): void
    {
        $admin = $this->login('admin', 'admin', 'admin123');

        $resposta = $this->abrir('admins/php/gestores/gestor_novo.php',
            ['email' => 'gestor1@empresa.com', 'senha' => 'abc'], $admin);

        // o certo é responder um JSON com "nok", e não dar erro do PHP
        $json = $this->respostaJson($resposta);
        $this->assertSame('nok', $json['status']);
    }

    /** @testdox CT20 - Alterar gestor sem trocar a senha */
    public function testCT20(): void
    {
        $admin = $this->login('admin', 'admin', 'admin123');

        // nova senha em branco: só o e-mail deve mudar
        $resposta = $this->respostaJson($this->abrir('admins/php/gestores/gestor_alterar.php?id=1',
            ['email' => 'gestor1.novo@empresa.com'], $admin));

        $this->assertSame('ok', $resposta['status']);
        $gestor = $this->consultar('SELECT email, senha FROM gestor WHERE id=1');
        $this->assertSame('gestor1.novo@empresa.com', $gestor['email']);
        $this->assertSame('g1senha', $gestor['senha']);
    }

    /** @testdox CT21 - Cadastrar analista de dados */
    public function testCT21(): void
    {
        $gestor2 = $this->login('gestor', 'gestor2@empresa.com', 'g2senha');

        $resposta = $this->respostaJson($this->abrir('php/analista_dados_novo.php',
            ['email' => 'analista3@empresa.com', 'senha' => 'an3'], $gestor2));

        $this->assertSame('ok', $resposta['status']);
        $this->assertEquals(2, $this->consultar("SELECT gestor_id FROM analista_dados WHERE email='analista3@empresa.com'")['gestor_id']);
    }

    /** @testdox CT22 - Cadastrar analista com e-mail repetido */
    public function testCT22(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/analista_dados_novo.php',
            ['email' => 'analista1@empresa.com', 'senha' => 'x'], $gestor));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Este e-mail já está cadastrado no sistema', $resposta['mensagem']);
    }

    /** @testdox CT23 - Gestor alterando analista de outro gestor */
    public function testCT23(): void
    {
        // gestor 1 tenta alterar o analista 2, que é do gestor 2
        $gestor1 = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/analista_dados_alterar.php?id=2',
            ['email' => 'invadido@empresa.com'], $gestor1));

        $this->assertSame('nok', $resposta['status']);
        $this->assertStringStartsWith('Acesso negado', $resposta['mensagem']);
        $this->assertSame('analista2@empresa.com', $this->consultar('SELECT email FROM analista_dados WHERE id=2')['email']);
    }
}
