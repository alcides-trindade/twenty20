function toggleMenu() {
    document.querySelector(".nav-links").classList.toggle("show");
}

// Permitir clique no dropdown no mobile
document.querySelector(".dropdown > a").addEventListener("click", function(e) {
    e.preventDefault();
    document.querySelector(".dropdown").classList.toggle("show");
});

// Para verficar os dados do formulário
function verificarNome(input) {
    if (input.value.length < 4) {
        alert("O nome deve ter pelo menos 4 caracteres.");
    } 
}

function verificarApelido(input) {
    if (input.value.length < 4) {
        alert("O apelido deve ter pelo menos 4 caracteres.");
    } 
}

function verificarData(input) {
    let hoje= new Date();
    let nascimento= new Date(input.value);
    let idade= hoje.getFullYear() - nascimento.getFullYear();
    let mesAtual= hoje.getMonth();
    let mesNascimento= nascimento.getMonth();
    if (mesAtual < mesNascimento || (mesAtual === mesNascimento && hoje.getDate() < nascimento.getDate())) {
    idade--;
    }
    if (idade < 18) {
        alert("Você deve ter pelo menos 18 anos para se registrar.");
    } 
}

function verificarTel(input) {
    let tel= /^([0-9]{9,9}|)$/
    if (!tel.test(input.value)) {
        alert("O número de telefone não é válido");
    }
}

function verificarEmail(input) {
    let regex= /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i
    if(!regex.test(input.value)){
     alert("O E-mail é inválido");
    }
 }

 // Para validar o formulário
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('contatoForm');
  const successMessage = document.getElementById('successMessage');
  
    console.log("Form encontrado:", form);


  if (!form) {
    console.error('⚠️ Formulário com id="contatoForm" não encontrado!');
    return;
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    if (successMessage) {
      successMessage.style.display = 'block';

      form.reset();

      setTimeout(() => {
        successMessage.style.display = 'none';
      }, 5000);
    }
  });
});

  
   