jQuery(document).ready(function ($) {
    // Captura a lista de itens do slider
    var $sliderList = $('.slider_about .swiper-wrapper');
    var $caixaImagemList = $('.caixa-imagem');

    // Inicializa o índice do slide atual
    var currentIndex = 0;

    // Atualiza a exibição do slider com efeito de transição lateral
    function updateSlider(direction) {
        // Esconde todos os slides
        $sliderList.children().css('display', 'none');

        var marginLeftStart = '-100%'; // Posição inicial à direita
        var marginLeftEnd = '0'; // Posição final central

        // Define a posição inicial e final dependendo da direção do slide
        if (direction === 'next') {
            marginLeftStart = '-100%'; // Posição inicial à esquerda
            marginLeftEnd = '0'; // Posição final central
        } else if (direction === 'prev') {
            marginLeftStart = '100%'; // Posição inicial à direita
            marginLeftEnd = '0'; // Posição final central
        }

        // Exibe apenas o slide atual com efeito de slide lateral
        $sliderList.children().eq(currentIndex).css({
            'display': 'block',
            'opacity': '1',
            'marginLeft': marginLeftStart // Começa da posição inicial
        }).animate({
            'marginLeft': marginLeftEnd // Move para a posição final
        }, 700); // Ajuste a velocidade do slide conforme necessário

        // Atualiza a aparência das caixas de imagem
        $caixaImagemList.removeClass('selecionado');
        $caixaImagemList.eq(currentIndex).addClass('selecionado');
    }

    // Função para avançar para o próximo slide
    function nextSlide() {
        currentIndex = (currentIndex + 1) % $sliderList.children().length;
        updateSlider('next'); // Define a direção como 'next' para o próximo slide
    }

    // Função para voltar para o slide anterior
    function prevSlide() {
        currentIndex = (currentIndex - 1 + $sliderList.children().length) % $sliderList.children().length;
        updateSlider('prev'); // Define a direção como 'prev' para o slide anterior
    }

    // Inicializa o slider
    updateSlider();

    // Adiciona eventos aos botões de navegação
    $('.swiper-button-next').on('click', function() {
        nextSlide();
    });

    $('.swiper-button-prev').on('click', function() {
        prevSlide();
    });

    // Adicionar evento de clique a cada caixa de imagem
    $caixaImagemList.each(function(index) {
        // Adicionar evento de clique
        $(this).on('click', function() {
            // Navegar para o slide correspondente com transição de slide
            currentIndex = index;
            updateSlider();
        });
    });

    // Define um intervalo para avançar automaticamente para o próximo slide a cada 3 segundos (3000 milissegundos)
    var interval = setInterval(nextSlide, 5000);

    // Pára o intervalo quando o mouse passa sobre o slider
    $('.slider_about').mouseenter(function() {
        clearInterval(interval);
    });

    // Reseta o intervalo quando o mouse sai do slider
    $('.slider_about').mouseleave(function() {
        interval = setInterval(nextSlide, 5000);
    });
});

