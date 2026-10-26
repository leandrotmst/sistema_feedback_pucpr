<?php
require_once __DIR__ . '/../TesteBase.php';

// Igor - Gerenciamento de Funcionários (CT09 a CT16)
// O CT14 (portabilidade em 3 navegadores) é manual: ver o Roteiro de Execução
class IgorFuncionariosTest extends TesteBase
{
    /** @testdox CT09 - Funcionário tentando cadastrar outro funcionário */
    public function testCT09(): void
    {
        // quem está logado é a Ana (funcionária), não um gestor
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        $resposta = $this->respostaJson($this->abrir('php/funcionario_novo.php',
            ['email' => 'novo@empresa.com', 'senha' => '123', 'equipe' => 'Dev'], $ana));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Sessão inválida', $resposta['mensagem']);
        $this->assertNull($this->consultar("SELECT id FROM funcionarios WHERE email='novo@empresa.com'"));
    }

    /** @testdox CT10 - Cadastro de funcionário com dados válidos */
    public function testCT10(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/funcionario_novo.php',
            ['email' => 'diego@empresa.com', 'senha' => 'diego123', 'equipe' => 'Suporte'], $gestor));

        $this->assertSame('ok', $resposta['status']);
        $diego = $this->consultar("SELECT * FROM funcionarios WHERE email='diego@empresa.com'");
        $this->assertEquals(1, $diego['gestor_id']);    // ficou ligado ao gestor que cadastrou
        $this->assertSame('Suporte', $diego['equipe']);
        $this->assertEquals(0, $diego['pontuacao']);    // começa com 0 pontos
    }

    /** @testdox CT11 - Cadastro de funcionário com e-mail repetido */
    public function testCT11(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/funcionario_novo.php',
            ['email' => 'ana@empresa.com', 'senha' => 'x', 'equipe' => 'Dev'], $gestor));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Este e-mail já está cadastrado no sistema', $resposta['mensagem']);
    }

    /** @testdox CT12 - Cadastro com e-mail inválido e senha vazia (burlando a tela) */
    public function testCT12(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        // manda direto para o PHP, sem passar pela validação do JavaScript da tela
        $resposta = $this->respostaJson($this->abrir('php/funcionario_novo.php',
            ['email' => 'emailsemarroba', 'senha' => '', 'equipe' => ''], $gestor));

        $this->assertSame('nok', $resposta['status'], 'O servidor aceitou e-mail sem @, senha vazia e equipe vazia');
        $this->assertNull($this->consultar("SELECT id FROM funcionarios WHERE email='emailsemarroba'"));
    }

    /** @testdox CT13 - Gestor alterando funcionário de outro gestor */
    public function testCT13(): void
    {
        // gestor 1 tenta alterar a Carla (id 3), que é do gestor 2
        $gestor1 = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/funcionario_alterar.php?id=3',
            ['email' => 'hack@empresa.com', 'equipe' => 'X'], $gestor1));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Acesso negado: funcionário não pertence ao gestor', $resposta['mensagem']);
        $this->assertSame('carla@empresa.com', $this->consultar('SELECT email FROM funcionarios WHERE id=3')['email']);
    }

    /** @testdox CT15 - Trocar senha com a senha atual errada */
    public function testCT15(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $resposta = $this->respostaJson($this->abrir('php/funcionario_alterar.php?id=1', [
            'email' => 'ana@empresa.com', 'equipe' => 'Desenvolvimento',
            'senha_atual' => 'errada', 'senha_nova' => 'nova123',
        ], $gestor));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('A senha atual informada está incorreta.', $resposta['mensagem']);
        $this->assertSame('ana123', $this->consultar('SELECT senha FROM funcionarios WHERE id=1')['senha']);
    }

    /** @testdox CT16 - Excluir funcionário apaga as respostas dele */
    public function testCT16(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        // exclui o Bruno (id 2), que tem uma resposta cadastrada
        $resposta = $this->respostaJson($this->abrir('php/funcionario_excluir.php?id=2', [], $gestor));

        $this->assertSame('ok', $resposta['status']);
        $this->assertNull($this->consultar('SELECT id FROM funcionarios WHERE id=2'));
        $this->assertEquals(0, $this->consultar('SELECT COUNT(*) AS total FROM respostas WHERE funcionarios_id=2')['total']);
    }
}
