<?php
$wallet = $_COOKIE['wallet_address'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Connexion Wallet</title>
  <style>
    body { margin: 0; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0b0c10; color: #e6e6e6; }
    main { max-width: 640px; margin: 3rem auto; padding: 1.5rem; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); border-radius: 16px; }
    h1 { margin-top: 0; font-size: 1.8rem; }
    p { line-height: 1.6; }
    button { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 1.2rem; border: none; border-radius: 999px; background: #1b6ef6; color: white; font-size: 1rem; cursor: pointer; transition: transform .1s ease; }
    button:hover { transform: translateY(-1px); }
    button:active { transform: translateY(0); }
    .meta { margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,.25); border-radius: 12px; }
    .links a { color: #66d9ff; text-decoration: none; display: inline-block; margin-right: 1rem; }
    .links a:hover { text-decoration: underline; }
    .warning { margin-top: 1rem; color: #ffc107; }
    .error { margin-top: 1rem; color: #ff4c4c; }
  </style>
</head>
<body>
  <main>
    <h1>Connexion Wallet</h1>
    <p>Pour accéder aux pages réservées, connecte ton wallet Ethereum (MetaMask ou un wallet compatible EVM).</p>

    <div id="status" class="meta">
      <p id="statusText">Chargement...</p>
    </div>

    <div id="actions">
      <button id="connectBtn">Connecter mon wallet</button>
      <button id="logoutBtn" style="display:none; background:#ff4961;">Déconnecter</button>
    </div>

    <div class="links" style="margin-top:1.5rem;">
      <a href="TaCollectionNFT.php">Accueil</a>
      <a href="Catalogue.php">Catalogue</a>
      <a href="Nouveau_membre.php">Espace membre</a>
    </div>

    <div id="message" class="warning" role="status"></div>
  </main>

  <script>
    function getCookie(name) {
      const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
      return match ? decodeURIComponent(match[1]) : '';
    }

    function setCookie(name, value, days) {
      const expires = new Date(Date.now() + (days || 1) * 864e5).toUTCString();
      document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; expires=' + expires;
    }

    function deleteCookie(name) {
      document.cookie = name + '=; path=/; max-age=0';
    }

    function updateUI() {
      const wallet = getCookie('wallet_address');
      const statusText = document.getElementById('statusText');
      const connectBtn = document.getElementById('connectBtn');
      const logoutBtn = document.getElementById('logoutBtn');
      const message = document.getElementById('message');

      if (!wallet) {
        statusText.textContent = 'Aucun wallet connecté. Clique sur le bouton ci-dessous pour te connecter.';
        connectBtn.style.display = 'inline-flex';
        logoutBtn.style.display = 'none';
        message.textContent = '';
      } else {
        statusText.innerHTML = 'Wallet connecté : <strong>' + wallet.slice(0,6) + '…' + wallet.slice(-4) + '</strong>';
        connectBtn.style.display = 'none';
        logoutBtn.style.display = 'inline-flex';
        message.textContent = 'Tu peux maintenant visiter le catalogue ou l’espace membre.';
      }
    }

    async function connectWallet() {
      const message = document.getElementById('message');
      if (!window.ethereum) {
        message.textContent = 'Aucun wallet détecté. Installe MetaMask ou un wallet compatible EVM.';
        return;
      }

      try {
        const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
        const address = accounts[0];
        setCookie('wallet_address', address, 7);
        updateUI();
        window.location.href = 'Catalogue.php';
      } catch (err) {
        message.textContent = 'La connexion a été annulée ou a échoué.';
        console.error(err);
      }
    }

    function logout() {
      deleteCookie('wallet_address');
      updateUI();
    }

    document.getElementById('connectBtn').addEventListener('click', connectWallet);
    document.getElementById('logoutBtn').addEventListener('click', logout);

    updateUI();
  </script>
</body>
</html>
