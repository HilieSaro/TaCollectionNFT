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
  <title>Section réservée - Annonces</title>
  <style>
    body { margin: 0; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0b0c10; color: #e6e6e6; }
    header { padding: 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; background: rgba(0,0,0,.55); border-bottom: 1px solid rgba(255,255,255,.1); }
    header a { color: #66d9ff; text-decoration: none; margin-left: 1rem; }
    header a:hover { text-decoration: underline; }
    main { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }
    h1 { margin-top: 0; }
    .card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .announcements { list-style: none; padding: 0; margin: 0; }
    .announcement { background: rgba(0,0,0,.3); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; opacity: 0; }
    .announcement strong { display: block; margin-bottom: .5rem; }
    form { display: grid; gap: .75rem; }
    input[type=text], textarea { width: 100%; padding: .75rem; border-radius: 12px; border: 1px solid rgba(255,255,255,.2); background: rgba(0,0,0,.3); color: #fff; }
    button { padding: .75rem 1.25rem; border: none; border-radius: 999px; background: #1b6ef6; color: white; cursor: pointer; }
    button:hover { filter: brightness(1.05); }
    .info { color: rgba(255,255,255,.75); font-size: .95rem; }
  </style>
</head>
<body>
  <header>
    <div>
      <strong>Connecté :</strong> <?php echo htmlspecialchars($wallet); ?>
      <?php if (in_array(strtolower($wallet), array_map('strtolower', ['0xB410825Ef18466A173d55f28d7D18ADE639E1925', '0x6f3e67E8Baab2ea8451094198B25E9A6a7342574', '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80']))): ?>
      <span style="color: #66d9ff;">🛡️ Tu peux effacer les messages !</span>
      <?php endif; ?>
    </div>
    <div>
      <a href="TaCollectionNFT.php">Accueil 🏠</a>
      <a href="Catalogue.php">Catalogue 📚</a>
      <a href="logout.php">Déconnexion 🚪</a>
    </div>
  </header>

  <main>
    <section class="card">
      <h1>Zone réservée - Annonces</h1>
      <p class="info">Cette page te permet d'afficher et conserver des annonces (localement) autour de ta collection. Les annonces sont gardées dans ton navigateur.</p>

      <form id="announceForm">
        <input type="text" id="title" placeholder="Titre de l'annonce 📝" required maxlength="80" />
        <textarea id="body" placeholder="Texte de l'annonce ✍️" rows="4" required maxlength="400"></textarea>
        <button type="submit">Ajouter l'annonce 🚀</button>
      </form>

      <div style="margin-top: 1.5rem;">
        <h2>Historique des annonces</h2>
      <?php if (!in_array(strtolower($wallet), array_map('strtolower', ['0xB410825Ef18466A173d55f28d7D18ADE639E1925', '0x6f3e67E8Baab2ea8451094198B25E9A6a7342574', '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80']))): ?>
      <p class="info">⚠️ Attention : Tu peux écrire des messages, mais pas les effacer ! 📝 Prends soin de ta rédaction. Pour effacer, contacte HILIE-SARO@outlook.fr ✉️</p>
      <?php endif; ?>
        <ul id="announcements" class="announcements"></ul>
        <?php if (in_array(strtolower($wallet), array_map('strtolower', ['0xB410825Ef18466A173d55f28d7D18ADE639E1925', '0x6f3e67E8Baab2ea8451094198B25E9A6a7342574', '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80']))): ?>
        <button id="clearAll" style="background:#ff4961; margin-top:1rem;">Effacer toutes les annonces 🗑️</button>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="jquery-4.0.0.min.js"></script>
  <script>
    const wallet = '<?php echo htmlspecialchars($wallet); ?>';
    // Les messages sont partagés entre tous les wallets (même stockage),
    // mais seules certaines adresses peuvent les supprimer.
    const STORAGE_KEY = 'ta_collection_announcements_v1';
    const authorizedWallets = [
      '0xb410825ef18466a173d55f28d7d18ade639e1925',
      '0x6f3e67e8baab2ea8451094198b25e9a6a7342574',
      '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80'
    ];
    const isAuthorized = authorizedWallets.includes(wallet.toLowerCase());

    function loadAnnouncements() {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return [];
      try {
        const data = JSON.parse(raw);
        console.log('Loaded announcements:', data);
        return data;
      } catch (e) {
        console.error('Error parsing announcements:', e);
        return [];
      }
    }

    function saveAnnouncements(items) {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        console.log('Saved announcements:', items);
      } catch (e) {
        console.error('Error saving announcements:', e);
      }
    }

    function renderAnnouncements() {
      const items = loadAnnouncements();
      console.log('Rendering announcements:', items);
      const $list = $('#announcements');
      $list.empty();

      if (items.length === 0) {
        $list.append('<li class="announcement">Aucune annonce pour le moment. Ajoute-en une ! 😊</li>');
        $('.announcement').css('opacity', 1);
        return;
      }

      items.forEach((item, index) => {
        let deleteButton = '';
        if (isAuthorized) {
          deleteButton = `<button data-index="${index}" style="margin-top:.75rem; padding:.45rem .9rem; border-radius:999px; border:none; background:rgba(255,255,255,.12); color:#fff; cursor:pointer;">Supprimer 🗑️</button>`;
        }
        const $li = $(
          `<li class="announcement">
             <strong>${item.title}</strong>
             <div>${item.body}</div>
             <div style="margin-top:.5rem; font-size:.85rem; color:rgba(255,255,255,.6);">${new Date(item.createdAt).toLocaleString()}</div>
             ${deleteButton}
           </li>`
        );
        $list.append($li);
      });

      $('.announcement').each(function (i, el) {
        $(el).delay(i * 120).animate({ opacity: 1, top: 0 }, 400);
      });
    }

    $(function () {
      renderAnnouncements();

      $('#announceForm').on('submit', function (event) {
        event.preventDefault();
        const title = $('#title').val().trim();
        const body = $('#body').val().trim();
        if (!title || !body) return;

        const items = loadAnnouncements();
        items.unshift({ title, body, createdAt: new Date().toISOString() });
        saveAnnouncements(items);
        $('#title').val('');
        $('#body').val('');
        renderAnnouncements();
      });

      $('#announcements').on('click', 'button', function () {
        const index = Number($(this).data('index'));
        const items = loadAnnouncements();
        items.splice(index, 1);
        saveAnnouncements(items);
        renderAnnouncements();
      });

      $('#clearAll').on('click', function () {
        if (!confirm('Supprimer toutes les annonces ? ⚠️')) return;
        localStorage.removeItem(STORAGE_KEY);
        renderAnnouncements();
      });
    });
  </script>
</body>
</html>
