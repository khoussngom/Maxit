<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer de compte principal - Maxit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/layout/partials/sidebar.layout.php'; ?>
    
    <div class="ml-16 p-6">
        <div class="max-w-4xl mx-auto">
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="flex justify-between items-center py-4 mb-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Changer de compte principal</h1>
                <div>
                    <a href="/comptes" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="bx bx-arrow-back mr-2"></i> Retour aux comptes
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
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Confirmation du changement de compte principal</h2>
                
                <?php if ($comptePrincipal) : ?>
                    <div class="mb-6">
                        <h3 class="text-md font-medium text-gray-700">Compte principal actuel :</h3>
                        <div class="mt-2 p-3 bg-gray-50 rounded-md">
                            <p><strong>Numéro :</strong> <?= htmlspecialchars($comptePrincipal['telephone']) ?></p>
                            <p><strong>Solde :</strong> <?= number_format($comptePrincipal['solde'], 2, ',', ' ') ?> XOF</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($comptesSecondaires)) : ?>
                    <form action="/comptes/change-principal/store" method="post" class="mt-4">
                        <div class="mb-4">
                            <label for="compte_id" class="block text-sm font-medium text-gray-700">Choisir un nouveau compte principal :</label>
                            <select id="compte_id" name="compte_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                <?php foreach ($comptesSecondaires as $compte) : ?>
                                    <option value="<?= htmlspecialchars($compte['telephone']) ?>" <?= ($compteSelectionne && $compte['telephone'] == $compteSelectionne['telephone']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($compte['telephone']) ?> - Solde: <?= number_format($compte['solde'], 2, ',', ' ') ?> XOF
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="bx bx-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Attention :</strong> En changeant votre compte principal, l'ancien compte principal deviendra un compte secondaire.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-4">
                            <a href="/comptes" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 mr-3">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Confirmer le changement
                            </button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4">
                        Aucun compte secondaire disponible pour devenir compte principal.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
