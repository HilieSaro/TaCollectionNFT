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
      background: black;
      cursor: zoom-in;
    }
    #bg-slideshow img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      opacity: 0;
      transition: opacity 1.8s ease, transform 12s ease;
      transform: scale(1.05);
    }
    #bg-slideshow img.active {
      opacity: 1;
      transform: scale(1);
    }

    /* Voile sombre – ne bloque plus les clics */
    .dark-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.45);
      z-index: -2;
      pointer-events: none;
    }

    /* Fond animé subtil – déjà non bloquant */
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

    /* 🖼️ Galerie plein écran */
    #fullscreen-viewer {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.95);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    #fullscreen-viewer img {
      max-width: 90%;
      max-height: 90%;
      object-fit: contain;
      transition: transform .3s ease;
    }
    #close-viewer {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 2rem;
      cursor: pointer;
      color: white;
    }
    #nav-left, #nav-right {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 3rem;
      cursor: pointer;
      color: white;
      padding: 10px;
      user-select: none;
    }
    #nav-left { left: 20px; }
    #nav-right { right: 20px; }
  </style>
</head>

<body>

<!-- Fond dynamique -->
<div id="bg-slideshow"></div>
<div class="dark-overlay"></div>
<div class="bg-particles"></div>

<!-- Galerie plein écran -->
<div id="fullscreen-viewer">
  <div id="close-viewer">✖</div>
  <div id="nav-left">❮</div>
  <img id="viewer-img" src="">
  <div id="nav-right">❯</div>
</div>

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

    <!-- 🟦 Message personnalisé selon le wallet -->
    <div class="event-box">
      <?php
        $vip = [
          '0xb410825ef18466a173d55f28d7d18ade639e1925',
          '0x6f3e67e8baab2ea8451094198b25e9a6a7342574',
          '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80'
        ];
        if (in_array(strtolower($wallet), $vip)) {
            echo "👑 <strong>Bienvenue membre VIP :</strong> Tu fais partie du cercle restreint. Des surprises arrivent bientôt… 🚀✨";
        } else {
            echo "🎉 <strong>Bienvenue dans ton espace membre :</strong> Reste connecté pour les prochains événements NFT ! 🌟";
        }
      ?>
    </div>

  </section>

</main>

<script src="jquery-4.0.0.min.js"></script>
<script>
let bgImages = [];
let currentIndex = 0;

/* ---------------------------------------------------
   🌌 Fond dynamique fullscreen
--------------------------------------------------- */
function loadBackgroundImages() {
  $.get('images.php', function(images) {
    if (!images || !images.length) return;

    bgImages = images;

    const $bg = $('#bg-slideshow');

    images.forEach((img, i) => {
      const safe = encodeURIComponent(img);
      $bg.append(`<img src="images/${safe}" class="${i === 0 ? 'active' : ''}">`);
    });

    setInterval(() => {
      const imgs = $('#bg-slideshow img');
      imgs.removeClass('active');
      currentIndex = (currentIndex + 1) % imgs.length;
      imgs.eq(currentIndex).addClass('active');
    }, 8000);
  }, 'json');
}

/* ---------------------------------------------------
   🖼️ Galerie plein écran
--------------------------------------------------- */
function openFullscreen(index) {
  if (!bgImages.length) return;
  const safe = encodeURIComponent(bgImages[index]);
  $('#viewer-img').attr('src', 'images/' + safe);
  $('#viewer-img').css('transform', 'scale(1)').data('scale', 1);
  $('#fullscreen-viewer').fadeIn(200);
  currentIndex = index;
}

$('#bg-slideshow').on('click', function () {
  openFullscreen(currentIndex);
});

$('#close-viewer').on('click', function () {
  $('#fullscreen-viewer').fadeOut(200);
});

$('#nav-left').on('click', function () {
  if (!bgImages.length) return;
  currentIndex = (currentIndex - 1 + bgImages.length) % bgImages.length;
  openFullscreen(currentIndex);
});

$('#nav-right').on('click', function () {
  if (!bgImages.length) return;
  currentIndex = (currentIndex + 1) % bgImages.length;
  openFullscreen(currentIndex);
});

/* Zoom à la molette dans la galerie */
$('#fullscreen-viewer').on('wheel', function(e) {
  e.preventDefault();
  let img = $('#viewer-img');
  let scale = img.data('scale') || 1;

  scale += e.originalEvent.deltaY * -0.001;
  scale = Math.min(Math.max(1, scale), 3);

  img.css('transform', 'scale(' + scale + ')');
  img.data('scale', scale);
});

/* Fermeture par clic sur fond noir */
$('#fullscreen-viewer').on('click', function(e) {
  if (e.target.id === 'fullscreen-viewer') {
    $('#fullscreen-viewer').fadeOut(200);
  }
});

/* ---------------------------------------------------
   🎈 Emojis flottants
--------------------------------------------------- */
function spawnEmoji() {
  const emojis = ["🚀","💎","🎨","🌟","🔥","✨","🧪","💯","😈","🎶","🦇","🧚‍♀️"];
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
