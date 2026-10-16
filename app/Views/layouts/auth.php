<?php
use App\Core\Session;
$flashError = Session::getFlash('error');
$flashSuccess = Session::getFlash('success');
$old = Session::getFlash('old', []);
?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Ingresar') ?> - MiniMarket Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <?= $content ?? '' ?>
    </div>
</body>
</html>
