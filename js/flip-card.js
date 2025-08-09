/**
 * フリップカード機能
 */
document.addEventListener('DOMContentLoaded', function() {
  const flipCard = document.querySelector('.haru-flip-card');
  
  if (flipCard) {
    // アクセシビリティ属性を設定
    flipCard.setAttribute('tabindex', '0');
    flipCard.setAttribute('role', 'button');
    flipCard.setAttribute('aria-label', '裏面を表示');
    flipCard.setAttribute('aria-pressed', 'false');
    
    flipCard.addEventListener('click', function(e) {
      // リンクまたはリンク内の要素をクリックした場合は何もしない
      if (e.target.tagName === 'A' || e.target.closest('a')) {
        return;
      }
      
      this.classList.toggle('flipped');
      
      // 状態更新（WCAG 4.1.2 準拠）
      const isFlipped = this.classList.contains('flipped');
      this.setAttribute('aria-pressed', isFlipped.toString());
      
      // aria-label も状態に応じて変更
      if (isFlipped) {
        this.setAttribute('aria-label', '表面を表示');  // 裏面表示中→次は表面
      } else {
        this.setAttribute('aria-label', '裏面を表示');  // 表面表示中→次は裏面
      }
    });
    
    flipCard.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();  // Space がスクロールしないように
        flipCard.click();    // 既存 click ハンドラを再利用
      }
    });
  }
});