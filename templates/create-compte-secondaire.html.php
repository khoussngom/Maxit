<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte secondaire - Maxit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/layout/partials/sidebar.layout.php'; ?>
    
    <div class="ml-16 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center py-4 mb-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Créer un compte secondaire</h1>
                <div>
                    <a href="/comptes" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <i class='bx bx-arrow-back mr-2'></i> Retour aux comptes
                    </a>
                </div>
            </div>
            
            <?php if (isset($error) && !empty($error)) : ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="/comptes/secondaire/store" method="POST">
                        <div class="mb-4">
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone du compte secondaire</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" 
                                   id="telephone" name="telephone" required pattern="[0-9]{9}" title="Numéro de téléphone à 9 chiffres">
                            <p class="mt-1 text-sm text-gray-500">Entrez un numéro de téléphone à 9 chiffres.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="montant_initial" class="block text-sm font-medium text-gray-700 mb-1">Montant initial</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="number" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500" 
                                       id="montant_initial" name="montant_initial" min="0" step="0.01" value="0">
                                <span class="inline-flex items-center px-3 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500">XOF</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Montant à transférer depuis le compte principal.</p>
                        </div>
                        
                        <?php if ($comptePrincipal) : ?>
                            <div class="mb-4 p-4 bg-blue-50 rounded-md border-l-4 border-blue-500">
                                <div class="flex items-center">
                                    <i class='bx bx-info-circle text-blue-500 mr-2'></i>
                                    <p class="text-sm text-blue-700">
                                        <strong>Solde disponible:</strong> <?= number_format($comptePrincipal['solde'], 2, ',', ' ') ?> XOF
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <i class='bx bx-plus mr-2'></i> Créer le compte secondaire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
