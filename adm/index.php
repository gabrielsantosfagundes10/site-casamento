<?php
session_start();
// Como este arquivo está dentro da pasta /adm, precisamos de ../ para achar o config na raiz
include '../config.php';

$senha_definida = "2225"; 

// LOGIN: Redirecionamento após sucesso limpa o formulário e evita a mensagem de erro no F5
if (isset($_POST['senha'])) {
    if ($_POST['senha'] == $senha_definida) {
        $_SESSION['admin_logado'] = true;
        header("Location: ./"); // Redireciona para a própria pasta atual
        exit;
    } else {
        $erro = "Senha incorreta!";
    }
}

if (isset($_GET['sair'])) {
    session_destroy();
    header("Location: ./");
    exit;
}

// --- TELA DE LOGIN ---
if (!isset($_SESSION['admin_logado'])): ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davi & Fernanda</title>

    <meta property="og:title" content="Davi & Fernanda">
    <meta property="og:description" content="Painel de Gestão do Casamento">
    <meta property="og:image" content="../images/daviefernanda05.png">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="../images/cadeado.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif;
            /* Buscando a imagem na pasta anterior (../) e mantendo o enquadramento subido */
            background: url("../images/daviefernanda05.png") no-repeat center -400px fixed; 
            background-size: cover;
            height: 100vh; 
            display: flex; 
            align-items: flex-end; 
            justify-content: center; 
            overflow: hidden;
        }
        body::before {
            content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.25); z-index: 1;
        }
        .glass-card {
            position: relative; z-index: 2;
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(25px) saturate(150%);
            -webkit-backdrop-filter: blur(25px) saturate(150%);
            padding: 40px; border-radius: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            width: 90%; max-width: 350px; text-align: center;
            margin-bottom: 60px; 
        }
        .glass-card h2 { color: white; font-weight: 600; margin-bottom: 25px; letter-spacing: 3px; text-transform: uppercase; font-size: 0.8rem; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        input { width: 100%; padding: 15px; margin-bottom: 15px; border: none; border-radius: 12px; background: rgba(255, 255, 255, 0.9); outline: none; font-size: 1rem; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #4A6741; color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        button:hover { background: #3d5535; }
        .erro { color: #ff4d4d; background: rgba(0,0,0,0.4); padding: 8px; border-radius: 8px; margin-top: 10px; font-size: 0.75rem; font-weight: 600; }

        @media (max-width: 600px) {
            body { background-position: center center; align-items: flex-end; }
            .glass-card { margin-bottom: 40px; padding: 30px; max-width: 300px; }
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <h2>Área Restrita</h2>
        <form method="POST">
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <?php if(isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
    </div>
</body>
</html>
<?php exit; endif; 

// --- CONSULTAS ---
$res_confirmados = $conn->query("SELECT nome_completo, data_confirmacao FROM convidados WHERE confirmado = 1 ORDER BY data_confirmacao DESC");
$res_pendentes = $conn->query("SELECT nome_completo FROM convidados WHERE confirmado = 0 ORDER BY nome_completo ASC");
$res_presentes = $conn->query("SELECT nome FROM presentes WHERE escolhido = 1 ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Davi & Fernanda</title>

    <meta property="og:title" content="Painel Davi & Fernanda">
    <meta property="og:image" content="../images/daviefernanda05.png">

    <link rel="icon" type="image/png" href="../images/cadeado.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: url("../images/daviefernanda05.png") no-repeat center center fixed; 
            background-size: cover; min-height: 100vh; padding: 40px 20px; 
        }
        body::before { content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); z-index: -1; }
        .container { max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        header h1 { font-family: 'Montserrat', sans-serif; font-size: 26px; color: #fff; font-weight: 600; }
        .btn-sair { text-decoration: none; font-size: 11px; color: #fff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 30px; text-transform: uppercase; transition: 0.3s; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        .card { background: rgba(255, 255, 255, 0.95); padding: 30px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .card h2 { font-family: 'Montserrat', sans-serif; font-size: 18px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; color: #2d3e27; }
        .badge { font-family: 'Montserrat', sans-serif; background: #4A6741; color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .scroll-list { max-height: 450px; overflow-y: auto; }
        .item { padding: 15px 0; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .nome { font-weight: 600; color: #333; font-size: 14px; }
        .nome-pendente { font-weight: 600; color: #222 !important; font-size: 14px; }
        .presente-item { padding: 12px 0; font-size: 14px; color: #222; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.03); display: flex; align-items: center; }
        .presente-item::before { content: "✦"; color: #c5a059; margin-right: 10px; font-size: 12px; }
        .data { font-size: 11px; color: #888; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Painel de Gestão</h1>
        <a href="?sair=1" class="btn-sair">Sair</a>
    </header>
    <div class="grid">
        <div class="card">
            <h2>Confirmados <span class="badge"><?= $res_confirmados->num_rows ?></span></h2>
            <div class="scroll-list">
                <?php while($c = $res_confirmados->fetch_assoc()): ?>
                <div class="item"><span class="nome"><?= $c['nome_completo'] ?></span><span class="data"><?= date('d/m H:i', strtotime($c['data_confirmacao'])) ?></span></div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="card">
            <h2>Aguardando <span class="badge" style="background:#777;"><?= $res_pendentes->num_rows ?></span></h2>
            <div class="scroll-list">
                <?php while($p = $res_pendentes->fetch_assoc()): ?>
                <div class="item"><span class="nome-pendente"><?= $p['nome_completo'] ?></span></div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="card">
            <h2>Presentes <span class="badge" style="background:#c5a059;"><?= $res_presentes->num_rows ?></span></h2>
            <div class="scroll-list">
                <?php while($pr = $res_presentes->fetch_assoc()): ?>
                <div class="presente-item"><?= $pr['nome'] ?></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>