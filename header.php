<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- Grain Overlay -->
  <svg class="grain" aria-hidden="true">
    <filter id="grain-filter">
      <feTurbulence type="fractalNoise" baseFrequency="0.75" numOctaves="4" stitchTiles="stitch"/>
      <feColorMatrix type="saturate" values="0"/>
    </filter>
    <rect width="100%" height="100%" filter="url(#grain-filter)"/>
  </svg>

  <!-- Progress Bar -->
  <div class="progress-bar" id="progressBar" aria-hidden="true"></div>

  <!-- Navigation -->
  <nav class="nav" data-component="nav">
    <div class="nav__inner">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav__logo">
        <?php bloginfo( 'name' ); ?>
      </a>
      <?php
      wp_nav_menu( array(
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'nav__links',
        'menu_id'        => 'navLinks',
        'fallback_cb'    => 'yigeren_fallback_menu',
      ) );
      ?>
      <button class="nav__toggle" aria-label="菜单" onclick="document.getElementById('navLinks').classList.toggle('open')">
        <span></span>
      </button>
    </div>
  </nav>

  <main data-component="main">
<?php
/**
 * Fallback menu if no menu is assigned
 */
function yigeren_fallback_menu() {
  echo '<ul class="nav__links" id="navLinks">';
  echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">首页</a></li>';
  $cats = array( 'life', 'moto', 'cat', 'photo', 'notes' );
  $labels = array( '生活', '摩托', '猫咪', '摄影', '笔记' );
  foreach ( $cats as $i => $slug ) {
    $cat = get_category_by_slug( $slug );
    if ( $cat ) {
      echo '<li><a href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $labels[ $i ] ) . '</a></li>';
    }
  }
  // Archive page
  $archive_page = get_page_by_path( 'archive' );
  if ( $archive_page ) {
    echo '<li><a href="' . esc_url( get_permalink( $archive_page ) ) . '">归档</a></li>';
  }
  // About page
  $about_page = get_page_by_path( 'about' );
  if ( $about_page ) {
    echo '<li><a href="' . esc_url( get_permalink( $about_page ) ) . '">关于</a></li>';
  }
  echo '</ul>';
}
?>
