<?php require_once dirname(__DIR__) . '/layout/partials/sidebar.layout.php'; ?>

<div class="container p-4">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-orange-500">Transactions du compte <?= $compte['telephone'] ?></h1>
            <a href="<?= getenv('BASE_URL') ?>/recherche/compte" class="text-orange-500 hover:text-orange-700">
                &larr; Retour à la recherche
            </a>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600">Numéro de compte</p>
                    <p class="font-semibold"><?= $compte['telephone'] ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Type de compte</p>
                    <p class="font-semibold"><?= ucfirst($compte['typecompte']) ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Solde actuel</p>
                    <p class="font-semibold text-xl"><?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <form method="get" action="<?= getenv('BASE_URL') ?>/recherche/transactions" class="bg-gray-100 p-4 rounded-lg">
                <input type="hidden" name="compte" value="<?= $compte['telephone'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="dateDebut" class="block text-gray-700 text-sm font-medium mb-2">Date début</label>
                        <input type="date" id="dateDebut" name="dateDebut" 
                               value="<?= $filtres['dateDebut'] ?? '' ?>"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="dateFin" class="block text-gray-700 text-sm font-medium mb-2">Date fin</label>
                        <input type="date" id="dateFin" name="dateFin" 
                               value="<?= $filtres['dateFin'] ?? '' ?>"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="type" class="block text-gray-700 text-sm font-medium mb-2">Type</label>
                        <select id="type" name="type" 
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-orange-500">
                            <option value="">Tous</option>
                            <option value="depot" <?= ($filtres['type'] ?? '') === 'depot' ? 'selected' : '' ?>>
                                Dépôt
                            </option>
                            <option value="retrait" <?= ($filtres['type'] ?? '') === 'retrait' ? 'selected' : '' ?>>
                                Retrait
                            </option>
                            <option value="paiement" <?= ($filtres['type'] ?? '') === 'paiement' ? 'selected' : '' ?>>
                                Paiement
                            </option>
                            <option value="annulation" <?= ($filtres['type'] ?? '') === 'annulation' ? 'selected' : '' ?>>
                                Annulation
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="bg-orange-500 text-white font-medium py-2 px-4 rounded hover:bg-orange-600 transition">
                            Filtrer
                        </button>
                        
                        <a href="<?= getenv('BASE_URL') ?>/recherche/transactions?compte=<?= $compte['telephone'] ?>" 
                           class="ml-2 text-gray-600 hover:text-gray-800 py-2 px-4">
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($transactions)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Date</th>
                            <th class="py-2 px-4 border-b text-left">Type</th>
                            <th class="py-2 px-4 border-b text-left">Montant</th>
                            <th class="py-2 px-4 border-b text-left">Motif</th>
                            <th class="py-2 px-4 border-b text-left">Destination</th>
                            <th class="py-2 px-4 border-b text-left">Source</th>
                            <th class="py-2 px-4 border-b text-left">État</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?= $transaction['id'] ?></td>
                                <td class="py-2 px-4 border-b"><?= $transaction['date'] ?></td>
                                <td class="py-2 px-4 border-b"><?= ucfirst($transaction['type']) ?></td>
                                <td class="py-2 px-4 border-b">
                                    <?= number_format($transaction['montant'], 0, ',', ' ') ?> FCFA
                                </td>
                                <td class="py-2 px-4 border-b"><?= $transaction['motif'] ?? '-' ?></td>
                                <td class="py-2 px-4 border-b"><?= $transaction['destination_telephone'] ?? '-' ?></td>
                                <td class="py-2 px-4 border-b"><?= $transaction['source_telephone'] ?? '-' ?></td>
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
        <?php else: ?>
            <div class="bg-gray-100 p-8 rounded-lg text-center text-gray-600">
                <p class="text-lg">Aucune transaction ne correspond à vos critères de recherche</p>
            </div>
        <?php endif; ?>
    </div>
</div>
