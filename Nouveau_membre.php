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
      position: relative;
    }

    /* 🌌 Fond dynamique fullscreen */
    #bg-slideshow {
      position: fixed;
      inset: 0;
      z-index: -3;
      overflow: hidden;
    }
    #bg-slideshow img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1.8s ease;
    }
    #bg-slideshow img.active {
      opacity: 1;
    }

    /* Voile sombre pour lisibilité */
    .dark-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      z-index: -2;
    }

    /* Fond animé léger */
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
      backdrop-filter: blur(6px);
    }

    header a { color: #66d9ff; text-decoration: none; margin-left: 1rem; }
    header a:hover { text-decoration: underline; }

    main { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }

    .card {
      background: rgba(0,0,0,.45);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      backdrop-filter: blur(6px);
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

    /* 🎈 Emojis flottants */
    .float-emoji {
      position: fixed;
      font-size: 2rem;
      animation: floatUp 3s linear forwards;
      pointer-events: none;
      z-index: 10;
    }
    @keyframes floatUp {
      from { transform: translateY(0) scale(1); opacity: 1; }
      to   { transform: translateY(-120px) scale(1.4); opacity: 0; }
    }
  </style>
</head>

<body>

<!-- Fond dynamique -->
<div id="bg-slideshow"></div>
<div class="dark-overlay"></div>
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

  <section class="card">
    <h1>Annonce du moment 📢</h1>

    <!-- 🟦 Zone modifiable -->
    <div class="event-box">
      🎉 <strong>Événement spécial :</strong>  
      Une nouvelle vague NFT arrive bientôt…  
      Prépare ton wallet ! 🚀✨
    </div>
    <!-- 🟦 Fin zone modifiable -->

  </section>

</main>

<script src="jquery-4.0.0.min.js"></script>
<script>
/* ---------------------------------------------------
   🌌 Fond dynamique fullscreen
--------------------------------------------------- */
function loadBackgroundImages() {
  $.get('images.php', function(images) {
    if (!images || !images.length) return;

    const $bg = $('#bg-slideshow');

    images.forEach((img, i) => {
      const safe = encodeURIComponent(img);
      $bg.append(`<img src="images/${safe}" class="${i === 0 ? 'active' : ''}">`);
    });

    let index = 0;
    setInterval(() => {
      const imgs = $('#bg-slideshow img');
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
  loadBackgroundImages();
});
</script>

</body>
</html>
