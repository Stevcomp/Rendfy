<?php
// Inicia ou retoma a sessão ativa do usuário no servidor
session_start();

// Bloqueia o acesso se o usuário não estiver autenticado e redireciona para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../Index.php");
    exit;
}

// Importa as operações do banco de dados (que agora também trazem a conexão embutida)
require_once __DIR__ . '/database/crud.php';
// Captura e forma o ID do usuário conectado com segurança
$usuario_id = intval($_SESSION['usuario_id']);

// Garante que o script só processe requisições via POST contendo arquivos anexados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    // Aloca os metadados do arquivo enviado em uma variável local de controle
    $arquivo = $_FILES['foto'];

    // Define uma lista branca contendo as extensões de imagem permitidas pelo sistema
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    // Extrai e converte a extensão do arquivo enviado para letras minúsculas
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    // Valida se a extensão é permitida e se o upload não registrou códigos de erro
    if (in_array($extensao, $extensoesPermitidas) && $arquivo['error'] === 0) {

        // Verifica a existência do diretório de uploads e cria-o caso necessário
        $pastaUploads = __DIR__ . '/../uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0777, true);
        }

        // Gera uma nomenclatura única e segura combinando o ID do usuário e um hash temporal
        $novoNome = "foto_usuario_" . $usuario_id . "_" . uniqid() . "." . $extensao;
        // Estrutura o caminho físico final onde o arquivo de imagem será hospedado
        $destino = $pastaUploads . DIRECTORY_SEPARATOR . $novoNome;

        // Tenta transferir o arquivo temporário do servidor para a pasta definitiva de destino
        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {

            // == Seguro: prepared statements para buscar os dados de perfil do usuário logado ==
            $antigo = read($pdo, 'usuarios', "id = ?", [$usuario_id]);
            // Remove fisicamente do disco a foto de perfil anterior para evitar acúmulo de arquivos obsoletos
            if ($antigo && !empty($antigo['foto_perfil']) && file_exists($pastaUploads . DIRECTORY_SEPARATOR . $antigo['foto_perfil'])) {
                unlink($pastaUploads . DIRECTORY_SEPARATOR . $antigo['foto_perfil']);
            }

            // == Seguro: Atualiza o registro do usuário com a nova foto usando prepared statement ==
            update($pdo, 'usuarios', ['foto_perfil' => $novoNome], "id = ?", [$usuario_id]);
        }
    }
}

// Redireciona o fluxo de navegação do usuário de volta para a página de perfil atualizada
header("Location: ../Perfil_cliente.php");
exit;