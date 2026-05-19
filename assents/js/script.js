// Confirmação ao excluir
function confirmarExclusao() {
    return confirm("Tem certeza que deseja excluir?");
}

// Mensagem simples de sucesso (opcional)
function mostrarMensagem(msg) {
    alert(msg);
}

let index = 0;
const slides = document.querySelectorAll(".slide");

function trocarSlide() {
    slides.forEach((slide) => slide.classList.remove("active"));

    index++;
    if (index >= slides.length) {
        index = 0;
    }

    slides[index].classList.add("active");
}

setInterval(trocarSlide, 5000);