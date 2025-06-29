document.addEventListener('DOMContentLoaded', function() {
  console.log('Profile card script loaded');
  
  // プロフィールカード対応（haru-プレフィックス対応）
  const profileCardContainers = document.querySelectorAll('.haru-profile-card-container');
  console.log('Found containers:', profileCardContainers.length);
  
  profileCardContainers.forEach(container => {
    container.addEventListener('click', (e) => {
      console.log('Container clicked');
      const card = container.querySelector('.haru-profile-card');
      if (card) {
        console.log('Toggling flip on card');
        card.classList.toggle('is-flipped');
      }
    });
  });

  // 直接プロフィールカードをクリック対応
  const profileCards = document.querySelectorAll('.haru-profile-card');
  console.log('Found cards:', profileCards.length);
  
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