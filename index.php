<?php
include 'config.php';

$sql = "SELECT * FROM presentes ORDER BY id ASC";
$result = $conn->query($sql);

$presentes = [];
while ($row = $result->fetch_assoc()) {
    $presentes[$row['id']] = $row;
}

function item($id, $nome, $presentes) {
    if (!isset($presentes[$id])) return "";
    
    $isEscolhido = ($presentes[$id]['escolhido'] == 1);
    $checked = $isEscolhido ? 'checked disabled' : '';
    
    $onclick = !$isEscolhido ? "onclick=\"escolherPresente(this, $id, '$nome')\"" : "";
    
    return "
    <div class='item'>
        <input type='checkbox' $checked $onclick id='item-$id'>
        <label for='item-$id'>$nome</label>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Davi & Fernanda</title>

    <meta property="og:type" content="website">
    <meta property="og:title" content="Davi & Fernanda - 12.04.2026">
    <meta property="og:description" content="Ficaremos muito felizes com sua presença em nosso casamento. Confira nossa lista de presentes e confirmação de presença.">
    <meta property="og:image" content="images/d&f.png">
    <meta property="og:image:width" content="300">
    <meta property="og:image:height" content="300">
    <meta property="og:url" content="https://seusite.com.br">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/d&f.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Josefin+Sans:wght@100..700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .hero-overlay h1 {
            margin-bottom: 20px;
            font-size: 5rem;
        }
        
        .hero-overlay p {
            margin-top: 10px;
            font-size: 1.5rem;
            letter-spacing: 5px;
        }

        .container_colunas_cozinha {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            width: fit-content;
            text-align: center;
        }

        .scroll-indicator span {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 12px;
            font-family: 'Montserrat', sans-serif;
            text-shadow: 0px 2px 4px rgba(0,0,0,0.8); 
            font-weight: 600;
        }

        .arrow {
            width: 15px;
            height: 15px;
            border-bottom: 3px solid white;
            border-right: 3px solid white;
            transform: rotate(45deg);
            animation: bounceGlow 2s infinite;
            display: inline-block;
        }

        @media (max-width: 1024px) {
            .container_colunas_cozinha {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero-overlay h1 {
                font-size: 3.5rem;
            }
            
            .hero-overlay p {
                font-size: 1.2rem;
                letter-spacing: 3px;
            }

            .container_colunas_cozinha {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .pix-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .sobre {
                flex-direction: column;
                text-align: center;
            }

            .sobre img {
                max-width: 100%;
                margin-bottom: 20px;
            }

            .scroll-indicator.final-lista {
                position: relative !important;
                left: auto !important;
                transform: none !important;
                margin: 40px auto !important;
                display: flex !important;
                flex-direction: row !important;
                width: fit-content !important;
                padding: 15px 25px !important;
                box-sizing: border-box;
            }
            
            .final-lista .arrow {
                margin-left: 15px !important;
                margin-top: 0 !important;
                flex-shrink: 0;
            }

            .final-lista span {
                margin-bottom: 0 !important;
                font-size: 0.7rem;
            }
        }

        @keyframes bounceGlow {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0) rotate(45deg); }
            40% { transform: translateY(-10px) rotate(45deg); }
            60% { transform: translateY(-5px) rotate(45deg); }
        }

        .scroll-indicator.final-lista {
            position: relative;
            margin: 40px auto; 
            color: #8da399;
            background: rgba(141, 163, 153, 0.1);
            padding: 20px 30px;
            border-radius: 50px;
            display: flex;
            flex-direction: row; 
            align-items: center;
            justify-content: center;
            width: fit-content;
            left: 0;
            transform: none;
        }
        
        .final-lista .arrow { 
            border-color: #8da399; 
            margin-left: 15px; 
            margin-bottom: 0; 
            animation: bounceGlowHorizontal 2s infinite;
        }

        .final-lista span { 
            color: #6a7d75; 
            text-shadow: none; 
            margin-bottom: 0; 
        }

        @keyframes bounceGlowHorizontal {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0) rotate(45deg); }
            40% { transform: translateY(5px) rotate(45deg); }
            60% { transform: translateY(3px) rotate(45deg); }
        }
    </style>
</head>
<body>

<section class="hero">
    <div class="hero-overlay" data-aos="zoom-in">
        <h1>Davi & Fernanda</h1>
        <p>12 • 04 • 2026</p>
    </div>
    
    <a href="#contagem" class="scroll-indicator">
        <span>Role para ver mais</span>
        <div class="arrow"></div>
    </a>
</section>

<section id="contagem" class="contagem-regressiva">
    <div class="container" data-aos="fade-up">
        <h3 id="countdown-title">Faltam apenas:</h3>
        <div id="countdown">
            <div class="tempo"><span id="days">00</span><p>Dias</p></div>
            <div class="tempo"><span id="hours">00</span><p>Horas</p></div>
            <div class="tempo"><span id="minutes">00</span><p>Minutos</p></div>
            <div class="tempo"><span id="seconds">00</span><p>Segundos</p></div>
        </div>
    </div>
</section>

<section class="secao-sobre">
    <div class="container">
        <div class="sobre" data-aos="fade-up">
            <img src="images/daviefernanda01.jpeg" alt="Noivos" loading="lazy">
            <div class="texto">
                <h2 class="titulo-sobre">Sobre nós</h2>
                <p>A noiva sempre amou histórias de amor. Livros com escritas emocionais e surpreendentes sempre fizeram parte de sua vida. Mal sabia ela que viveria uma história assim, e melhor ainda é saber que a história foi escrita pelo próprio Deus. Em uma quarta-feira comum, o Senhor dirigiu nossos passos até que nos encontrássemos, na faculdade, após a aula à noite. O Senhor fez com que o ordinário se transformasse em extraordinário. Depois do primeiro contato, da primeira conversa, ambos sabíamos que o que estava acontecendo não era normal, era uma conexão diferente. A conversa fluía livremente e a companhia um do outro se mostrou um ambiente de conforto e confiança. Depois de muitas dúvidas, orações e medos, o Senhor deu o selo que faltava, a confirmação que os corações precisavam. E com as alianças nos dedos, soubemos que a união não era só de duas pessoas, mas sim de dois propósitos, unidos para que o nome do Senhor seja glorificado pelo resto de nossas vidas. 💚</p>
            </div>
        </div>
    </div>
</section>

<section class="secao-inspiracoes">
    <div class="container">
        <div class="inspiracoes">
            <h2 class="titulo-sessao titulo-localizacao" data-aos="fade-up">Localização</h2>
            
            <div class="mapa-container" data-aos="fade-up">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3838.632483842183!2d-48.0534289!3d-15.8233379!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x935a32b674a09593%3A0x126e634c90531e5d!2sS%C3%ADtio%20Geranium!5e0!3m2!1spt-BR!2sbr!4v1710000000000!5m2!1spt-BR!2sbr" 
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                
                <div class="link-ambiente" style="margin-top: 25px; text-align: center;">
                    <p style="margin-bottom: 15px; font-size: 1.1rem; color: #ffffff; text-shadow: 1px 1px 10px rgba(0,0,0,0.5);">Para fotos e como chegar, use o botão abaixo:</p>
                    
                    <a href="https://maps.google.com/?q=S%C3%ADtio+Geranium+Brasilia" target="_blank" 
                       style="display: inline-block; padding: 15px 35px; background-color: #8da399; color: white; text-decoration: none; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: 0.3s;">
                       📍 Abrir no Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="secao-pix">
    <div class="container" data-aos="fade-up">
        <div class="pix-wrapper">
            <div class="pix-texto">
                <h2>Presentes e Mimos</h2>
                <p>Ficaremos muito felizes com sua escolha na lista abaixo para compor o nosso lar! Basta marcar o item que você deseja nos presentear.</p>
                <p>Caso prefira nos presentear de outra forma — como uma contribuição para nossa lua de mel — você pode utilizar o QR Code ao lado. Desde já, nosso muito obrigado! 💚</p>
                <div class="chave-pix">
                    <strong>Chave PIX:</strong><br> (61) 99570-9067 - NUBANK - Fernanda Santos Fagundes
                </div>
            </div>
            <div class="pix-qrcode">
                <img src="images/qrcode.jpg" alt="QR Code Pix">
                <span>Aponte a câmera do celular</span>
            </div>
        </div>
    </div>
</section>

<section class="secao-lista">
    <div class="container">
        <div class="presentes" data-aos="fade-up">
            <div class="cabecalho-lista">
                <h3>Lista de Presentes</h3>
                <h2>(Escolha com carinho 💕)</h2>
                <div class="cores-preferencia">
                    <span>Cores de preferência:</span>
                        <div class="cor-item"><span class="circulo inox"></span> Inox</div>
                        <div class="cor-item"><span class="circulo preto"></span> Preto</div>
                        <div class="cor-item"><span class="circulo verde"></span> Verde</div>
                        <div class="cor-item"><span class="circulo branco"></span> Branco</div>
                </div>
            </div>

            <h2 class="titulo-categoria">Cozinha</h2>
            <div class="container_colunas_cozinha">
                <div class="coluna_cozinha">
                    <?= item(1,'Abridor de latas',$presentes) ?>
                    <?= item(2,'Açucareiro',$presentes) ?>
                    <?= item(3,'Afiador de facas',$presentes) ?>
                    <?= item(4,'Amassador de alho',$presentes) ?>
                    <?= item(5,'Assadeira',$presentes) ?>
                    <?= item(6,'Avental',$presentes) ?>
                    <?= item(7,'Bandeija',$presentes) ?>
                    <?= item(8,'Boleira',$presentes) ?>
                    <?= item(9,'Coador de café',$presentes) ?>
                    <?= item(10,'Colheres de pau',$presentes) ?>
                    <?= item(11,'Colheres de silicone',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(12,'Conchas',$presentes) ?>
                    <?= item(13,'Colher de sobremesa',$presentes) ?>
                    <?= item(14,'Copo medidor',$presentes) ?>
                    <?= item(15,'Descascador de legumes',$presentes) ?>
                    <?= item(16,'Escorredor de louça',$presentes) ?>
                    <?= item(17,'Escorredor de macarrão',$presentes) ?>
                    <?= item(18,'Pilão socador de alho',$presentes) ?>
                    <?= item(19,'Kit facas',$presentes) ?>
                    <?= item(20,'Frigideira',$presentes) ?>
                    <?= item(21,'Garrafa térmica de café',$presentes) ?>
                    <?= item(22,'Jarra de vidro',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(23,'Jogo de copos',$presentes) ?>
                    <?= item(24,'Jogo de panelas',$presentes) ?>
                    <?= item(25,'Jogo de pratos',$presentes) ?>
                    <?= item(26,'Jogo de talheres',$presentes) ?>
                    <?= item(27,'Jogo de xícaras',$presentes) ?>
                    <?= item(28,'Kit pia',$presentes) ?>
                    <?= item(29,'Liquidificador',$presentes) ?>
                    <?= item(30,'Panela de pressão',$presentes) ?>
                    <?= item(31,'Pano de prato',$presentes) ?>
                    <?= item(32,'Peneira',$presentes) ?>
                    <?= item(33,'Porta frios',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(34,'Porta mantimentos',$presentes) ?>
                    <?= item(35,'Porta temperos',$presentes) ?>
                    <?= item(36,'Jogo de taças',$presentes) ?>
                    <?= item(37,'Potes',$presentes) ?>
                    <?= item(38,'Ralador',$presentes) ?>
                    <?= item(39,'Rodinho de pia',$presentes) ?>
                    <?= item(40,'Rolo para abrir massas',$presentes) ?>
                    <?= item(41,'Saleiro e pimenteiro',$presentes) ?>
                    <?= item(42,'Tábuas de corte',$presentes) ?>
                    <?= item(43,'Travessas',$presentes) ?>
                    <?= item(44,'Utensílios de silicone',$presentes) ?>
                    <?= item(45,'Tigelas',$presentes) ?>
                </div>
            </div>

            <h2 class="titulo-categoria">Quarto & Banheiro</h2>
            <div class="container_colunas_cozinha">
                <div class="coluna_cozinha">
                    <?= item(46,'Cabides',$presentes) ?><?= item(47,'Lençol',$presentes) ?><?= item(48,'Cobertor',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(49,'Espelho',$presentes) ?><?= item(50,'Jogo de cama',$presentes) ?><?= item(51,'Colcha',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(53,'Jogo de toalha de banho',$presentes) ?><?= item(54,'Jogo de toalha de rosto',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(55,'Porta sabonete',$presentes) ?><?= item(56,'Porta escova',$presentes) ?><?= item(57,'Lixeira',$presentes) ?>
                </div>
            </div>

            <h2 class="titulo-categoria">Lavanderia & Limpeza</h2>
            <div class="container_colunas_cozinha">
                <div class="coluna_cozinha">
                    <?= item(59,'Vassoura',$presentes) ?><?= item(60,'Rodo',$presentes) ?><?= item(61,'Panos de chão',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(64,'Mop giratório',$presentes) ?><?= item(65,'Aspirador de pó',$presentes) ?><?= item(66,'Ferro de passar',$presentes) ?>
                </div>
                <div class="coluna_cozinha">
                    <?= item(67,'Tábua de passar',$presentes) ?><?= item(68,'Cesto de roupas',$presentes) ?><?= item(69,'Baldes ou bacias',$presentes) ?>
                </div>
            </div>

            <h2 class="titulo-categoria">Itens Especiais</h2>
            <div class="container_colunas_cozinha">
                <div class="coluna_cozinha"><?= item(70,'Geladeira',$presentes) ?><?= item(71,'Máquina de lavar',$presentes) ?></div>
                <div class="coluna_cozinha"><?= item(72,'Televisão',$presentes) ?><?= item(73,'Guarda roupas',$presentes) ?></div>
                <div class="coluna_cozinha"><?= item(74,'Micro-ondas',$presentes) ?><?= item(75,'Batedeira',$presentes) ?></div>
                <div class="coluna_cozinha"><?= item(76,'Air Fryer',$presentes) ?><?= item(77,'Sofá',$presentes) ?></div>
            </div>

            <a href="#confirmacao" class="scroll-indicator final-lista">
                <span>Agora, confirme sua presença abaixo</span>
                <div class="arrow"></div>
            </a>
        </div>
    </div>
</section>

<section id="confirmacao" class="secao-rsvp">
    <div class="container" data-aos="fade-up">
        <div class="rsvp-card">
            <h2 class="titulo-sessao titulo-confirmacao">Confirmação de Presença</h2>
            <div id="rsvp-fluxo" class="rsvp-box">
                <p class="instrucao-rsvp">Por favor, digite seu nome como está no convite para liberar sua confirmação:</p>
                <div class="busca-container">
                    <input type="text" id="nome-busca" placeholder="Digite seu nome conforme o convite">
                    <button onclick="verificarLista()">
                        <span>Verificar</span>
                        <i class="seta">→</i>
                    </button>
                </div>
                <div id="rsvp-resultado"></div>
            </div>
        </div>
    </div>
    <footer>
      Desenvolvido por Gabriel Fagundes :: 
    </footer>
</section>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    const targetDate = new Date("2026-04-12T00:00:00").getTime();
    const countdownTitle = document.getElementById("countdown-title");
    const countdownContainer = document.getElementById("countdown");

    function updateCountdown() {
        const now = new Date().getTime();
        const difference = targetDate - now;

        if (difference < 0) {
            countdownTitle.innerText = "O grande dia já aconteceu! 💕";
            countdownContainer.style.display = "none";
            clearInterval(timerInterval);
            return;
        }

        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        document.getElementById("days").innerText = String(days).padStart(2, '0');
        document.getElementById("hours").innerText = String(hours).padStart(2, '0');
        document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
        document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');
    }

    const timerInterval = setInterval(updateCountdown, 1000);
    updateCountdown();
</script>
<script src="script.js"></script>

</body>
</html>
