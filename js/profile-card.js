document.addEventListener('DOMContentLoaded', function() {
  // 既存のハルカード (haru-card) 対応
  const haruCardContainers = document.querySelectorAll('.haru-card-container');
  
  haruCardContainers.forEach(container => {
    container.addEventListener('click', () => {
      const card = container.querySelector('.haru-card');
      if (card) {
        card.classList.toggle('is-flipped');
      }
    });
  });

  // 新しいプロフィールカード (profile-card) 対応
  const profileCards = document.querySelectorAll('.profile-card');
  
  profileCards.forEach(card => {
    // クリックイベント
    card.addEventListener('click', function() {
      this.classList.toggle('is-flipped');
    });
    
    // キーボードアクセシビリティ対応
    card.addEventListener('keydown', function(event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        this.classList.toggle('is-flipped');
      }
    });
    
    // アクセシビリティ属性を設定
    card.setAttribute('tabindex', '0');
    card.setAttribute('role', 'button');
    card.setAttribute('aria-label', 'プロフィールカードをフリップ');
  });
}); 