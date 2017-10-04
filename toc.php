<?php

// Нам нужен фильтр контента чтоб прописать всем H2 и H3 атрибут id
add_filter('the_content', 'abv_add_ids');

function abv_add_ids($content) {
  $html = str_get_html($content);

  $i = 1;

  foreach($html->find('h2, h3') as $element) {
    // Если у тэга уже есть id - мы его не трогаем. Иначе генерируем свой
    if ( !isset($element->id) || empty($element->id) ) {
      $element->id = 'toc_id' . $i;
      $i++;
    }
  }

  return $html->save();
}

add_shortcode('abv_toc', 'abv_toc_shortcode');

function abv_toc_shortcode() {
  ob_start();

  remove_shortcode('abv_toc', 'abv_toc_shortcode'); // отключаем наш шорткод, иначе он будет вызываться в цикле бесконечно

  $content = apply_filters('the_content', get_the_content() ); // get_the_content не вызывает других фильтров, наш фильтр выше не отработает, поэтому вызываем их принудительно

  $html = str_get_html( $content );

  echo '<ul class="abv_toc">';

  foreach($html->find('h2, h3') as $element) {
    $class = ($element->tag == 'h2') ? 'abv_toc_h2' : 'abv_toc_h3';
    ?>

    <li class="abv_toc_item <?=$class;?>">
      <a href="#<?=$element->id;?>">
        <?=$element->innertext;?>
      </a>
    </li>

    <?php
  }

  echo '</ul>';

  add_shortcode('abv_toc', 'abv_toc_shortcode'); // возвращаем обработку нашего шорткода, если он будет в другом месте

  return ob_get_clean();
}
