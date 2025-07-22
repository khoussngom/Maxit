<?php require_once dirname(__DIR__) . '/layout/partials/sidebar.layout.php'; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-orange-500">Recherche de compte</h2>
        <p class="text-gray-600">Recherchez un compte client par son numéro de téléphone</p>
    </div>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
        
        <form method="get" action="<?= $baseUrl ?? '' ?>/comptes/recherche" class="mb-8">
            <div class="mb-4">
                <label for="telephone" class="block text-gray-700 font-medium mb-2">Numéro de téléphone / compte</label>
                <input type="text" name="telephone" id="telephone" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500"
                       placeholder="Entrez le numéro de téléphone (ex: 774730039)"
                       value="<?= isset($telephone) ? htmlspecialchars($telephone) : '' ?>">
                <p class="text-xs text-gray-500 mt-1">Note: Entrez le numéro sans indicatif international</p>
            </div>
            
            <button type="submit" class="bg-orange-500 text-white font-medium py-2 px-4 rounded hover:bg-orange-600 transition">
                Rechercher
            </button>
        </form>
        
        <?php if (isset($compte)): ?>
            <div class="border-t pt-6 mt-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informations du compte</h2>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Numéro de compte</p>
                            <p class="font-semibold"><?= $compte['telephone'] ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Solde actuel</p>
                            <p class="font-semibold text-xl"><?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Type de compte</p>
                            <p class="font-semibold"><?= ucfirst($compte['typecompte']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Propriétaire</p>
                            <p class="font-semibold"><?= $compte['personne_telephone'] ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($transactions)): ?>
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Dernières transactions</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Date</th>
                                    <th class="py-2 px-4 border-b text-left">Type</th>
                                    <th class="py-2 px-4 border-b text-left">Montant</th>
                                    <th class="py-2 px-4 border-b text-left">Motif</th>
                                    <th class="py-2 px-4 border-b text-left">État</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="py-2 px-4 border-b"><?= $transaction['date'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= ucfirst($transaction['type']) ?></td>
                                        <td class="py-2 px-4 border-b">
                                            <?= number_format($transaction['montant'], 0, ',', ' ') ?> FCFA
                                        </td>
                                        <td class="py-2 px-4 border-b"><?= $transaction['motif'] ?? '-' ?></td>
                                        <td class="py-2 px-4 border-b">
                                            <?php if ($transaction['etat'] === 'completed'): ?>
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
                                                    Complétée
                                                </span>
                                            <?php elseif ($transaction['etat'] === 'pending'): ?>
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded">
                                                    En attente
                                                </span>
                                            <?php elseif ($transaction['etat'] === 'canceled'): ?>
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">
                                                    Annulée
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="<?= getenv('BASE_URL') ?>/recherche/transactions?compte=<?= $compte['telephone'] ?>" 
                           class="text-orange-500 hover:text-orange-700 font-medium">
                            Voir toutes les transactions →
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-100 p-4 rounded text-center text-gray-600">
                        Aucune transaction trouvée pour ce compte
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
