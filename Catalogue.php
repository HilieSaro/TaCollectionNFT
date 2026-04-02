<?php
session_start();
$wallet = $_SESSION['wallet_address'] ?? '';
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
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;">
  <title>Catalogue des Profils NFT</title>
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
      justify-content: space-between;
      background: rgba(0, 0, 0, .55);
      border-bottom: 1px solid rgba(255, 255, 255, .1);
    }

    header a {
      color: #66d9ff;
      text-decoration: none;
      margin-left: 1rem;
    }

    header a:hover {
      text-decoration: underline;
    }

    main {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
    }

    .card {
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(255, 255, 255, .12);
      border-radius: 14px;
      overflow: hidden;
    }

    .card img {
      width: 100%;
      aspect-ratio: 1/1;
      object-fit: cover;
      border-bottom: 1px solid rgba(255, 255, 255, .12);
    }

    .meta {
      padding: 1rem;
    }

    .meta h3 {
      margin: 0 0 .5rem;
      font-size: 1rem;
    }

    .meta a {
      color: #a2d5ff;
      text-decoration: none;
      font-weight: 600;
    }

    .meta a:hover {
      text-decoration: underline;
    }

    .loader {
      text-align: center;
      margin-top: 2rem;
      color: rgba(255, 255, 255, .7);
    }
  </style>
</head>

<body>

  <header>
    <div><strong>Catalogue des Profils NFT</strong></div>
    <div>
      <a href="TaCollectionNFT.php">Accueil 🏠</a>
      <a href="Nouveau_membre.php">Espace membre 👥</a>
      <a href="logout.php">Déconnexion 🚪</a>
    </div>
  </header>

  <main>
    <div id="catalogue" class="grid"></div>
    <div id="loading" class="loader">Chargement du catalogue…</div>
  </main>

  <script src="jquery-4.0.0.min.js"></script>
  <script>
    function loadOwners() {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'profiles.php', true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState !== 4) return;
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              resolve(data.addresses || []);
            } catch {
              reject("Erreur JSON");
            }
          } else {
            reject("Erreur réseau");
          }
        };
        xhr.send();
      });
    }

    function loadImages() {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'images.php', true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState !== 4) return;
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              resolve(data);
            } catch {
              reject("Erreur JSON images");
            }
          } else {
            reject("Erreur réseau images");
          }
        };
        xhr.send();
      });
    }

    // VERSION CORRIGÉE : encodeURIComponent(image)
    function buildCard(owner, image) {
      const link = `https://opensea.io/${owner}`;
      const safeImage = encodeURIComponent(image);

      return `
      <article class="card">
        <a href="${link}" target="_blank">
          <img src="images/${safeImage}" alt="NFT Profil">
        </a>
        <div class="meta">
          <h3>${image}</h3>
          <a href="${link}" target="_blank">Voir sur OpenSea 📖</a>
        </div>
      </article>
    `;
    }

    async function loadCatalogue() {
      const $loader = $('#loading');
      const $catalogue = $('#catalogue');

      try {
        const owners = await loadOwners();
        const images = await loadImages();

        if (!owners.length || !images.length) {
          $catalogue.html("<p style='opacity:.8;'>Aucune donnée disponible.</p>");
          return;
        }

        const cards = images.map((img, i) => {
          const owner = owners[i % owners.length];
          return buildCard(owner, img);
        }).join('');

        $catalogue.html(cards);

      } catch (e) {
        $catalogue.html("<p style='color:#ff8080;'>Impossible de charger le catalogue.</p>");
        console.error(e);
      } finally {
        $loader.hide();
      }
    }

    $(loadCatalogue);
  </script>

</body>

</html>