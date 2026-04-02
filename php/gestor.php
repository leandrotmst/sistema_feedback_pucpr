<?php
/**
 * Classe Gestor — mesma lógica dos endpoints em projeto/php:
 * gestor_login, gestor_get, gestor_novo, gestor_alterar, gestor_excluir,
 * valida_sessao, gestor_logoff.
 *
 * Uso típico (um script por ação, como nos .php originais):
 *
 *   require_once __DIR__ . '/gestor.php';
 *   $api = new Gestor();
 *   Gestor::enviarJson($api->login());
 */

class Gestor
{
    private mysqli $conexao;

    public function __construct()
    {
        require_once __DIR__ . '/conexao.php';
        global $conexao;
        $this->conexao = $conexao;
    }

    /** @return array{status: string, mensagem: string, data: array} */
    private static function retornoVazio(): array
    {
        return [
            'status'   => '',
            'mensagem' => '',
            'data'     => [],
        ];
    }

    /**
     * Equivalente a gestor_login.php
     */
    public function login(): array
    {
        $retorno = self::retornoVazio();

        $stmt = $this->conexao->prepare("SELECT * FROM gestor WHERE email = ? AND senha = ?");
        $stmt->bind_param('ss', $_POST['email'], $_POST['senha']);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $tabela = [];
        if ($resultado->num_rows > 0) {
            while ($linha = $resultado->fetch_assoc()) {
                $tabela[] = $linha;
            }

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            // $_SESSION['email'] = $tabela;
            $_SESSION['gestor_logado'] = $tabela[0]['email'];

            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Sucesso, consulta efetuada.',
                'data'     => $tabela,
            ];
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Não há registros',
                'data'     => [],
            ];
        }

        $stmt->close();
        $this->conexao->close();

        return $retorno;
    }

    /**
     * Equivalente a gestor_get.php (lista ou por id em $_GET['id']).
     */
    public function get(): array
    {
        $retorno = self::retornoVazio();

        if (isset($_GET['id'])) {
            $stmt = $this->conexao->prepare('SELECT * FROM gestor WHERE id = ?');
            $stmt->bind_param('i', $_GET['id']);
        } else {
            $stmt = $this->conexao->prepare('SELECT * FROM gestor');
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        $tabela = [];
        if ($resultado->num_rows > 0) {
            while ($linha = $resultado->fetch_assoc()) {
                $tabela[] = $linha;
            }
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Sucesso, consulta efetuada.',
                'data'     => $tabela,
            ];
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Não há registros',
                'data'     => [],
            ];
        }

        $stmt->close();
        $this->conexao->close();

        return $retorno;
    }

    /**
     * Equivalente a gestor_novo.php — campos em $_POST.
     */
    public function novo(): array
    {
        $retorno = self::retornoVazio();

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $this->conexao->prepare(
            'INSERT INTO gestor(email, senha) VALUES(?,?)'
        );
        $stmt->bind_param('ss', $email, $senha);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'registro inserido com sucesso',
                'data'     => [],
            ];
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'falha ao inserir o registro',
                'data'     => [],
            ];
        }

        $stmt->close();
        $this->conexao->close();

        return $retorno;
    }

    /**
     * Equivalente a gestor_alterar.php — id em $_GET['id'], demais campos em $_POST.
     */
    public function alterar(): array
    {
        $retorno = self::retornoVazio();

        if (isset($_GET['id'])) {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $stmt = $this->conexao->prepare(
                'UPDATE gestor SET email = ?, senha = ? WHERE id = ?'
            );
            $stmt->bind_param('ssi', $email, $senha, $_GET['id']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $retorno = [
                    'status'   => 'ok',
                    'mensagem' => 'Registro alterado com sucesso.',
                    'data'     => [],
                ];
            } else {
                $retorno = [
                    'status'   => 'nok',
                    'mensagem' => 'Não posso alterar um registro.' . json_encode($_GET),
                    'data'     => [],
                ];
            }
            $stmt->close();
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Não posso alterar um registro sem um ID informado.',
                'data'     => [],
            ];
        }

        $this->conexao->close();

        return $retorno;
    }

    /**
     * Equivalente a gestor_excluir.php — id em $_GET['id'].
     */
    public function excluir(): array
    {
        $retorno = self::retornoVazio();

        if (isset($_GET['id'])) {
            $stmt = $this->conexao->prepare('DELETE FROM gestor WHERE id = ?');
            $stmt->bind_param('i', $_GET['id']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $retorno = [
                    'status'   => 'ok',
                    'mensagem' => 'Registro excluido',
                    'data'     => [],
                ];
            } else {
                $retorno = [
                    'status'   => 'nok',
                    'mensagem' => 'Registro não excluido',
                    'data'     => [],
                ];
            }
            $stmt->close();
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'É necessário informar um ID para exclusão',
                'data'     => [],
            ];
        }

        $this->conexao->close();

        return $retorno;
    }

    /**
     * Equivalente a valida_sessao.php (não usa banco).
     */
    public static function validaSessao(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (isset($_SESSION['email'])) {
            return [
                'status'   => 'ok',
                'mensagem' => '',
                'data'     => [],
            ];
        }
        return [
            'status'   => 'nok',
            'mensagem' => '',
            'data'     => [],
        ];
    }

    /**
     * Equivalente a gestor_logoff.php (não usa banco).
     */
    public static function logoff(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return [
            'status'   => 'ok',
            'mensagem' => '',
            'data'     => [],
        ];
    }

    /**
     * Resposta JSON para o front (cabeçalho correto: charset=utf-8).
     */
    public static function enviarJson(array $retorno): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($retorno);
    }
}
