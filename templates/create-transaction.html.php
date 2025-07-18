<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Transaction - Maxit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/layout/partials/sidebar.layout.php'; ?>

    <div class="ml-16 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center py-4 mb-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle Transaction</h1>
                <div>
                    <a href="/transactions" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <i class='bx bx-arrow-back mr-2'></i> Retour aux transactions
                    </a>
                </div>
            </div>

            <?php if (isset($error) && !empty($error)) : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="/transactions/store" method="POST">
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de transaction</label>
                            <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="depot">Dépôt</option>
                                <option value="retrait">Retrait</option>
                                <option value="transfert">Transfert</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="compte_telephone" class="block text-sm font-medium text-gray-700 mb-1">Compte source</label>
                            <select id="compte_telephone" name="compte_telephone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" required>
                                <option value="">Sélectionnez un compte</option>
                                <?php if (isset($comptes) && is_array($comptes)): ?>
                                    <?php foreach ($comptes as $compte): ?>
                                        <option value="<?= htmlspecialchars($compte['telephone']) ?>">
                                            <?= htmlspecialchars($compte['telephone']) ?> - 
                                            <?= (isset($compte['typecompte']) && $compte['typecompte'] === 'principal') ? 'Principal' : 'Secondaire' ?> - 
                                            Solde: <?= number_format($compte['solde'], 2, ',', ' ') ?> XOF
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div id="destination_container" class="mb-4 hidden">
                            <label for="destination_telephone" class="block text-sm font-medium text-gray-700 mb-1">Destination du transfert</label>
                            <input type="text" id="destination_telephone" name="destination_telephone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500" pattern="[0-9]{9}" title="Numéro de téléphone à 9 chiffres">
                            <p class="mt-1 text-sm text-gray-500">Entrez un numéro de téléphone à 9 chiffres pour le destinataire.</p>
                        </div>

                        <div class="mb-4">
                            <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="number" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500" id="montant" name="montant" min="0" step="0.01" required>
                                <span class="inline-flex items-center px-3 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500">XOF</span>
                            </div>
                        </div>

                        <!-- <div class="mb-4">
                            <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif (optionnel)</label>
                            <textarea id="motif" name="motif" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"></textarea>
                        </div> -->

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <i class='bx bx-send mr-2'></i> Effectuer la transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour afficher/masquer le champ de destination selon le type de transaction
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const destinationContainer = document.getElementById('destination_container');
            const destinationInput = document.getElementById('destination_telephone');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'transfert') {
                    destinationContainer.classList.remove('hidden');
                    destinationInput.setAttribute('required', 'required');
                } else {
                    destinationContainer.classList.add('hidden');
                    destinationInput.removeAttribute('required');
                }
            });
        });
    </script>
</body>

</html>