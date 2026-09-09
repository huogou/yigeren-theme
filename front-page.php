<?php
/**
 * Front Page Template
 * @package yigeren
 */
get_header();
?>

<!-- Hero Banner -->
<header class="page-header hero-banner" data-component="hero">
    <div class="hero-bg">
      <img src="https://yaoqiang.xin/wp-content/uploads/2026/06/homepage-banner.png" alt="">
      <div class="hero-overlay"></div>
    </div>
    <div class="hero-content container">
      <span class="hero-kanji">記</span>
      <h1 class="hero-title">一个人的<br>互联网笔记</h1>
      <div class="hero-divider"></div>
      <p class="hero-tagline">骑着摩托去远方，回到家有猫在等</p>
      <p class="hero-desc">在这里记录生活里那些值得慢下来的瞬间</p>
      <div class="hero-categories">
        <a href="<?php echo esc_url( home_url( '/life/' ) ); ?>">
          <span class="hero-cat-icon">◐</span>生活
        </a>
        <a href="<?php echo esc_url( home_url( '/moto/' ) ); ?>">
          <span class="hero-cat-icon">◐</span>摩托
        </a>
        <a href="<?php echo esc_url( home_url( '/cat/' ) ); ?>">
          <span class="hero-cat-icon">◐</span>猫咪
        </a>
        <a href="<?php echo esc_url( home_url( '/photo/' ) ); ?>">
          <span class="hero-cat-icon">◐</span>光影
        </a>
        <a href="<?php echo esc_url( home_url( '/notes/' ) ); ?>">
          <span class="hero-cat-icon">◐</span>笔记
        </a>
      </div>
    </div>
    <div class="hero-scroll-hint">
      <span>向下探索</span>
      <svg width="16" height="24" viewBox="0 0 16 24" fill="none">
        <rect x="5.5" y="1" width="5" height="9" rx="2.5" stroke="currentColor" stroke-width="1"/>
        <circle cx="8" cy="4.5" r="0.8" fill="currentColor" class="scroll-dot"/>
        <path d="M3 16L8 21L13 16" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
  </header>

<!-- Recent Timeline -->
<section class="section" data-component="timeline">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Recent</span>
        <h2 style="margin-top:8px;">最近的生活</h2>
      </div>
    </div>
    <?php
    $recent = new WP_Query( array( 'posts_per_page' => 5, 'post_status' => 'publish' ) );
    if ( $recent->have_posts() ) :
      $current_month = '';
      while ( $recent->have_posts() ) : $recent->the_post();
        $post_month = date_i18n( 'F Y', strtotime( get_the_date() ) );
        if ( $post_month !== $current_month ) :
          $current_month = $post_month;
    ?>
    <div class="diary__month"><?php echo esc_html( $current_month ); ?></div>
    <?php endif; ?>
    <div class="diary__entry reveal">
      <div class="diary__dot"></div>
      <div class="diary__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
      <div class="diary__text">
        <a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a>
        <br><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?>
      </div>
      <?php $mood = yigeren_post_mood(); if ( $mood ) : ?>
      <span class="diary__mood"><?php echo esc_html( $mood ); ?></span>
      <?php endif; ?>
    </div>
    <?php
      endwhile;
      wp_reset_postdata();
    endif;
    ?>
  </div>
</section>

<!-- Motorcycle (latest post from moto category) -->
<?php
$moto_query = yigeren_category_query( 'moto', 1 );
if ( $moto_query->have_posts() ) :
  $moto_query->the_post();
?>
<section class="section" data-component="motorcycle">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Riding</span>
        <h2 style="margin-top:8px;">摩托骑行</h2>
      </div>
      <?php $moto_cat = get_category_by_slug( 'moto' ); if ( $moto_cat ) : ?>
      <a href="<?php echo esc_url( get_category_link( $moto_cat ) ); ?>" class="section__more">更多骑行记录 &rarr;</a>
      <?php endif; ?>
    </div>
    <div class="featured reveal reveal-delay-1">
      <div class="featured__cover">
        <?php if ( has_post_thumbnail() ) : ?>
          <?php the_post_thumbnail( 'article-cover' ); ?>
        <?php endif; ?>
      </div>
      <div>
        <div class="featured__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
        <h2><?php the_title(); ?></h2>
        <p class="featured__text"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
        <a href="<?php the_permalink(); ?>" class="featured__link">阅读全文 <span>&rarr;</span></a>
      </div>
    </div>
  </div>
</section>
<?php
  wp_reset_postdata();
endif;
?>

<!-- Cat (latest post from cat category) -->
<?php
$cat_query = yigeren_category_query( 'cat', 1 );
if ( $cat_query->have_posts() ) :
  $cat_query->the_post();
?>
<section class="section" data-component="cat">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Cat</span>
        <h2 style="margin-top:8px;">猫咪日常</h2>
      </div>
      <?php $cat_cat = get_category_by_slug( 'cat' ); if ( $cat_cat ) : ?>
      <a href="<?php echo esc_url( get_category_link( $cat_cat ) ); ?>" class="section__more">更多猫咪故事 &rarr;</a>
      <?php endif; ?>
    </div>
    <div class="featured reveal reveal-delay-1" style="grid-template-columns: 1fr 1.1fr;">
      <div>
        <?php $mood = yigeren_post_mood(); if ( $mood ) : ?>
        <span class="diary__mood" style="margin-bottom:12px;"><?php echo esc_html( $mood ); ?></span>
        <?php endif; ?>
        <h2 style="margin-top:8px;"><?php the_title(); ?></h2>
        <p class="featured__text"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
        <a href="<?php the_permalink(); ?>" class="featured__link">阅读全文 <span>&rarr;</span></a>
      </div>
      <div class="featured__cover">
        <?php if ( has_post_thumbnail() ) : ?>
          <?php the_post_thumbnail( 'article-cover' ); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php
  wp_reset_postdata();
endif;
?>

<!-- Latest Articles -->
<section class="section" data-component="articles">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Latest</span>
        <h2 style="margin-top:8px;">最新文章</h2>
      </div>
      <?php $archive_page = get_page_by_path( 'archive' ); if ( $archive_page ) : ?>
      <a href="<?php echo esc_url( get_permalink( $archive_page ) ); ?>" class="section__more">全部归档 &rarr;</a>
      <?php endif; ?>
    </div>
    <ul class="article-list reveal reveal-delay-1">
      <?php
      $latest = new WP_Query( array( 'posts_per_page' => 8, 'post_status' => 'publish' ) );
      if ( $latest->have_posts() ) :
        while ( $latest->have_posts() ) : $latest->the_post();
          $cats = get_the_category();
          $cat_label = ! empty( $cats ) ? yigeren_category_label( $cats[0]->slug ) : '';
      ?>
      <li class="article-item">
        <span class="article-item__date"><?php echo esc_html( yigeren_short_date() ); ?></span>
        <div class="article-item__body">
          <?php if ( $cat_label ) : ?>
          <span class="article-item__cat"><?php echo esc_html( $cat_label ); ?></span>
          <?php endif; ?>
          <h3 class="article-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="article-item__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
        </div>
      </li>
      <?php
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </ul>
  </div>
</section>

<!-- 孩子回家 Project Section -->
<section class="section" id="project" data-component="project">
  <div class="section__kanji" aria-hidden="true">家</div>
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Project</span>
        <h2>孩子回家</h2>
      </div>
    </div>
    <div class="project-card reveal reveal-delay-1">
      <div class="project-card__illustration">
        <svg viewBox="0 0 480 360" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="孩子回家插画">
          <rect width="480" height="360" fill="var(--seed-surface)"/>
          <rect width="480" height="200" fill="var(--seed-bg)"/>
          <circle cx="380" cy="60" r="30" fill="var(--seed-fg)" opacity="0.08"/>
          <circle cx="380" cy="60" r="22" fill="var(--seed-fg)" opacity="0.06"/>
          <circle cx="388" cy="54" r="22" fill="var(--seed-bg)"/>
          <circle cx="60" cy="35" r="1.2" fill="var(--seed-fg)" opacity="0.25"/>
          <circle cx="120" cy="55" r="0.8" fill="var(--seed-fg)" opacity="0.2"/>
          <circle cx="200" cy="28" r="1" fill="var(--seed-fg)" opacity="0.3"/>
          <circle cx="280" cy="45" r="0.7" fill="var(--seed-fg)" opacity="0.15"/>
          <circle cx="320" cy="22" r="1.1" fill="var(--seed-fg)" opacity="0.2"/>
          <circle cx="440" cy="38" r="0.9" fill="var(--seed-fg)" opacity="0.18"/>
          <circle cx="160" cy="70" r="0.6" fill="var(--seed-fg)" opacity="0.15"/>
          <path d="M0 180 Q60 150 140 165 Q220 140 300 158 Q380 135 480 155 L480 200 L0 200Z" fill="var(--seed-border)" opacity="0.3"/>
          <rect y="195" width="480" height="165" fill="var(--seed-border)" opacity="0.15"/>
          <path d="M240 360 Q238 300 235 260 Q230 220 240 200" fill="none" stroke="var(--seed-muted)" stroke-width="20" opacity="0.08"/>
          <path d="M240 360 Q238 300 235 260 Q230 220 240 200" fill="none" stroke="var(--seed-muted)" stroke-width="1.5" opacity="0.15" stroke-dasharray="4 6"/>
          <rect x="210" y="175" width="60" height="35" fill="var(--seed-border)" opacity="0.5"/>
          <polygon points="205,175 240,148 275,175" fill="var(--seed-border)" opacity="0.6"/>
          <rect x="232" y="192" width="16" height="18" fill="var(--seed-accent)" opacity="0.2"/>
          <rect x="216" y="183" width="10" height="8" fill="var(--seed-accent)" opacity="0.25"/>
          <rect x="254" y="183" width="10" height="8" fill="var(--seed-accent)" opacity="0.25"/>
          <ellipse cx="240" cy="210" rx="25" ry="8" fill="var(--seed-accent)" opacity="0.06"/>
          <g transform="translate(240, 275)">
            <ellipse cx="0" cy="8" rx="6" ry="10" fill="var(--seed-fg)" opacity="0.25"/>
            <circle cx="0" cy="-6" r="5" fill="var(--seed-fg)" opacity="0.25"/>
            <rect x="3" y="-2" width="5" height="8" rx="1" fill="var(--seed-accent)" opacity="0.2"/>
            <ellipse cx="0" cy="20" rx="8" ry="2" fill="var(--seed-fg)" opacity="0.05"/>
          </g>
          <g transform="translate(240, 188)">
            <ellipse cx="0" cy="4" rx="4" ry="7" fill="var(--seed-accent)" opacity="0.3"/>
            <circle cx="0" cy="-5" r="3.5" fill="var(--seed-accent)" opacity="0.3"/>
            <path d="M-4 0 Q-10 2 -12 6" fill="none" stroke="var(--seed-accent)" stroke-width="1.5" opacity="0.25" stroke-linecap="round"/>
          </g>
          <g opacity="0.15">
            <rect x="80" y="170" width="4" height="30" fill="var(--seed-muted)"/>
            <ellipse cx="82" cy="165" rx="14" ry="20" fill="var(--seed-muted)" opacity="0.6"/>
          </g>
          <g opacity="0.12">
            <rect x="380" y="175" width="3" height="25" fill="var(--seed-muted)"/>
            <ellipse cx="381" cy="170" rx="12" ry="18" fill="var(--seed-muted)" opacity="0.6"/>
          </g>
          <circle cx="180" cy="230" r="1.5" fill="var(--seed-accent)" opacity="0.2"/>
          <circle cx="300" cy="245" r="1" fill="var(--seed-accent)" opacity="0.15"/>
          <circle cx="150" cy="255" r="1.2" fill="var(--seed-accent)" opacity="0.12"/>
          <circle cx="330" cy="220" r="0.8" fill="var(--seed-accent)" opacity="0.18"/>
          <text x="240" y="340" text-anchor="middle" font-family="Caveat, cursive" font-size="14" fill="var(--seed-muted)" opacity="0.35">every child deserves a way home</text>
        </svg>
      </div>
      <div class="project-card__body">
        <span class="project-card__tag">公益小程序</span>
        <div class="annotation annotation--right" style="margin-bottom: 4px;">every child deserves a way home</div>
        <h2>孩子回家</h2>
        <p>一个帮助走失儿童家庭团聚的公益小程序。通过发布和扩散寻亲信息，让更多人看到，让每一个走失的孩子都能找到回家的路。</p>
        <div class="project-card__stats">
          <div class="project-card__stat">
            <span class="project-card__stat-num">36</span>
            <span class="project-card__stat-label">条寻亲信息</span>
          </div>
          <div class="project-card__stat">
            <span class="project-card__stat-num">180+</span>
            <span class="project-card__stat-label">天</span>
          </div>
        </div>
        <button class="project-card__link" data-qr-modal-open type="button">扫码使用 <span>&rarr;</span></button>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
