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
    // 投稿・固定ページ（HOMEページの固定ページ含む）
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
    
    // トップページ（固定ページ以外）：サイトのキャッチフレーズ
    if ( is_front_page() ) {
        $description = get_bloginfo( 'description', 'display' );
        return $description ? haru_trim_for_description( $description ) : '';
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
    $og_description = $description; // 重複計算を避ける
    $og_image = haru_get_ogp_image();
    
    // 正規URLを取得（カテゴリ、タグ、検索、ページ送りなどにも対応）
    $og_url = wp_get_canonical_url();
    if ( ! $og_url ) {
        // フォールバック（404ページなど）
        $og_url = home_url( add_query_arg( null, null ) );
    }
    $og_url = set_url_scheme( $og_url, 'https' );
    
    // ページタイプの判定
    $og_type = ( is_front_page() || is_home() || is_archive() ) ? 'website' : 'article';
    
    // サイト名
    $og_site_name = get_bloginfo( 'name' );
    
    // OGPタグ出力
    echo "\n<!-- Open Graph -->\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    if ( $og_description !== '' ) {
        echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
    }
    echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $og_site_name ) . '">' . "\n";
    
    // Twitterカード
    echo "\n<!-- Twitter Card -->\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    if ( $og_description !== '' ) {
        echo '<meta name="twitter:description" content="' . esc_attr( $og_description ) . '">' . "\n";
    }
    echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
}
add_action( 'wp_head', 'haru_output_seo_tags', 5 );

// タイトルの区切り文字を「|」に変更
add_filter( 'document_title_separator', function() {
    return '|';
});

// タグページをnoindexに設定（WordPress 5.7以降の推奨方法）
add_filter( 'wp_robots', function( $robots ) {
    if ( is_tag() ) {
        $robots['noindex'] = true;
        // nofollow を指定しない = follow のまま
    }
    return $robots;
});

// タグをXMLサイトマップから除外
add_filter( 'wp_sitemaps_taxonomies', function( $taxonomies ) {
    unset( $taxonomies['post_tag'] );
    return $taxonomies;
});

// ユーザー（著者）サイトマップを除外
add_filter( 'wp_sitemaps_add_provider', function( $provider, $name ) {
    if ( 'users' === $name ) {
        return false;
    }
    return $provider;
}, 10, 2 );


// ============================================== 
//  パンくずリスト
// ==============================================

// パンくず用: まず配列化
function haru_breadcrumb_items() {
    $items = [];
    $items[] = ['url' => home_url('/'), 'label' => 'Home'];

    if ( is_front_page() ) return $items;

    // ブログトップ（固定ページを投稿ページに設定している場合）
    if ( is_home() ) {
        $title = get_the_title( get_option('page_for_posts') );
        $items[] = ['url' => '', 'label' => $title ?: 'ブログ'];
        return $items;
    }

    if ( is_singular() ) {
        $post = get_queried_object();

        if ( $post->post_type === 'post' ) {
            // スクラップブック内の投稿の場合、固定ページを挿入
            // カテゴリーベースのチェックを一旦外して、投稿URLで判定
            $permalink = get_permalink($post);
            if ( strpos($permalink, '/scrapbook/') !== false ) {
                // 固定ページを複数の方法で探す
                $scrapbook_page = get_page_by_path('scrapbook');
                if ( ! $scrapbook_page ) {
                    // スラッグで見つからない場合、タイトルで探す
                    $scrapbook_page = get_page_by_title('SCRAP BOOK');
                }
                
                if ( $scrapbook_page ) {
                    $items[] = [
                        'url' => get_permalink($scrapbook_page),
                        'label' => get_the_title($scrapbook_page)
                    ];
                }
            }
            
            // カテゴリー階層
            $cats = get_the_category( $post->ID );
            if ( $cats ) {
                // 階層が深いカテゴリを優先（より具体的なカテゴリを選択）
                usort($cats, function($a, $b) {
                    return count(get_ancestors($b->term_id, 'category')) - count(get_ancestors($a->term_id, 'category'));
                });
                $primary = $cats[0]; // 最も階層が深いカテゴリ
                
                $parents = array_reverse( get_ancestors($primary->term_id, 'category') );
                foreach ( $parents as $tid ) {
                    $t = get_term($tid, 'category');
                    if ( ! is_wp_error($t) ) {
                        $items[] = ['url' => get_term_link($t), 'label' => $t->name];
                    }
                }
                $items[] = ['url' => get_term_link($primary), 'label' => $primary->name];
            }
        } elseif ( is_post_type_hierarchical($post->post_type) && $post->post_parent ) {
            // 階層型の固定ページ
            $anc = array_reverse( get_post_ancestors($post) );
            foreach ( $anc as $pid ) {
                $items[] = ['url' => get_permalink($pid), 'label' => get_the_title($pid)];
            }
        } elseif ( $post->post_type !== 'post' && $post->post_type !== 'page' ) {
            // カスタム投稿タイプ
            $obj = get_post_type_object($post->post_type);
            if ( $obj && ! empty($obj->has_archive) ) {
                $items[] = ['url' => get_post_type_archive_link($post->post_type), 'label' => $obj->labels->name];
            }
        }

        $items[] = ['url' => '', 'label' => get_the_title($post)];
    }
    elseif ( is_category() || is_tax() ) {
        $term = get_queried_object();
        $parents = array_reverse( get_ancestors($term->term_id, $term->taxonomy) );
        foreach ( $parents as $tid ) {
            $t = get_term($tid, $term->taxonomy);
            if ( ! is_wp_error($t) ) {
                $items[] = ['url' => get_term_link($t), 'label' => $t->name];
            }
        }
        $items[] = ['url' => '', 'label' => $term->name];
    }
    elseif ( is_post_type_archive() ) {
        $obj = get_queried_object();
        $items[] = ['url' => '', 'label' => $obj->labels->name];
    }
    elseif ( is_search() ) {
        $items[] = ['url' => '', 'label' => '検索結果'];
    }
    elseif ( is_author() ) {
        $a = get_queried_object();
        $items[] = ['url' => '', 'label' => '著者: ' . $a->display_name];
    }
    elseif ( is_date() ) {
        $items[] = ['url' => '', 'label' => 'アーカイブ'];
    }
    elseif ( is_404() ) {
        $items[] = ['url' => '', 'label' => '404 Not Found'];
    }

    // ページ送り
    $paged = max( get_query_var('paged'), get_query_var('page') );
    if ( $paged && $paged > 1 ) {
        $items[] = ['url' => '', 'label' => 'ページ ' . intval($paged)];
    }

    return $items;
}

// 表示 + JSON-LD
function haru_render_breadcrumbs() {
    if ( is_front_page() ) return ''; // トップでは出さない
    $items = haru_breadcrumb_items();

    // 見えるパンくず（Microdataは最小限）
    $html  = '<nav class="haru-breadcrumbs" aria-label="パンくず">';
    $html .= '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    $pos = 1; $last = count($items);
    foreach ( $items as $i ) {
        $label = esc_html( wp_strip_all_tags( $i['label'] ) );
        $url   = $i['url'];
        $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"'
              .  ( $pos === $last ? ' aria-current="page"' : '' ) . '>';
        if ( $url && $pos !== $last ) {
            $html .= '<a itemprop="item" href="' . esc_url($url) . '"><span itemprop="name">' . $label . '</span></a>';
        } else {
            $html .= '<span itemprop="name">' . $label . '</span>';
        }
        $html .= '<meta itemprop="position" content="' . intval($pos) . '" /></li>';
        $pos++;
    }
    $html .= '</ol></nav>';

    // JSON-LD
    $pos = 1; $list = [];
    foreach ( $items as $i ) {
        $entry = ['@type' => 'ListItem', 'position' => $pos++, 'name' => wp_strip_all_tags($i['label'])];
        if ( ! empty($i['url']) ) $entry['item'] = $i['url'];
        $list[] = $entry;
    }
    $json = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => $list,
    ];
    $html .= '<script type="application/ld+json">' . wp_json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . '</script>';

    return $html;
}
add_shortcode('haru_breadcrumbs', 'haru_render_breadcrumbs');


// ============================================== 
//  リダイレクト設定
// ==============================================

// 添付ファイルページを親記事へリダイレクト
add_action('template_redirect', function(){
    if (is_attachment()) {
        $parent = get_post_field('post_parent', get_queried_object_id());
        wp_redirect($parent ? get_permalink($parent) : home_url('/'), 301);
        exit;
    }
});


// ============================================== 
//  構造化データ（Person）
// ==============================================

// Person構造化データ（瀬川ハル）
add_action('wp_head', function () {
    // トップページのみ
    if ( ! is_front_page() ) return;
    
    $img = get_theme_file_uri('images/harusegawa_icon_2.png');
    
    $data = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Person',
        '@id'           => home_url('/#person'),
        'name'          => '瀬川 晴',
        'alternateName' => array(
            '瀬川晴',
            'せがわはる',
            'セガワハル',
            'Haru Segawa',
            'segawa haru',
            'HARUsegawa'
        ),
        'url'           => home_url('/'),
        'image'         => array(
            '@type' => 'ImageObject',
            'url'   => $img,
        ),
        'jobTitle'      => 'クリエイター',
        'sameAs'        => array(
            'https://www.instagram.com/haru_segawa',
            'https://jp.pinterest.com/haru_segawa/',
            'https://www.behance.net/haru_segawa',
        ),
    );
    
    echo '<script type="application/ld+json">' .
         wp_json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) .
         '</script>' . "\n";
}, 6);


// ============================================== 
//  Contact Form 7 最適化
// ==============================================

// フロントページ以外でCF7を無効化
add_action( 'wp', function() {
    if ( ! is_front_page() ) {
        // フロントページ以外ではCF7の読み込みを無効化
        add_filter( 'wpcf7_load_js', '__return_false' );
        add_filter( 'wpcf7_load_css', '__return_false' );
    }
});

// reCAPTCHAもフロントページ以外では読み込まない
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_front_page() ) {
        // CF7が使うreCAPTCHA関連のスクリプトを除外
        wp_dequeue_script( 'google-recaptcha' );
        wp_dequeue_script( 'wpcf7-recaptcha' );
        wp_deregister_script( 'google-recaptcha' );
        wp_deregister_script( 'wpcf7-recaptcha' );
    }
}, 100 );


// ============================================== 
//  関連記事自動表示
// ==============================================

/**
 * 関連記事用のクエリループを自動的に調整（安全版）
 * Query Loop ブロックに CSS クラス「haru-related-posts」を付けると、
 * 現在の投稿のタグ → なければカテゴリで絞り込み、現在記事を除外する。
 *
 * ※ サイトエディター側で「クエリの継承（Inherit）」はオフにしておくこと。
 */
add_filter('query_loop_block_query_vars', function ($query, $block) {
    // 管理画面や投稿詳細以外では何もしない
    if (is_admin() || !is_singular('post')) {
        return $query;
    }

    // 目印クラスが付いた Query Loop のみ対象
    $attrs = $block->parsed_block['attrs'] ?? [];
    $class = $attrs['className'] ?? '';
    if (!$class || strpos($class, 'haru-related-posts') === false) {
        return $query;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) {
        return $query;
    }

    // 基本のクエリ調整
    $query['post_type'] = $query['post_type'] ?? 'post';
    $query['ignore_sticky_posts'] = true;
    $query['post__not_in'] = array_unique(array_merge($query['post__not_in'] ?? [], [$post_id]));
    $query['orderby'] = $query['orderby'] ?? 'date';
    $query['order']   = $query['order']   ?? 'DESC';

    // 優先度：タグ → カテゴリ → そのまま新着
    $tag_ids = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'ids']);
    if (!empty($tag_ids)) {
        $query['tag__in'] = $tag_ids;
        return $query;
    }

    $cat_ids = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);
    if (!empty($cat_ids)) {
        $query['category__in'] = $cat_ids;
        return $query;
    }

    // タグ・カテゴリが無い記事はフォールバック（新着）
    return $query;
}, 10, 2);

// ==============================================
//  関連記事デバッグ（暫定）
// ==============================================

// 1) haru-related-posts の Query Loop にデバッグ用マーカーを付与
add_filter('query_loop_block_query_vars', function ($query, $block) {
    if (is_admin() || !is_singular('post')) {
        return $query;
    }
    $attrs = $block->parsed_block['attrs'] ?? [];
    $class = $attrs['className'] ?? '';
    if (!$class || strpos($class, 'haru-related-posts') === false) {
        return $query;
    }
    // WP_Query に無視されるカスタムキーでマーカーを仕込む
    if (empty($query['haru_related_marker'])) {
        $query['haru_related_marker'] = uniqid('hrd_', true);
    }
    return $query;
}, 99, 2);

// 2) 実際に WP_Query が走る直前の最終引数を捕捉
add_action('pre_get_posts', function($q){
    if (is_admin()) return;
    // マーカーが付いた Query Loop だけ対象
    $marker = $q->get('haru_related_marker');
    if (!$marker) return;
    // 収集（安全のため必要なキーのみ）
    $vars = $q->query_vars;
    $capture = array(
        'marker'         => $marker,
        'post_type'      => $vars['post_type'] ?? null,
        'posts_per_page' => $vars['posts_per_page'] ?? null,
        'orderby'        => $vars['orderby'] ?? null,
        'order'          => $vars['order'] ?? null,
        'post__not_in'   => $vars['post__not_in'] ?? null,
        'post__in'       => $vars['post__in'] ?? null,
        'tag__in'        => $vars['tag__in'] ?? null,
        'category__in'   => $vars['category__in'] ?? null,
        'tax_query'      => $vars['tax_query'] ?? null,
    );
    if (!isset($GLOBALS['haru_related_debug_data'])) {
        $GLOBALS['haru_related_debug_data'] = array();
    }
    $GLOBALS['haru_related_debug_data'][] = $capture;
}, 1000);

// 3) フッターにHTMLコメントとして出力（フロントのみ）
add_action('wp_footer', function(){
    if (is_admin()) return;
    if (empty($GLOBALS['haru_related_debug_data'])) return;
    $data = $GLOBALS['haru_related_debug_data'];
    // JSONにしてコメントで出力（1行）
    echo "\n<!-- HaruRelatedDebug " . esc_html( wp_json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ) . " -->\n";
});


// ============================================== 
//  吹き出しシステム
// ==============================================

// キャラクター定義
function haru_bubble_get_characters() {
    return array(
        'user1' => array(
            'name' => '晴',
            'file' => 'user1.webp',
            'positions' => array('left'),
        ),
        'user2' => array(
            'name' => 'ミケネコ',
            'file' => 'user2.webp',
            'positions' => array('left', 'right'),
        ),
        'user3' => array(
            'name' => 'ハチワレ',
            'file' => 'user3.webp',
            'positions' => array('left', 'right'),
        ),
    );
}

// ショートコード実装
add_shortcode('bubble', 'haru_bubble_render');

function haru_bubble_render($atts, $content = null) {
    // パラメータ取得
    $atts = shortcode_atts(array(
        'name' => 'user1',
        'pos' => 'left',
    ), $atts, 'bubble');
    
    // キャラクター情報
    $characters = haru_bubble_get_characters();
    
    // 値の検証
    $name = sanitize_key($atts['name']);
    if (!isset($characters[$name])) {
        $name = 'user1';
    }
    
    $character = $characters[$name];
    $pos = in_array($atts['pos'], array('left', 'right')) ? $atts['pos'] : 'left';
    
    // 位置チェック
    if (!in_array($pos, $character['positions'])) {
        $pos = $character['positions'][0];
    }
    
    // コンテンツ処理
    if (empty($content)) {
        return '';
    }
    $content = do_shortcode($content);
    $content = wp_kses_post($content);
    
    // 画像パス
    $img_url = get_theme_file_uri('images/chat/' . $character['file']);
    
    // HTML出力（クラスのみ、スタイル属性なし）
    $html = sprintf(
        '<div class="haru-bubble haru-bubble--%s haru-bubble--%s" aria-label="%sの発言">
            <div class="haru-bubble__avatar">
                <img class="haru-bubble__icon" src="%s" alt="%s" width="80" height="80" loading="lazy" decoding="async">
                <div class="haru-bubble__name">%s</div>
            </div>
            <div class="haru-bubble__content">%s</div>
        </div>',
        esc_attr($pos),
        esc_attr($name),
        esc_attr($character['name']),
        esc_url($img_url),
        esc_attr($character['name']),
        esc_html($character['name']),
        $content
    );
    
    return $html;
}
