<?php
require_once __DIR__ . '/../TesteBase.php';

// Fernanda - Login e Sessão (CT01 a CT08)
// O CT06 (carga no login) é feito no JMeter: testes/jmeter/CT06_carga_login.jmx
class FernandaAutenticacaoTest extends TesteBase
{
    /** @testdox CT01 - Login de funcionário com dados corretos */
    public function testCT01(): void
    {
        // 1. Ana faz login com a senha certa
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        // 2. A sessão dela tem que estar valendo
        $sessao = $this->respostaJson($this->abrir('php/get_sessao_funcionario.php', [], $ana));
        $this->assertSame('ok', $sessao['status']);
        $this->assertSame('ana@empresa.com', $sessao['email']);
        $this->assertSame('Desenvolvimento', $sessao['equipe']);
    }

    /** @testdox CT02 - Login de funcionário com senha errada */
    public function testCT02(): void
    {
        $resposta = $this->respostaJson($this->abrir('php/funcionario_login.php', ['email' => 'ana@empresa.com', 'senha' => 'errada']));

        $this->assertSame('nok', $resposta['status']);
        $this->assertSame('Não há registros', $resposta['mensagem']);
    }

    /** @testdox CT03 - Login com SQL Injection */
    public function testCT03(): void
    {
        // texto clássico de ataque: tenta fazer o WHERE do SQL ser sempre verdadeiro
        $ataque   = "' OR '1'='1' -- ";
        $resposta = $this->respostaJson($this->abrir('php/funcionario_login.php', ['email' => $ataque, 'senha' => $ataque]));

        $this->assertSame('nok', $resposta['status']);
    }

    /** @testdox CT04 - Senha aparecendo na resposta do login */
    public function testCT04(): void
    {
        $resposta = $this->respostaJson($this->abrir('php/funcionario_login.php', ['email' => 'ana@empresa.com', 'senha' => 'ana123']));

        $this->assertSame('ok', $resposta['status']);
        // a resposta vai para o navegador, então NÃO pode ter a senha
        $this->assertArrayNotHasKey('senha', $resposta['data'][0],
            'A resposta do login trouxe a senha: ' . json_encode($resposta['data'][0]));
    }

    /** @testdox CT05 - Login de gestor e verificação da sessão */
    public function testCT05(): void
    {
        $gestor = $this->login('gestor', 'gestor1@empresa.com', 'g1senha');

        $valida = $this->respostaJson($this->abrir('php/gestor_valida_sessao.php', [], $gestor));
        $this->assertSame('ok', $valida['status']);
    }

    /** @testdox CT07 - Sair da conta encerra a sessão */
    public function testCT07(): void
    {
        $ana = $this->login('funcionario', 'ana@empresa.com', 'ana123');

        // clica em "Sair da Conta"
        $this->abrir('php/funcionario_logout.php', [], $ana);

        // depois de sair, a sessão não pode mais valer
        $valida = $this->respostaJson($this->abrir('php/funcionario_valida_sessao.php', [], $ana));
        $this->assertSame('nok', $valida['status']);
    }

    /** @testdox CT08 - Teste de mutação no login do gestor */
    public function testCT08(): void
    {
        // O teste "login de gestor com senha errada é recusado"
        $senhaErradaRecusada = function () {
            $r = $this->abrir('php/gestor_login.php', ['email' => 'gestor1@empresa.com', 'senha' => 'errada']);
            return is_array($r['json']) && $r['json']['status'] === 'nok';
        };

        // 1. No código original o teste passa
        $this->assertTrue($senhaErradaRecusada());

        // 2. Estragamos o código de propósito: "> 0" vira ">= 0" (o mutante)
        $original = $this->estragarCodigo('php/gestor_login.php', 'num_rows > 0', 'num_rows >= 0');
        try {
            // 3. Com o código estragado o teste tem que FALHAR (= mutante eliminado)
            $this->assertFalse($senhaErradaRecusada(), 'O mutante sobreviveu: o teste não percebeu o erro');
        } finally {
            $this->consertarCodigo('php/gestor_login.php', $original);   // sempre volta o código original
        }
    }
}
