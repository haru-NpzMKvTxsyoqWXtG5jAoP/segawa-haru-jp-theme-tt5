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
- **高さ統一**: ブロックエディタの「寸法」→「最小の高さ」で500px設定（CSS不要）

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

### 料金表コンポーネント（.hr-table）
**技術実装の核心:**
- `border-collapse: separate` + `border-spacing: 0` でボーダー重なり問題を根本解決
- `grid-auto-rows: 1fr` で将来的な行数拡張に対応
- CSS変数 `--hr-table-border` でボーダー定義を一元管理
- colgroup による列幅制御とセル装飾の完全分離

**HTML構造設計:**
- セマンティックテーブル（table + caption + colgroup + tbody）
- `scope="row"` でアクセシビリティ配慮
- インラインstyle完全除去（CSS分離徹底）
- thead「項目/内容」は冗長と判断し採用せず

**均等分割システム:**
- `.hr-split`（Grid）で高さの均等分割
- `.hr-pane`（Flexbox）で縦中央配置と内容整理
- `border-top` による視覚的境界線（構造とデザインの役割分離）

**重要な設計判断:**
- ❌ border-collapse: collapse → 線が重なって太くなる
- ✅ border-collapse: separate → 各セル独立、隙間0で統一感
- ❌ rowspan使用 → 空行必要、構造複雑
- ✅ Grid内部分割 → シンプル、拡張容易

**絶対禁止事項（テーブル特有）:**
- 勝手なfont-size指定（large、small等）
- 勝手なfont-weight調整（500等）
- HTMLへのstyle属性混在
- 冗長なCSS記述（.hr-table p で包括可能なmargin指定等）

**拡張ガイドライン:**
- 行数増加：HTML の .hr-pane 追加のみ（CSS変更不要）
- ボーダー変更：--hr-table-border 変数のみ修正
- 列幅調整：colgroup の CSS のみ変更

**失敗から学んだ教訓:**
- thead視覚隠しCSS → 無駄な複雑化（theadそのものが不要だった）
- 横スクロール対策 → body min-width で既に対応済みだった
- 複数の冗長CSS → .hr-table p { margin: 0; } で包括対応可能

**技術的詳細メモ:**
- `overflow-wrap: break-word` で日本語長文の自然な改行
- `table-layout: fixed` でレンダリング最適化
- CSS変数は必ず hr- プレフィックス（--hr-table-border等）
- セル内Grid+Flexboxは異なる責務（高さ分割 vs 縦中央配置）

**アクセシビリティ配慮:**
- caption でテーブル目的明示
- scope="row" で行見出し識別
- thead は必要十分性で判断（自明な場合は省略可）

## 絶対禁止事項：その場しのぎ解決策

**問題解決の原則:**
- 問題の根本原因を特定してから解決策を考える
- 「とりあえず動く方法」は一切提案しない
- CSS問題をHTML style属性で解決するような設計破綻は厳禁
- 設計の一貫性を最優先し、途中で方針転換しない
- 推測で答えず、不明な場合は「調査が必要」と正直に伝える

**具体例:**
- ❌ CSSが効かない → HTMLのstyle属性で解決
- ✅ CSSが効かない → なぜ効かないのか根本原因を特定
- ❌ レイアウト崩れ → 別の方法で見た目だけ修正
- ✅ レイアウト崩れ → 構造的な問題を見直して根本解決