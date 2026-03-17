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
  <title>Catalogue des Profils NFT</title>
  <style>
    body { margin: 0; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0b0c10; color: #e6e6e6; }
    header { padding: 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; background: rgba(0,0,0,.55); border-bottom: 1px solid rgba(255,255,255,.1); }
    header a { color: #66d9ff; text-decoration: none; margin-left: 1rem; }
    header a:hover { text-decoration: underline; }
    main { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
    h1 { margin-top: 0; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap: 1rem; }
    .card { display: flex; flex-direction: column; border-radius: 14px; overflow: hidden; border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.06); }
    .card img { width: 100%; aspect-ratio: 1/1; object-fit: cover; }
    .meta { padding: 1rem; display: flex; flex-direction: column; justify-content: space-between; flex: 1; }
    .meta h3 { margin: 0 0 .5rem; font-size: 1rem; line-height: 1.2; }
    .meta a { color: #a2d5ff; text-decoration: none; font-weight: 600; }
    .meta a:hover { text-decoration: underline; }
    .loader { text-align:center; margin-top: 2rem; color: rgba(255,255,255,.7); }
    .notice { background: rgba(255,255,255,.06); padding: 1rem; border-left: 4px solid #66d9ff; border-radius: 10px; margin-bottom: 1.5rem; }
  </style>
</head>
<body>
  <header>
    <div>
      <strong>Catalogue des Profils NFT</strong>
    </div>
    <div>
      <a href="TaCollectionNFT.php">Accueil 🏠</a>
      <a href="Nouveau_membre.php">Espace membre 👥</a>
      <a href="logout.php">Déconnexion 🚪</a>
    </div>
  </header>

  <main>
    <section class="notice">
      <strong>Info :</strong> Cette page affiche les liens directs vers les deux profils OpenSea spécifiés. Clique sur une vignette pour ouvrir le profil complet. 🎨
    </section>

    <div id="catalogue" class="grid"></div>
    <div id="loading" class="loader">Chargement du catalogue…</div>
    <div id="error" style="color:#ff8080; margin-top:1rem; display:none;"></div>
  </main>

  <script src="jquery-4.0.0.min.js"></script>
  <script>
    function buildCard(owner) {
      const link = `https://opensea.io/${owner}`;
      return `
        <article class="card">
          <a href="${link}" target="_blank" rel="noopener noreferrer">
            <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='220'%20height='220'%3E%3Crect%20width='220'%20height='220'%20fill='%23111'/%3E%3Ctext%20x='50%25'%20y='55%25'%20dominant-baseline='middle'%20text-anchor='middle'%20font-family='system-ui%2C%20sans-serif'%20font-size='14'%20fill='%23ddd'%3ENFT%20Collection%3C/text%3E%3C/svg%3E" alt="Collection NFT" />
          </a>
          <div class="meta">
            <h3>Collection NFT ${owner.slice(0, 10)}...</h3>
            <a href="${link}" target="_blank" rel="noopener noreferrer">Voir sur OpenSea 📖</a>
          </div>
        </article>
      `;
    }

    function loadOwners() {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'profiles.php', true);
        xhr.onreadystatechange = function () {
          if (xhr.readyState !== 4) return;
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              resolve(Array.isArray(data.addresses) ? data.addresses : []);
            } catch (e) {
              reject(new Error('Erreur de parsing JSON')); 
            }
          } else {
            reject(new Error('Erreur XMLHttpRequest ' + xhr.status));
          }
        };
        xhr.send();
      });
    }

    async function loadCatalogue() {
      const $loader = $('#loading');
      const $catalogue = $('#catalogue');
      const $error = $('#error');
      $error.hide();
      $loader.show();

      try {
        const owners = await loadOwners();
        if (!owners.length) {
          $catalogue.html('<p style="opacity:.8;">Aucune adresse disponible pour le moment.</p>');
          return;
        }

        const cards = owners.map(buildCard).join('');
        $catalogue.html(cards);
      } catch (err) {
        $error.text('Impossible de charger le catalogue. Réessaie plus tard. 😞').show();
        console.error('Catalogue error:', err);
      } finally {
        $loader.hide();
      }
    }

    $(function () {
      loadCatalogue();
    });
  </script>
</body>
</html>
