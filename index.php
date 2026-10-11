<?php
// Cria a pasta uploads se não existir
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$mensagem = '';
$status = '';

// Processamento do upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $nome_aluno = trim($_POST['nome_aluno'] ?? '');
    $turma = trim($_POST['turma'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    $arquivo = $_FILES['arquivo'];

    if ($nome_aluno === '' || $turma === '' || $titulo === '') {
        $mensagem = "Por favor, preencha todos os campos do formulário.";
        $status = "erro";
    } elseif ($arquivo['error'] === UPLOAD_ERR_OK) {
        // Sanitizar nome do arquivo e do aluno para evitar problemas de segurança
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nome_limpo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nome_aluno);
        $titulo_limpo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $titulo);
        $novo_nome = $titulo_limpo . '_' . $nome_limpo . '_' . time() . '.' . $extensao;
        $destino = $upload_dir . $novo_nome;

        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            $mensagem = "Trabalho enviado com sucesso!";
            $status = "sucesso";
        } else {
            $mensagem = "Erro ao mover o arquivo enviado.";
            $status = "erro";
        }
    } else {
        $mensagem = "Erro no upload do arquivo. Código de erro: " . $arquivo['error'];
        $status = "erro";
    }
}

// Listar arquivos na pasta uploads
$arquivos_enviados = [];
if (is_dir($upload_dir)) {
    $scan = scandir($upload_dir);
    foreach ($scan as $file) {
        if ($file !== '.' && $file !== '..') {
            $caminho_completo = $upload_dir . $file;
            $arquivos_enviados[] = [
                'nome' => $file,
                'tamanho' => filesize($caminho_completo),
                'data' => filemtime($caminho_completo)
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio de Trabalhos Escolares</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Sistema de Envio de Trabalhos</h1>
            <p>Envie sua atividade escolar de forma rápida e segura.</p>
        </header>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $status; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="card-grid">
            <!-- Formulário de Envio -->
            <section class="card">
                <h2>Enviar Novo Trabalho</h2>
                <form action="index.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div class="form-group">
                        <label for="nome_aluno">Nome Completo do Aluno:</label>
                        <input type="text" id="nome_aluno" name="nome_aluno" required placeholder="Ex: João da Silva">
                    </div>

                    <div class="form-group">
                        <label for="turma">Turma / Série:</label>
                        <input type="text" id="turma" name="turma" required placeholder="Ex: 9º Ano A">
                    </div>

                    <div class="form-group">
                        <label for="titulo">Título do Trabalho:</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ex: Pesquisa de História">
                    </div>

                    <div class="form-group">
                        <label for="arquivo">Selecione o Arquivo:</label>
                        <div class="file-drop-area" id="dropArea">
                            <span class="file-msg">Arraste o arquivo aqui ou clique para selecionar</span>
                            <input type="file" id="arquivo" name="arquivo" required>
                        </div>
                        <small id="fileNameDisplay" class="file-name"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar Trabalho</button>
                </form>
            </section>

            <!-- Lista de Trabalhos e Download Geral -->
            <section class="card">
                <div class="header-section">
                    <h2>Trabalhos Enviados</h2>
                    <?php if (!empty($arquivos_enviados)): ?>
                        <a href="download_all.php" class="btn btn-success">📥 Baixar Todos (ZIP)</a>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <?php if (empty($arquivos_enviados)): ?>
                        <p class="empty-text">Nenhum trabalho enviado até o momento.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome do Arquivo</th>
                                    <th>Tamanho</th>
                                    <th>Data</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arquivos_enviados as $arq): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($arq['nome']); ?></td>
                                        <td><?php echo number_format($arq['tamanho'] / 1024, 2, ',', '.'); ?> KB</td>
                                        <td><?php echo date('d/m/Y H:i', $arq['data']); ?></td>
                                        <td>
                                            <a href="uploads/<?php echo urlencode($arq['nome']); ?>" class="btn-sm" download>Baixar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
