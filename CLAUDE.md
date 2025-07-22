# segawa-haru.jp WordPress テーマ

## 基本情報
- **サイト**: https://segawa-haru.jp/
- **テーマ**: Twenty Twenty-Five 子テーマ
- **デプロイ**: WP pusher プラグインで GitHub から自動反映

## 開発方針
- **保守性重視**: シンプルで理解しやすいコード
- **モバイルファースト**: レスポンシブ設計
- **WordPress ネイティブ**: ブロックエディタ優先
- **パフォーマンス**: 軽量で高速な動作
- **アクセシビリティ**: WCAG 準拠

## 技術スタック

### フォント
- **Noto Sans JP**: セルフホスト配信（300, 400, 500, 700）
- **GDPR 対応**: 外部 CDN 不使用、完全プライバシー保護
- **フォントサイズ**: theme.json で clamp() による完全レスポンシブ
- **パフォーマンス**: font-display: swap + 自動preload対応

### CSS 設計
- **CSS 変数**: 一貫性のあるデザインシステム
- **clamp()**: レスポンシブな値設定
- **モダン CSS**: Grid、Flexbox、CSS Variables

### JavaScript
- **最小限**: フリップカード機能のみ
- **アクセシビリティ**: キーボードナビゲーション対応

## 主要コンポーネント

### カードシステム
- **サービスカード**: 最大5列、ホバーアニメーション
- **スクラップカード**: 最大6列、2行省略テキスト
- **ギャラリーカード**: 最大7列、wide タグで2列分表示

### レスポンシブスペーサー
- **3サイズ**: large、medium、small
- **用途**: WordPress スペーサーブロックの拡張

### 縦型ヘッダー
- **縦書きナビゲーション**: writing-mode 使用
- **sticky 配置**: スクロール追従

### フリップカード
- **3D 回転**: CSS transforms
- **WCAG 4.1.2 準拠**: role="button", aria-pressed, 動的 aria-label
- **キーボード操作**: Enter/Space キー対応

## 最適化実装

### パフォーマンス
- **軽量クエリ**: fields=ids + no_found_rows で最適化
- **WebP 画像配信**: EWWW IO連携で30-50%軽量化
- **フォントキャッシュ**: .htaccess で1年間長期キャッシュ
- **エラーハンドリング**: ファイル存在チェック

### セキュリティ
- **エスケープ処理**: esc_html、esc_url 使用
- **外部依存最小化**: セルフホスト方針

### アクセシビリティ
- **WCAG 4.1.2**: フリップカードで Name, Role, Value 実装
- **動的 aria-label**: 状態に応じて「裏面を表示」「表面を表示」
- **フォーカス管理**: focus-visible 対応
- **キーボード**: 全機能操作可能

## ファイル構成
```
theme-root/
├── functions.php      # 機能拡張
├── style.css         # メインスタイル
├── theme.json        # テーマ設定（フォント定義含む）
├── .htaccess         # WebP配信・フォントキャッシュ設定
├── .gitattributes    # フォントファイルのバイナリ指定
├── fonts/            # Noto Sans JP セルフホスト（3.8MB）
│   ├── noto-sans-jp-300.woff2
│   ├── noto-sans-jp-400.woff2
│   ├── noto-sans-jp-500.woff2
│   └── noto-sans-jp-700.woff2
├── js/
│   └── flip-card.js  # フリップカード機能
└── custom-html/      # カスタムHTML用SVGファイル
    ├── harusegawa-title-logo-svg_customHTML.txt
    └── *.svg.html
```

## 重要な実装詳細

### 内部用タグシステム
- **haru-gallery-item-wide**: ギャラリーで2列分表示
- **CSS で非表示**: 一般ユーザーには見えない
- **PHP で除外**: タグ一覧からも除外

### GDPR 対応
- **Google Fonts**: システムフォント方式
- **外部リクエスト**: 完全に除去
- **プライバシー**: ユーザー IP 送信なし