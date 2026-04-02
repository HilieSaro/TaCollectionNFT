<?php
session_start();
$wallet = $_SESSION['wallet_address'] ?? '';

// Liste les images disponibles dans le dossier "images".
// Ajoute simplement des fichiers dans ce dossier pour qu'ils soient affichés dans la galerie.
$imageDir = __DIR__ . '/images';
$images = [];
if (is_dir($imageDir)) {
  $files = glob($imageDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
  sort($files, SORT_NATURAL | SORT_FLAG_CASE);
  foreach ($files as $file) {
    $images[] = [
      'name' => pathinfo($file, PATHINFO_FILENAME),
      'src' => 'images/' . rawurlencode(basename($file)),
    ];
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;">
  <title>Ta Collection NFT</title>
  <style>
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #0b0c10;
      color: #e6e6e6;
    }

    header {
      padding: 1rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      background: rgba(0, 0, 0, .55);
      border-bottom: 1px solid rgba(255, 255, 255, .1);
    }

    header h1 {
      margin: 0;
      font-size: 1.2rem;
      letter-spacing: .03em;
    }

    header nav a {
      color: #66d9ff;
      margin-left: 1rem;
      text-decoration: none;
    }

    header nav a:hover {
      text-decoration: underline;
    }

    main {
      padding: 1.5rem;
      max-width: 1100px;
      margin: 0 auto;
    }

    .hero {
      text-align: center;
      margin-bottom: 2rem;
    }

    .hero h2 {
      margin: .5rem 0;
      font-size: 2.2rem;
    }

    .hero p {
      max-width: 720px;
      margin: 0.5rem auto;
      line-height: 1.6;
    }

    #gallery {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
      margin-top: 2rem;
    }

    .card {
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(255, 255, 255, .12);
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .card img {
      width: 100%;
      aspect-ratio: 1/1;
      object-fit: cover;
    }

    .card .meta {
      padding: .75rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card .meta h3 {
      margin: 0 0 .5rem;
      font-size: 1rem;
      line-height: 1.2;
    }

    .card .meta a {
      color: #a2d5ff;
      text-decoration: none;
      font-weight: 600;
    }

    .card .meta a:hover {
      text-decoration: underline;
    }

    .notice {
      margin-top: 1rem;
      padding: 1rem;
      background: rgba(255, 255, 255, .06);
      border-left: 4px solid #66d9ff;
    }

    .loader {
      text-align: center;
      margin-top: 2rem;
      color: rgba(255, 255, 255, .7);
    }

    @media (max-width: 640px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>

<body>
  <header>
    <h1>Ta Collection NFT</h1>
    <nav>
      <a href="TaCollectionNFT.php">Accueil</a>
      <a href="Catalogue.php">Catalogue</a>
      <a href="Nouveau_membre.php">Espace membre</a>
      <span id="walletStatus"></span>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Bienvenue dans la galerie</h2>
      <div class="notice">
        <strong>Astuce :</strong> clique sur une image pour l’ouvrir en grand.
      </div>
    </section>

    <section>
      <h2>Galerie locale</h2>
      <div id="gallery"></div>
      <div id="loading" class="loader">Chargement de la galerie…</div>
      <div id="error" class="loader" style="display:none; color:#ff9090;"></div>
    </section>
  </main>

  <script src="jquery-4.0.0.min.js"></script>
  <script>
    const localImages = <?php echo json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const walletAddress = <?php echo json_encode($wallet); ?>;

    function setWalletStatus() {
      const statusEl = document.getElementById('walletStatus');
      if (!statusEl) return;
      if (walletAddress) {
        statusEl.textContent = 'Connecté : ' + walletAddress.slice(0, 6) + '…' + walletAddress.slice(-4);
      } else {
        statusEl.innerHTML = '<a href="Login.php" style="color:#66d9ff;">Connecter mon wallet</a>';
      }
    }

    function renderCards(items) {
      const gallery = document.getElementById('gallery');
      if (!gallery) return;
      gallery.innerHTML = '';
      if (items.length === 0) {
        gallery.innerHTML = '<p style="opacity:.9">Aucune image trouvée. Ajoute des fichiers dans le répertoire <code>php/images/</code>.</p>';
        return;
      }
      items.forEach(item => {
        const card = document.createElement('article');
        card.className = 'card';
        card.innerHTML = `
          <a href="${item.src}" target="_blank" rel="noopener noreferrer">
            <img src="${item.src}" alt="${item.name}">
          </a>
          <div class="meta">
            <h3>${item.name || 'Image'}</h3>
            <a href="${item.src}" target="_blank" rel="noopener noreferrer">Ouvrir en grand</a>
          </div>
        `;
        gallery.appendChild(card);
      });
    }

    $(function() {
      setWalletStatus();
      $('#loading').hide();
      renderCards(localImages);
    });
  </script>
</body>

</html>