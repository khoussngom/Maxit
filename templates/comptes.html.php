<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes - Maxit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/layout/partials/sidebar.layout.php'; ?>
    
    <div class="ml-16 p-6">
        <div class="max-w-4xl mx-auto">
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="flex justify-between items-center py-4 mb-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des comptes</h1>
                <div>
                    <a href="/comptes/secondaire/create" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <i class="bx bx-plus mr-2"></i> Créer un compte secondaire
                    </a>
                </div>
            </div>
            
            <?php if (isset($success) && !empty($success)) : ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error) && !empty($error)) : ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <h2 class="text-xl font-bold text-gray-800 mb-4">Compte principal</h2>
            <?php if ($comptePrincipal) : ?>
                <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Compte <?= htmlspecialchars($comptePrincipal['telephone']) ?>
                                </h3>
                                <p class="text-sm text-orange-500 font-medium">Compte principal</p>
                            </div>
                            <div class="text-2xl font-bold text-gray-900">
                                <?= number_format($comptePrincipal['solde'], 2, ',', ' ') ?> XOF
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    Aucun compte principal trouvé.
                </div>
            <?php endif; ?>
            
            <h2 class="text-xl font-bold text-gray-800 mb-4">Comptes secondaires</h2>
            <?php if (!empty($comptesSecondaires)) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($comptesSecondaires as $compte) : ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Compte <?= htmlspecialchars($compte['telephone']) ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-2">Compte secondaire</p>
                                <p class="text-lg font-bold text-orange-500">
                                    <?= number_format($compte['solde'], 2, ',', ' ') ?> XOF
                                </p>
                                <div class="mt-3 flex justify-end">
                                    <form action="/comptes/change-principal/store" method="post">
                                        <input type="hidden" name="compte_id" value="<?= htmlspecialchars($compte['telephone']) ?>">
                                        <button type="submit" class="inline-flex items-center px-3 py-1 text-xs border border-transparent font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="bx bx-check-shield mr-1"></i> Définir comme principal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4">
                    Aucun compte secondaire trouvé.
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>
