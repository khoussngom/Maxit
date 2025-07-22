<?php require_once dirname(__DIR__) . '/layout/partials/sidebar.layout.php'; ?>

<div class="container p-4">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-orange-500 mb-6">Effectuer un dépôt</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= getenv('BASE_URL') ?>/transactions/depot" class="mb-8">
            <div class="mb-4">
                <label for="compte_telephone" class="block text-gray-700 font-medium mb-2">Compte à créditer</label>
                <select name="compte_telephone" id="compte_telephone" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500">
                    <option value="">Sélectionnez un compte</option>
                    <?php foreach ($comptes as $compte): ?>
                        <option value="<?= $compte['telephone'] ?>">
                            <?= $compte['telephone'] ?> (<?= ucfirst($compte['typecompte']) ?>) - 
                            Solde: <?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="montant" class="block text-gray-700 font-medium mb-2">Montant (FCFA)</label>
                <input type="number" name="montant" id="montant" required min="1" step="1"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500"
                       placeholder="Entrez le montant">
            </div>
            
            <div class="mb-4">
                <label for="motif" class="block text-gray-700 font-medium mb-2">Motif</label>
                <input type="text" name="motif" id="motif"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500"
                       placeholder="Motif du dépôt (optionnel)">
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-orange-500 text-white font-medium py-2 px-4 rounded hover:bg-orange-600 transition">
                    Effectuer le dépôt
                </button>
                
                <a href="<?= getenv('BASE_URL') ?>/transactions" class="text-gray-600 hover:text-gray-800">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
