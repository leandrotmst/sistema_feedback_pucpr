<?php
/**
 * Respostas do formulário (equipe + nível emocional/estresse 0–5).
 */
class RespostaModel
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

    public function get(): array
    {
        $retorno = self::retornoVazio();

        $stmt = $this->conexao->prepare(
            'SELECT id, equipe, nivel, criado_em FROM resposta ORDER BY id DESC'
        );
        $stmt->execute();
        $resultado = $stmt->get_result();

        $tabela = [];
        while ($linha = $resultado->fetch_assoc()) {
            $tabela[] = $linha;
        }
        $retorno = [
            'status'   => 'ok',
            'mensagem' => 'Sucesso, consulta efetuada.',
            'data'     => $tabela,
        ];

        $stmt->close();
        $this->conexao->close();

        return $retorno;
    }

    public function novo(): array
    {
        $retorno = self::retornoVazio();

        $equipe = trim((string) ($_POST['equipe'] ?? ''));
        $nivel  = isset($_POST['nivel']) ? (int) $_POST['nivel'] : -1;

        if ($equipe === '' || $nivel < 0 || $nivel > 5) {
            $this->conexao->close();
            return [
                'status'   => 'nok',
                'mensagem' => 'Informe a equipe e um nível entre 0 e 5.',
                'data'     => [],
            ];
        }

        $stmt = $this->conexao->prepare(
            'INSERT INTO resposta (equipe, nivel) VALUES (?, ?)'
        );
        $stmt->bind_param('si', $equipe, $nivel);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Resposta registrada com sucesso.',
                'data'     => [],
            ];
        } else {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Falha ao salvar a resposta.',
                'data'     => [],
            ];
        }

        $stmt->close();
        $this->conexao->close();

        return $retorno;
    }
}
