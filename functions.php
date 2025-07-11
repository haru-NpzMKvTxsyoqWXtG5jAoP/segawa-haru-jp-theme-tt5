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
}
add_action( 'wp_enqueue_scripts', 'haru_enqueue_assets' );

// ============================================== 
//  フロントページのギャラリー表示件数制限
// ==============================================
/**
 * フロントページの haru-gallery-grid だけ 15 件表示に制限
 */
add_filter(
    'query_loop_block_query_vars',
    function ( $query_vars, $block ) {

        // ① 表示中のページがフロントでなければ何もしない
        if ( ! is_front_page() ) {
            return $query_vars;
        }

        // ② ブロックの追加クラス名を取得（無い場合は空文字）
        $class = $block->attributes['className'] ?? '';

        // ③ haru-gallery-grid というクラスを持っているか確認
        if ( str_contains( $class, 'haru-gallery-grid' ) ) {
            $query_vars['posts_per_page'] = 15; // ← ここ変えれば上限も変わる
        }

        return $query_vars;
    },
    10,
    2
);