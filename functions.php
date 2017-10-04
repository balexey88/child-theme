<?php
require_once 'toc.php';
require_once 'simple_html_dom.php';

function my_theme_enqueue_styles() {

    $parent_style = 'twentyseventeen-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style( 'oswald-font', '//fonts.googleapis.com/css?family=Oswald' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

add_action( 'admin_enqueue_scripts', 'abv_admin_scripts2' );

function abv_admin_scripts2() {
  wp_enqueue_script('iris');
  wp_enqueue_style('iris');
  wp_enqueue_script('abv-admin1', get_stylesheet_directory_uri() . '/js/admin.js', array('jquery', 'iris'), '', true );
}

function my_title($title) {
    $title = $title . ' - ' . date('d.m.Y');

    return $title;
}

// add_filter('the_title', 'my_title');

add_shortcode('two_columns', 'two_columns_func');

function two_columns_func($atts, $content) {
  ob_start();
  ?>

    <div class="two_columns">
      <?=do_shortcode($content);?>
    </div>

    <div class="clear"></div>

  <?php
  return ob_get_clean();
}


add_shortcode('column1', 'column1_func');
add_shortcode('column2', 'column1_func');

function column1_func($atts, $content) {
  ob_start();
  ?>

    <div class="column">
      <?=$content;?>
    </div>

  <?php

  return ob_get_clean();
}

// add_shortcode('column2', 'column2_func');

function column2_func($atts, $content) {
  ob_start();
  ?>

    <div class="column_right">
      <?=$content;?>
    </div>

  <?php

  return ob_get_clean();
}

remove_action('the_content', 'wpautop');

add_action('add_meta_boxes_page', 'my_add_metabox');

function my_add_metabox($post) {
    add_meta_box(
        'my-meta-id',
        __( 'My metabox', 'my-text-domain' ),
        'render_my_metabox',
        'page',
        'side',
        'default'
    );
}

function render_my_metabox($post) {
    $value = get_post_meta($post->ID, 'my_phone', true);
    $select = get_post_meta($post->ID, 'my_sel', true);
    $check = get_post_meta($post->ID, 'my_check', true);
    ?>

    <p>
    <input type="text" name="my_phone" id="my_phone" value=<?=$value;?> >
    </p>

    <select name="my_sel">
      <option value="24" <?php if ($select == 24) echo 'selected="selected"';?> >Вариант 24</option>
      <option value="abc" <?php if ($select == 'abc') echo 'selected="selected"';?> >Другой вариант</option>
    </select>

    <p>
      <label for="my_check">
        <input type="checkbox" name="my_check" id="my_check" value="1" <?php if ($check) echo 'checked="checked"';?> >
        Отметка
      </label>
    </p>

    <?php
}


add_action('save_post', 'my_metabox_save');

function my_metabox_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    if ( !isset($_POST['my_phone']) ) return $post_id;

    // Sanitize the user input.
    $mydata = sanitize_text_field( $_POST['my_phone'] );
    $select = sanitize_text_field( $_POST['my_sel'] );

    $check = isset($_POST['my_check']) ? 1 : 0;

    // Update the meta field.
    update_post_meta( $post_id, 'my_phone', $mydata );
    update_post_meta( $post_id, 'my_sel', $select );


    update_post_meta( $post_id, 'my_check', $check );


}

class My_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname' => 'my_widget',
            'description' => 'My Widget description for WP Admin',
        );

        parent::__construct( 'my_widget', 'My Widget', $widget_ops );
    }

    public function widget( $args, $instance ) {
      echo $args['before_widget'];

    if ( ! empty( $instance['title'] ) ) {
    	echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    }

    if ( isset($instance['echo_date']) && $instance['echo_date']) {
      echo date('d.m.Y');
    }

    echo $args['after_widget'];
}


    public function form( $instance ) {
      $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'My Widget', 'text_domain' );
      $echo_date = ! empty( $instance['echo_date'] ) ? $instance['echo_date'] : '';

    ?>

    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'echo_date' ) ); ?>"><?php esc_attr_e( 'Echo date:', 'text_domain' ); ?></label>
        <input class="" id="<?php echo esc_attr( $this->get_field_id( 'echo_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'echo_date' ) ); ?>" type="checkbox" value="1" <?php if ($echo_date) echo 'checked="checked"'; ?>>
    </p>

    <?php
}

public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['echo_date'] = ( ! empty( $new_instance['echo_date'] ) ) ? strip_tags( $new_instance['echo_date'] ) : '';

    return $instance;
}

}

add_action( 'widgets_init', 'ab_register_my_widget');

function ab_register_my_widget() {
    register_widget( 'My_Widget' );
}

add_filter( 'manage_abv_portfolio_posts_columns' , 'abv_posts_columns' );

function abv_posts_columns( $columns ) {
    $columns['title'] = __( 'My Title', 'text_domain' );
    unset($columns['author']);
    $columns['my_column'] = __( 'My Column', 'text_domain' );

    return $columns;
}

add_action( 'manage_abv_portfolio_posts_custom_column' , 'abv_posts_columns_content', 10, 2 );

function abv_posts_columns_content( $column, $post_id ) {
    if ($column == 'my_column') {
        echo $post_id;
    }
}

// ************************************

add_action( 'admin_menu', 'abv_menu_pages' );

function abv_menu_pages() {
    add_menu_page(
        __('My Settings Page', 'text_domain'), // $page_title
        __('My Settings', 'text_domain'), // $menu_title
        'manage_options', // $capability, Admin user
        'abv_my_settings', // $menu_slug
        'abv_my_settings_render', // $function
        'dashicons-welcome-learn-more', // $icon_url
        40 // $position
    );

    add_submenu_page(
        'abv_my_settings', // $parent_slug
        __('My Settings Subpage', 'text_domain'), // $page_title
        __('More Settings', 'text_domain'), // $menu_title
        'manage_options', // $capability, Admin user
        'abv_my_more_settings', // $menu_slug
        'abv_my_more_settings_render' // $function
    );
}

function abv_my_settings_render() {
    ?>

    <!-- Стандартная разметка Wordpress -->
    <div class="wrap">

        <!-- Заголовок страницы настроек  -->
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <!-- Показывать сообщения админ-панели  -->
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php
            // Добавить служебные поля Wordpress
            settings_fields('abv_my_settings');

            // Отобразить зарегистрированные поля
            do_settings_sections('abv_my_settings');

            // Добавить кнопку сохранения
            submit_button();
            ?>
        </form>
    </div>

    <?php
}


function abv_my_more_settings_render() {}

  add_action( 'admin_init', 'abv_register_settings' );

  function abv_register_settings() {
      add_settings_section(
          'abv_setting_section', // $id, поля настроек привязываются к этому $id
          __('Example settings section in reading', 'text_domain'), // $title - заголовок секции
          'abv_setting_section_render', // $function - функция, которая будет выводить секцию
          // '', // $function - функция, которая будет выводить секцию
          'abv_my_settings' // $menu_slug страницы настроек
      );

      add_settings_field(
          'abv_my_setting', // $name - имя поля в базе данных
          __('Setting', 'text_domain'), // $title - заголовок секции
          'abv_setting_field_function', // $function - функция, которая будет выводить поле
          'abv_my_settings', // $menu_slug страницы настроек
          'abv_setting_section', // $id секции
          ['name' => 'abv_my_setting']
      );

      add_settings_field(
          'abv_my_setting2', // $name - имя поля в базе данных
          __('Setting 2', 'text_domain'), // $title - заголовок секции
          'abv_setting_field_function', // $function - функция, которая будет выводить поле
          'abv_my_settings', // $menu_slug страницы настроек
          'abv_setting_section', // $id секции
          ['name' => 'abv_my_setting2']
      );

      add_settings_section(
          'abv_color_section', // $id, поля настроек привязываются к этому $id
          __('Choose colors', 'text_domain'), // $title - заголовок секции
          '', // $function - функция, которая будет выводить секцию
          'abv_my_settings' // $menu_slug страницы настроек
      );

      add_settings_field(
          'abv_menu_color', // $name - имя поля в базе данных
          __('Menu Color', 'text_domain'), // $title - заголовок секции
          'abv_setting_field_function', // $function - функция, которая будет выводить поле
          'abv_my_settings', // $menu_slug страницы настроек
          'abv_color_section', // $id секции
          [
            'name' => 'abv_menu_color',
            'class' => 'abv_color_select',
          ]
      );

      register_setting(
          'abv_my_settings', // $menu_slug страницы настроек
          'abv_my_setting' // $name - имя поля в базе данных
      );

      register_setting('abv_my_settings', 'abv_my_setting2');
      register_setting('abv_my_settings', 'abv_menu_color');
  }

  function abv_setting_section_render() {
      echo '<p class="description">Intro text for our settings section</p>';
  }

  function abv_setting_field_function($args) {
    $name = $args['name'];
      $value = get_option($name);
      $class = isset($args['class']) ? $args['class'] : '';

      echo '<input type="text" class="regular-text ' . $class. '" id="' .$name .'" name="' . $name .'" value="' . $value . '">';
  }

add_action('wp_head', 'abv_menu_color');

function abv_menu_color() {
  ?>
  <style>
  .navigation-top a {
    color: <?=get_option('abv_menu_color');?>;
  }
  </style>
  <?php
}
