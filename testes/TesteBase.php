<?php
use PHPUnit\Framework\TestCase;

/*
 * Classe base dos nossos testes.
 *
 * Como o AlignUp é feito em PHP "procedural" (cada arquivo é uma página que recebe
 * um formulário e devolve um JSON), os testes fazem a mesma coisa que o navegador:
 * fazem login, mandam o formulário para a página PHP e conferem o JSON que voltou.
 * Por isso o XAMPP (Apache e MySQL) precisa estar ligado.
 *
 * ATENÇÃO: antes de cada teste o banco "projeto" é apagado e criado de novo
 * com os dados do arquivo massa_de_dados.sql.
 */
abstract class TesteBase extends TestCase
{
    // Mesmo usuário e senha do banco que estão no php/conexao.php
    const BANCO_USUARIO = 'root';
    const BANCO_SENHA   = '';

    // Antes de CADA teste: banco limpo, sempre com os mesmos dados
    protected function setUp(): void
    {
        $banco = new mysqli('localhost', self::BANCO_USUARIO, self::BANCO_SENHA);
        $banco->multi_query(file_get_contents(__DIR__ . '/massa_de_dados.sql'));
        while ($banco->more_results() && $banco->next_result()) { }   // espera terminar todos os comandos
        $banco->close();
    }

    // Endereço do sistema no XAMPP (pode ser trocado no phpunit.xml)
    protected function url(string $pagina): string
    {
        $base = getenv('URL_SISTEMA') ?: 'http://localhost/sistema_feedback_pucpr';
        return rtrim($base, '/') . '/' . $pagina;
    }

    // Pasta do projeto (para os testes que leem ou alteram arquivos do sistema)
    protected function pastaDoSistema(): string
    {
        return dirname(__DIR__);
    }

    /*
     * Faz login e devolve o "usuário logado" (na verdade, o arquivo onde ficam os cookies da sessão).
     * $tipo: funcionario, gestor, analista ou admin
     */
    protected function login(string $tipo, string $email, string $senha): string
    {
        $paginas = [
            'funcionario' => 'php/funcionario_login.php',
            'gestor'      => 'php/gestor_login.php',
            'analista'    => 'php/analista_dados_login.php',
            'admin'       => 'admins/php/admin_login.php',
        ];
        $campoEmail = ($tipo === 'admin') ? 'usuario' : 'email';

        $usuario = tempnam(sys_get_temp_dir(), 'cookie');
        $this->abrir($paginas[$tipo], [$campoEmail => $email, 'senha' => $senha], $usuario);
        return $usuario;
    }

    /*
     * Abre uma página do sistema.
     * - $dados: campos do formulário (se tiver, envia como POST)
     * - $usuario: quem está logado (o que o login() devolveu). null = ninguém logado
     * Devolve ['texto' => o que a página respondeu, 'json' => a resposta já convertida (ou null se não for JSON)]
     */
    protected function abrir(string $pagina, array $dados = [], ?string $usuario = null): array
    {
        $curl = curl_init($this->url($pagina));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        if ($dados) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($dados));
        }
        if ($usuario) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $usuario);   // manda o cookie da sessão
            curl_setopt($curl, CURLOPT_COOKIEJAR, $usuario);    // guarda o cookie novo
        }
        $texto = curl_exec($curl);
        $erro  = curl_error($curl);
        curl_close($curl);

        $this->assertNotFalse($texto, "Não consegui abrir {$pagina}. O Apache do XAMPP está ligado? Erro: {$erro}");
        return ['texto' => $texto, 'json' => json_decode(trim($texto), true)];
    }

    // Confere que a página respondeu um JSON válido (e não um erro do PHP) e devolve ele
    protected function respostaJson(array $resposta): array
    {
        $this->assertIsArray($resposta['json'], "A página não devolveu um JSON válido. Ela respondeu:\n" . $resposta['texto']);
        return $resposta['json'];
    }

    // Faz uma consulta no banco e devolve a primeira linha (ou null)
    protected function consultar(string $sql): ?array
    {
        $banco = new mysqli('localhost', self::BANCO_USUARIO, self::BANCO_SENHA, 'projeto');
        $linha = $banco->query($sql)->fetch_assoc();
        $banco->close();
        return $linha;
    }

    // Executa um comando no banco (UPDATE, DELETE...)
    protected function executar(string $sql): void
    {
        $banco = new mysqli('localhost', self::BANCO_USUARIO, self::BANCO_SENHA, 'projeto');
        $banco->query($sql);
        $banco->close();
    }

    /*
     * Teste de mutação: troca um trecho do código do sistema por outro (o "mutante")
     * e devolve o código original, para depois desfazer com consertarCodigo().
     */
    protected function estragarCodigo(string $arquivo, string $trechoOriginal, string $trechoMutante): string
    {
        $caminho  = $this->pastaDoSistema() . '/' . $arquivo;
        $original = file_get_contents($caminho);
        $this->assertSame(1, substr_count($original, $trechoOriginal), "O trecho a ser trocado não foi encontrado em {$arquivo}");
        file_put_contents($caminho, str_replace($trechoOriginal, $trechoMutante, $original));
        sleep(3);   // espera o PHP perceber que o arquivo mudou (o cache do PHP confere a cada 2 segundos)
        return $original;
    }

    protected function consertarCodigo(string $arquivo, string $original): void
    {
        file_put_contents($this->pastaDoSistema() . '/' . $arquivo, $original);
        sleep(3);   // espera o PHP voltar a usar o código original
    }
}
