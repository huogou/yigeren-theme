<?php
/**
 * 一个人的互联网笔记 - Theme Functions
 * @package yigeren
 * @version filemtime( get_stylesheet_directory() . "/style.css" )
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme Setup
 */
function yigeren_setup() {
    // Theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
    add_theme_support( 'custom-logo', array(
        'width'  => 200,
        'height' => 50,
    ) );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );

    // Image sizes
    add_image_size( 'article-cover', 880, 495, true );  // 16:9
    add_image_size( 'card-cover', 520, 347, true );     // 3:2
    add_image_size( 'photo-large', 680, 453, true );    // 3:2

    // Navigation menu
    register_nav_menus( array(
        'primary' => '主导航',
        'social'  => '社交链接',
    ) );
}
add_action( 'after_setup_theme', 'yigeren_setup' );

/**
 * Enqueue Styles and Scripts
 */
function yigeren_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'yigeren-fonts',
        'https://fonts.loli.net/css2?family=Noto+Serif+SC:wght@400;500;600&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Noto+Sans+SC:wght@300;400;500&family=Caveat:wght@400;500&display=swap',
        array(),
        null
    );

    // Theme CSS
    wp_enqueue_style(
        'yigeren-style',
        get_stylesheet_uri(),
        array( 'yigeren-fonts' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'yigeren_scripts' );

/**
 * Custom excerpt length
 */
function yigeren_excerpt_length( $length ) {
    return 40;
}
add_filter( 'excerpt_length', 'yigeren_excerpt_length' );

/**
 * Custom excerpt more text
 */
function yigeren_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'yigeren_excerpt_more' );

/**
 * Get category Chinese name mapping
 */
function yigeren_category_label( $cat_slug ) {
    $labels = array(
        'life'  => '生活',
        'moto'  => '摩托',
        'cat'   => '猫咪',
        'photo' => '摄影',
        'notes' => '笔记',
    );
    return isset( $labels[ $cat_slug ] ) ? $labels[ $cat_slug ] : $cat_slug;
}

/**
 * Get category English label mapping
 */
function yigeren_category_en( $cat_slug ) {
    $labels = array(
        'life'  => 'Life',
        'moto'  => 'Riding',
        'cat'   => 'Cat',
        'photo' => 'Photography',
        'notes' => 'Notes',
    );
    return isset( $labels[ $cat_slug ] ) ? $labels[ $cat_slug ] : $cat_slug;
}

/**
 * Get category kanji character
 */
function yigeren_category_kanji( $cat_slug ) {
    $kanji = array(
        'life'  => '日',
        'moto'  => '道',
        'cat'   => '猫',
        'photo' => '光',
        'notes' => '筆',
    );
    return isset( $kanji[ $cat_slug ] ) ? $kanji[ $cat_slug ] : '記';
}

/**
 * Get category description
 */
function yigeren_category_desc( $cat_slug ) {
    $descs = array(
        'life'  => '最近在骑车、拍照和陪猫晒太阳。',
        'moto'  => '两个轮子，一条路，够了。',
        'cat'   => '年糕，一只橘猫，正在认真地长大。',
        'photo' => '用相机记住那些不值得写文章但值得记住的瞬间。',
        'notes' => '想到什么就记下来，不一定完整，但值得留下。',
    );
    return isset( $descs[ $cat_slug ] ) ? $descs[ $cat_slug ] : '';
}

/**
 * Format post date in English short format
 */
function yigeren_short_date( $post = null ) {
    $post = get_post( $post );
    return date_i18n( 'M d', strtotime( $post->post_date ) );
}

/**
 * Format post date in full format
 */
function yigeren_full_date( $post = null ) {
    $post = get_post( $post );
    return date_i18n( 'M d, Y', strtotime( $post->post_date ) );
}

/**
 * Get post mood/tag from custom field or category
 */
function yigeren_post_mood( $post = null ) {
    $post = get_post( $post );
    $mood = get_post_meta( $post->ID, '_yigeren_mood', true );
    if ( ! $mood ) {
        $cats = get_the_category( $post );
        if ( ! empty( $cats ) ) {
            $mood = yigeren_category_label( $cats[0]->slug );
        }
    }
    return $mood;
}

/**
 * Custom query for category pages
 */
function yigeren_category_query( $cat_slug, $posts_per_page = 20 ) {
    return new WP_Query( array(
        'category_name'  => $cat_slug,
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
}

/**
 * Get recent posts grouped by month
 */
function yigeren_timeline_posts( $cat_slug = '', $limit = 30 ) {
    $args = array(
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ( $cat_slug ) {
        $args['category_name'] = $cat_slug;
    }
    $query = new WP_Query( $args );
    $grouped = array();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $month_key = date_i18n( 'Y-m', strtotime( get_the_date() ) );
            $grouped[ $month_key ][] = get_post();
        }
        wp_reset_postdata();
    }
    return $grouped;
}

/**
 * Get archive data grouped by year > month
 */
function yigeren_archive_data() {
    $posts = get_posts( array(
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
    $archive = array();
    foreach ( $posts as $post ) {
        $year  = date_i18n( 'Y', strtotime( $post->post_date ) );
        $month = date_i18n( 'n', strtotime( $post->post_date ) );
        $archive[ $year ][ $month ][] = $post;
    }
    return $archive;
}

/**
 * Widget areas (optional)
 */
function yigeren_widgets_init() {
    register_sidebar( array(
        'name'          => '页脚',
        'id'            => 'footer',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
    ) );
}
add_action( 'widgets_init', 'yigeren_widgets_init' );

/**
 * Disable emoji scripts for performance
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_scripts', 'print_emoji_script' );

/**
 * Disable comments completely
 */
function disable_comments_completely() {
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    add_filter('comments_open', '__return_false');
    add_filter('pings_open', '__return_false');
}
add_action('init', 'disable_comments_completely');
