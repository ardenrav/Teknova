<?php
$to = 'contact@teknova-sarl.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$service = trim($_POST['service'] ?? 'Non précisé');
$message = trim($_POST['message'] ?? '');
$errors = [];

if ($nom === '') {
    $errors[] = 'Votre nom est requis.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Votre e-mail est invalide.';
}

if ($telephone === '') {
    $errors[] = 'Votre numéro de téléphone est requis.';
}

if ($message === '') {
    $errors[] = 'Veuillez décrire votre projet.';
}

function renderPage($title, $content) {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>
      <style>
        :root {
          --bg: #0b1220;
          --panel: #111827;
          --panel-soft: #f8fafc;
          --text: #0f172a;
          --muted: #475569;
          --primary: #2563eb;
          --primary-dark: #1d4ed8;
        }
        * { box-sizing: border-box; }
        body {
          margin: 0;
          min-height: 100vh;
          display: grid;
          place-items: center;
          font-family: Arial, Helvetica, sans-serif;
          background: linear-gradient(135deg, #eff6ff, #f8fafc);
          color: var(--text);
        }
        .card {
          width: min(640px, calc(100% - 32px));
          background: #fff;
          border: 1px solid #e2e8f0;
          border-radius: 18px;
          box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
          padding: 32px 24px;
        }
        .badge {
          display: inline-block;
          padding: 8px 12px;
          border-radius: 999px;
          background: rgba(37, 99, 235, 0.1);
          color: var(--primary);
          font-size: 12px;
          font-weight: 700;
          letter-spacing: 0.04em;
          text-transform: uppercase;
        }
        h1 { font-size: clamp(1.8rem, 3vw, 2.5rem); margin: 18px 0 12px; }
        p { line-height: 1.7; color: var(--muted); margin: 0 0 16px; }
        ul { margin: 0 0 22px; padding-left: 18px; color: var(--muted); }
        a.button {
          display: inline-block;
          background: var(--primary);
          color: #fff;
          text-decoration: none;
          border-radius: 12px;
          padding: 12px 18px;
          font-weight: 700;
        }
        a.button:hover { background: var(--primary-dark); }
      </style>
    </head>
    <body>
      <main class="card">
        <div class="badge">Teknova</div>
        ' . $content . '
      </main>
    </body>
    </html>';
}

if (!empty($errors)) {
    $messageContent = '
      <h1>Une information manque</h1>
      <p>Merci de vérifier les éléments suivants :</p>
      <ul>
        ' . implode('', array_map(function ($error) { return '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>'; }, $errors)) . '
      </ul>
      <p><a class="button" href="index.html#contact">Retour au formulaire</a></p>
    ';
    renderPage('Erreur - formulaire Teknova', $messageContent);
    exit;
}

$subject = 'Nouvelle demande de devis Teknova';
$body = "Bonjour,\n\n" .
    "Nom : $nom\n" .
    "E-mail : $email\n" .
    "Téléphone : $telephone\n" .
    "Service souhaité : $service\n\n" .
    "Projet :\n$message\n";

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = @mail($to, $subject, $body, $headers);

if ($sent) {
    $messageContent = '
      <h1>Merci !</h1>
      <p>Votre demande a bien été reçue. Nous reviendrons vers vous rapidement pour vous proposer une réponse adaptée à votre projet.</p>
      <p><a class="button" href="index.html#contact">Retour au site</a></p>
    ';
    renderPage('Demande envoyée - Teknova', $messageContent);
} else {
    $messageContent = '
      <h1>Le message n’a pas pu être envoyé</h1>
      <p>Il y a eu un problème temporaire lors de l’envoi de votre demande. Merci de réessayer plus tard ou de nous écrire directement à contact@teknova-sarl.com.</p>
      <p><a class="button" href="index.html#contact">Réessayer</a></p>
    ';
    renderPage('Erreur d’envoi - Teknova', $messageContent);
}
