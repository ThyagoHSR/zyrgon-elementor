jQuery(document).ready(function ($) {
    // Captura a lista de itens do slider
    var $sliderList = $('.slider_about .swiper-wrapper');
    var $caixaImagemList = $('.caixa-imagem');

    // Inicializa o índice do slide atual
    var currentIndex = 0;

    // Atualiza a exibição do slider com efeito de transição
    function updateSlider() {
        // Esconde todos os slides
        $sliderList.children().hide();
        // Exibe apenas o slide atual com efeito de fade-in
        $sliderList.children().eq(currentIndex).fadeIn();

        // Atualiza a aparência das caixas de imagem
        $caixaImagemList.removeClass('selecionado');
        $caixaImagemList.eq(currentIndex).addClass('selecionado');
    }

    // Função para avançar para o próximo slide
    function nextSlide() {
        currentIndex = (currentIndex + 1) % $sliderList.children().length;
        updateSlider();
    }

    // Função para voltar para o slide anterior
    function prevSlide() {
        currentIndex = (currentIndex - 1 + $sliderList.children().length) % $sliderList.children().length;
        updateSlider();
    }

    // Inicializa o slider
    updateSlider();

    // Inicializa o Swiper
    var swiper = new Swiper('.slider_about', {
        // Configurações do Swiper
        // Adicione suas configurações aqui
        on: {
            slideChange: function() {
                // Atualiza o índice do slide atual
                currentIndex = swiper.activeIndex;
                // Atualiza a exibição do slider
                updateSlider(currentIndex);
            },
        },
    });

    // Adiciona eventos aos botões de navegação
    $('.swiper-button-next').on('click', function() {
        nextSlide();
        swiper.slideNext();
    });

    $('.swiper-button-prev').on('click', function() {
        prevSlide();
        swiper.slidePrev();
    });

    // Adicionar evento de clique a cada caixa de imagem
    $caixaImagemList.each(function(index) {
        // Adicionar evento de clique
        $(this).on('click', function() {
            // Navegar para o slide correspondente com transição de slide
            swiper.slideTo(index);
            currentIndex = index;
            updateSlider(currentIndex);
        });
    });
});
