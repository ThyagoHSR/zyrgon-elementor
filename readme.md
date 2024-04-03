# Widgets personalizados para o Elementor

## Descrição

Este repósitorio destinado a compartilhamentos de códigos.


## Como Executar

1. Crie um plugin para registrar os widgets no elementor.
2. Crie uma função para inicializar os widgets ex:

```bash
public function init_widgets() {
 require_once( __DIR__ . '/widgets/widget-slider-about.php');
	    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \My_Widget_slider_about() );

        });
	}
```
## Widget Carousel about us => 

<img src="imgs/carousel-aboutus-image.webp">