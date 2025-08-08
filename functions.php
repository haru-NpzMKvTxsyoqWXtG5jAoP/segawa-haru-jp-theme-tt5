<?php

// ============================================== 
//  読み込みアセット（CSS・JS）
// ==============================================
function haru_enqueue_assets() {
    /* 子テーマ CSS（更新日時をバージョンにしてキャッシュバスティング）*/
    $css_path = get_stylesheet_directory() . '/style.css';
    $child_ver = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0.0';
    
    wp_enqueue_style(
        'haru-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'twentytwentyfive-style' ), // 親テーマの正式ハンドル名に依存
        $child_ver
    );

    /* フリップカード JS */
    $js_path = get_stylesheet_directory() . '/js/flip-card.js';
    $js_ver = file_exists( $js_path ) ? filemtime( $js_path ) : '1.0.0';
    
    wp_enqueue_script(
        'haru-flip-card',
        get_stylesheet_directory_uri() . '/js/flip-card.js',
        array(),
        $js_ver,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'haru_enqueue_assets', 20 );


// ============================================== 
//  ギャラリータグ一覧
// ==============================================
/**
 * ギャラリーカテゴリの記事で使われているタグ一覧を表示
 * ショートコード [haru_gallery_tags] で使用
 */
function haru_gallery_tags_render() {
    $post_ids = get_posts( array(
        'category_name'  => 'gallery',
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ) );
    
    if ( empty( $post_ids ) ) return '';
    
    $tags = get_terms( array(
        'taxonomy'   => 'post_tag',
        'hide_empty' => true,
        'object_ids' => $post_ids,
        'orderby'    => 'name',
    ) );
    
    if ( is_wp_error( $tags ) || empty( $tags ) ) return '';

    $out = '<ul class="haru-gallery-tag-list">';
    foreach ( $tags as $tag ) {
        // 内部用タグを除外
        if ( $tag->slug === 'haru-gallery-item-wide' ) {
            continue;
        }
        
        $out .= sprintf(
            '<li><a href="%s">%s</a></li>',
            esc_url( get_tag_link( $tag ) ),
            esc_html( $tag->name )
        );
    }
    $out .= '</ul>';
    return $out;
}

// ============================================== 
//  ショートコード登録
// ==============================================
add_shortcode( 'haru_gallery_tags', 'haru_gallery_tags_render' );


// ============================================== 
//  SEO機能
// ==============================================

// SEO設定（再定義ガード付き）
if ( ! defined( 'HARU_META_DESCRIPTION_LENGTH' ) ) {
    define( 'HARU_META_DESCRIPTION_LENGTH', 120 );
}
if ( ! defined( 'HARU_DEFAULT_OGP_IMAGE' ) ) {
    define( 'HARU_DEFAULT_OGP_IMAGE', 'images/harusegawa_ogp.png' );
}

// SEO機能の有効/無効を制御（将来プラグイン導入時に便利）
function haru_seo_enabled() {
    return apply_filters( 'haru_seo_enabled', true );
}

// 固定ページの抜粋機能を有効化
add_action( 'init', function() {
    add_post_type_support( 'page', 'excerpt' );
});

// 文字列を整形してmeta description用に調整
function haru_trim_for_description( $text, $length = HARU_META_DESCRIPTION_LENGTH ) {
    // ショートコード除去
    $text = strip_shortcodes( $text );
    
    // HTMLタグ除去（第2引数trueで改行も除去）
    $text = wp_strip_all_tags( $text, true );
    
    // 空白文字の正規化（マルチバイト対応）
    $text = preg_replace( '/\s+/u', ' ', trim( $text ) );
    
    // 文字数制限（超過時は末尾に…を追加）
    if ( mb_strlen( $text, 'UTF-8' ) > $length ) {
        $text = mb_substr( $text, 0, $length, 'UTF-8' ) . '…';
    }
    
    return $text;
}

// meta description を生成
function haru_get_meta_description() {
    // トップページ：サイトのキャッチフレーズ
    if ( is_front_page() ) {
        $description = get_bloginfo( 'description', 'display' );
        return $description ? haru_trim_for_description( $description ) : '';
    }
    
    // 投稿・固定ページ
    if ( is_singular() ) {
        $post = get_queried_object();
        
        // 手動抜粋がある場合は優先
        if ( $post && has_excerpt( $post ) ) {
            return haru_trim_for_description( get_the_excerpt( $post ) );
        }
        
        // 本文から生成（生データを取得）
        $content = $post ? get_post_field( 'post_content', $post ) : '';
        return $content ? haru_trim_for_description( $content ) : '';
    }
    
    // アーカイブページ：出力しない
    return '';
}

// OGP画像を取得（優先順位：アイキャッチ画像→サイトアイコン→デフォルト画像）
function haru_get_ogp_image() {
    // 投稿・固定ページのアイキャッチ画像
    if ( is_singular() ) {
        $post_id = get_queried_object_id();
        $thumbnail_url = $post_id ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';
        if ( $thumbnail_url ) {
            return $thumbnail_url;
        }
    }
    
    // サイトアイコン（1200px以上を要求）
    $site_icon_url = get_site_icon_url( 1200 );
    if ( $site_icon_url ) {
        return $site_icon_url;
    }
    
    // デフォルト画像
    return get_theme_file_uri( HARU_DEFAULT_OGP_IMAGE );
}

// SEOタグを出力
function haru_output_seo_tags() {
    // SEO機能が無効の場合は何もしない
    if ( ! haru_seo_enabled() ) {
        return;
    }
    
    // 不要なコンテキストでは出力しない
    if ( is_admin() || is_feed() || is_embed() || 
         ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) ) {
        return;
    }
    
    // デバッグ用コメント
    echo "<!-- Haru SEO v1 -->\n";
    
    // meta description
    $description = haru_get_meta_description();
    if ( $description !== '' ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    
    // OGPタグの準備
    $og_title = wp_get_document_title();
    $og_description = haru_get_meta_description();
    $og_image = haru_get_ogp_image();
    
    // ページネーション対応を含むURL取得
    if ( is_paged() ) {
        $og_url = get_pagenum_link( get_query_var( 'paged' ) );
    } else {
        $og_url = is_front_page() ? home_url( '/' ) : get_permalink();
    }
    $og_url = set_url_scheme( $og_url, 'https' );
    
    // ページタイプの判定
    $og_type = ( is_front_page() || is_home() || is_archive() ) ? 'website' : 'article';
    
    // サイト名
    $og_site_name = get_bloginfo( 'name' );
    
    // OGPタグ出力
    echo "\n<!-- Open Graph -->\n";
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    if ( $og_description !== '' ) {
        echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
    }
    echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $og_site_name ) . '">' . "\n";
}
add_action( 'wp_head', 'haru_output_seo_tags', 5 );

