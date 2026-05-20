/* ==========================================================================
   1. CONTAGEM REGRESSIVA
   ========================================================================== */
function atualizarContagem() {
    const dataCasamento = new Date("April 12, 2026 00:00:00").getTime();
    const agora = new Date().getTime();
    const diferenca = dataCasamento - agora;

    const elDias = document.getElementById("days");
    const elHoras = document.getElementById("hours");
    const elMinutos = document.getElementById("minutes");
    const elSegundos = document.getElementById("seconds");

    if (diferenca <= 0) {
        const container = document.getElementById("countdown");
        if (container) container.innerHTML = "<h2>É HOJE! 💕</h2>";
        return;
    }

    const dias = Math.floor(diferenca / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diferenca % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutos = Math.floor((diferenca % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((diferenca % (1000 * 60)) / 1000);

    if (elDias) elDias.innerText = dias.toString().padStart(2, '0');
    if (elHoras) elHoras.innerText = horas.toString().padStart(2, '0');
    if (elMinutos) elMinutos.innerText = minutos.toString().padStart(2, '0');
    if (elSegundos) elSegundos.innerText = segundos.toString().padStart(2, '0');
}

setInterval(atualizarContagem, 1000);
atualizarContagem();

/* ==========================================================================
   2. RESERVA DE PRESENTES
   ========================================================================== */
function escolherPresente(checkbox, id, nomePresente) {
    if (!checkbox.checked) {
        checkbox.checked = true; 
        return;
    }

    Swal.fire({
        title: 'Você escolheu este presente?',
        text: `Você selecionou "${nomePresente}". Esta ação nos ajuda a organizar nossa lista e não pode ser desfeita. Podemos seguir?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8da399',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sim, desejo presentear',
        cancelButtonText: 'Agora não',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("escolher.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + id
            })
            .then(() => {
                Swal.fire({
                    title: 'Muito obrigado! 💕',
                    text: `Ficamos muito felizes! O item "${nomePresente}" foi reservado para você.`,
                    icon: 'success',
                    confirmButtonColor: '#8da399'
                }).then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                console.error("Erro ao reservar:", error);
                checkbox.checked = false;
            });
        } else {
            checkbox.checked = false;
        }
    });
}

/* ==========================================================================
   3. RSVP RESTRITO (SISTEMA DE BUSCA E SELEÇÃO)
   ========================================================================== */

// Listener para a tecla Enter no campo de busca
document.addEventListener("DOMContentLoaded", function() {
    const inputBusca = document.getElementById("nome-busca");
    if (inputBusca) {
        inputBusca.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                verificarLista();
            }
        });
    }
});

function verificarLista() {
    const nomeInput = document.getElementById("nome-busca");
    const resultadoDiv = document.getElementById("rsvp-resultado");
    
    // ALTERAÇÃO AQUI: .trim() limpa as pontas e o .replace limpa espaços duplos no meio
    const nome = nomeInput.value.trim().replace(/\s+/g, ' ');

    if (nome.length < 3) {
        Swal.fire({ title: 'Ops!', text: 'Digite seu nome completo.', icon: 'warning', confirmButtonColor: '#8da399' });
        return;
    }

    resultadoDiv.innerHTML = "<p style='margin-top:20px; color:#8da399;'>Buscando...</p>";

    fetch("verificar_convidado.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "nome=" + encodeURIComponent(nome)
    })
    .then(response => response.json())
    .then(data => {
        resultadoDiv.innerHTML = ""; // Limpa o "Buscando..."

        if (data.sucesso) {
            if (data.ja_confirmado) {
                Swal.fire({ title: 'Olá!', text: 'Sua presença já está confirmada! ✅', icon: 'info', confirmButtonColor: '#8da399' });
                return;
            }

            // Se tiver apenas 1 resultado, vai direto pro popup
            if (data.lista && data.lista.length === 1) {
                modalConfirmar(data.lista[0].id, data.lista[0].nome);
            } 
            // Se tiver mais de 1 (ambiguidade), mostra a lista de botões
            else if (data.lista && data.lista.length > 1) {
                let html = `<p class='instrucao-rsvp' style='margin-top:20px;'>Encontramos mais de um nome. Qual deles é você?</p>`;
                html += `<div class='lista-escolha' style='display:flex; flex-direction:column; gap:10px; margin-top:15px;'>`;
                data.lista.forEach(convidado => {
                    html += `
                        <button class='btn-nome-encontrado' 
                                style='padding:12px; border-radius:30px; border:2px solid #8da399; font-weight:800; background:white; color:#222222; cursor:pointer;'
                                onclick="modalConfirmar(${convidado.id}, '${convidado.nome}')">
                            ${convidado.nome}
                        </button>`;
                });
                html += "</div>";
                resultadoDiv.innerHTML = html;
            }
        } else {
            Swal.fire({ title: 'Não encontrado', text: 'Não achamos esse nome na lista de convidados.', icon: 'error', confirmButtonColor: '#d33' });
        }
    })
    .catch(error => {
        console.error("Erro ao verificar:", error);
        resultadoDiv.innerHTML = "";
        Swal.fire({ title: 'Erro', text: 'Erro ao conectar com o servidor.', icon: 'error' });
    });
}

function modalConfirmar(id, nome) {
    Swal.fire({
        title: 'Confirmar Presença?',
        text: `Olá ${nome}, deseja confirmar sua presença no nosso casamento?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8da399',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sim, confirmar! ❤️',
        cancelButtonText: 'Agora não',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            confirmarPresenca(id);
        }
    });
}

function confirmarPresenca(id) {
    Swal.fire({
        title: 'Confirmando...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch("confirmar_rsvp.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
    })
    .then(() => {
        Swal.fire({
            title: 'Presença Confirmada! 🎉',
            text: 'Mal podemos esperar por esse dia! Obrigado por confirmar.',
            icon: 'success',
            confirmButtonColor: '#8da399'
        }).then(() => {
            location.reload();
        });
    })
    .catch(error => {
        console.error("Erro ao confirmar:", error);
        Swal.fire({ title: 'Erro', text: 'Não conseguimos salvar sua presença.', icon: 'error' });
    });
}