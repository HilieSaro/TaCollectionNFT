<?php
$wallet = $_COOKIE['wallet_address'] ?? '';
if (!$wallet) {
    header('Location: Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Espace membre - Annonce du moment</title>

  <style>
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #0b0c10;
      color: #e6e6e6;
      overflow-x: hidden;
    }

    /* 🌌 Animation de fond */
    .bg-particles {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: -1;
      background: radial-gradient(circle at 20% 20%, rgba(255,255,255,.06), transparent 60%),
                  radial-gradient(circle at 80% 80%, rgba(255,255,255,.04), transparent 60%);
      animation: bgMove 18s infinite alternate ease-in-out;
    }
    @keyframes bgMove {
      from { transform: translateY(0px); }
      to   { transform: translateY(-25px); }
    }

    header {
      padding: 1rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      background: rgba(0,0,0,.55);
      border-bottom: 1px solid rgba(255,255,255,.1);
    }

    header a { color: #66d9ff; text-decoration: none; margin-left: 1rem; }
    header a:hover { text-decoration: underline; }

    main { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }

    .card {
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      backdrop-filter: blur(4px);
    }

    /* 🎨 Carrousel */
    #carousel {
      width: 100%;
      height: 200px;
      border-radius: 14px;
      overflow: hidden;
      margin-bottom: 1.5rem;
      position: relative;
    }
    #carousel img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 1.2s ease;
    }
    #carousel img.active {
      opacity: 1;
    }

    /* 🎈 Emojis flottants */
    .float-emoji {
      position: fixed;
      font-size: 2rem;
      animation: floatUp 3s linear forwards;
      pointer-events: none;
    }
    @keyframes floatUp {
      from { transform: translateY(0) scale(1); opacity: 1; }
      to   { transform: translateY(-120px) scale(1.4); opacity: 0; }
    }

    /* 🟦 Zone d’annonce du moment */
    .event-box {
      background: rgba(0,0,0,.35);
      padding: 1.2rem;
      border-radius: 14px;
      font-size: 1.1rem;
      line-height: 1.5;
      border-left: 4px solid #66d9ff;
      animation: fadeIn 1s ease forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to { opacity: 1; }
    }
  </style>
</head>

<body>

<div class="bg-particles"></div>

<header>
  <div>
    <strong>Connecté :</strong> <?= htmlspecialchars($wallet); ?>
  </div>
  <div>
    <a href="TaCollectionNFT.php">Accueil 🏠</a>
    <a href="Catalogue.php">Catalogue 📚</a>
    <a href="logout.php">Déconnexion 🚪</a>
  </div>
</header>

<main>

  <!-- 🎨 Carrousel -->
  <div id="carousel"></div>

  <section class="card">
    <h1>Annonce du moment 📢</h1>

    <!-- 🟦 C’EST ICI QUE TU METS TON MESSAGE -->
    <div class="event-box">
      🎉 <strong>Événement spécial :</strong>  
      La prochaine mise à jour de la collection arrive bientôt…  
      Reste connecté pour découvrir les nouveautés ! 🚀✨
    </div>
    <!-- 🟦 FIN DE LA ZONE MODIFIABLE -->

  </section>

</main>

<script src="jquery-4.0.0.min.js"></script>
<script>
/* ---------------------------------------------------
   🎨 Carrousel d’images depuis /images
--------------------------------------------------- */
function loadCarouselImages() {
  $.get('images.php', function(images) {
    if (!images || !images.length) return;

    const $carousel = $('#carousel');
    images.forEach((img, i) => {
      const safe = encodeURIComponent(img);
      $carousel.append(`<img src="images/${safe}" class="${i === 0 ? 'active' : ''}">`);
    });

    let index = 0;
    setInterval(() => {
      const imgs = $('#carousel img');
      imgs.removeClass('active');
      index = (index + 1) % imgs.length;
      imgs.eq(index).addClass('active');
    }, 6000);
  });
}

/* ---------------------------------------------------
   🎈 Emojis flottants
--------------------------------------------------- */
function spawnEmoji() {
  const emojis = ["🚀","💎","🎨","🌟","🔥","✨","🧪"];
  const emoji = emojis[Math.floor(Math.random() * emojis.length)];

  const el = document.createElement("div");
  el.className = "float-emoji";
  el.textContent = emoji;
  el.style.left = Math.random() * window.innerWidth + "px";
  el.style.bottom = "0px";

  document.body.appendChild(el);
  setTimeout(() => el.remove(), 3000);
}
setInterval(spawnEmoji, 5000);

/* ---------------------------------------------------
   Initialisation
--------------------------------------------------- */
$(function () {
  loadCarouselImages();
});
</script>

</body>
</html>
