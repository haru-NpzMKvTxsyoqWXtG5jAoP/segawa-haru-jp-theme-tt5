<?php

// ============================================== 
//  読み込みアセット（CSS・Adobe Fonts）
// ==============================================
function haru_enqueue_assets() {
    /* 親テーマ CSS */
    wp_enqueue_style(
        'tt5-parent',
        get_template_directory_uri() . '/style.css'
    );

    /* 子テーマ CSS（更新日時をバージョンにしてキャッシュバスティング）*/
    $child_ver = filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_style(
        'haru-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'tt5-parent' ),
        $child_ver
    );

    /* Adobe Fonts (Typekit) – 源ノ角ゴシック */
    wp_enqueue_script( 'haru-typekit', 'https://use.typekit.net/bew5wgt.js', array(), null, false );
    wp_add_inline_script( 'haru-typekit', 'try{Typekit.load({async:true});}catch(e){}' );

    /* Masonry関連JavaScript */
    wp_enqueue_script(
        'haru-masonry',
        get_stylesheet_directory_uri() . '/js/masonry.pkgd.min.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/masonry.pkgd.min.js' ),
        true
    );

    wp_enqueue_script(
        'haru-imagesloaded',
        get_stylesheet_directory_uri() . '/js/imagesloaded.pkgd.min.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/imagesloaded.pkgd.min.js' ),
        true
    );

    wp_enqueue_script(
        'haru-custom-masonry',
        get_stylesheet_directory_uri() . '/js/custom-masonry.js',
        array( 'haru-masonry', 'haru-imagesloaded' ),
        filemtime( get_stylesheet_directory() . '/js/custom-masonry.js' ),
        true
    );

    /* プロフィールカードJavaScript */
    wp_enqueue_script(
        'haru-profile-card',
        get_stylesheet_directory_uri() . '/js/profile-card.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/profile-card.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'haru_enqueue_assets' );