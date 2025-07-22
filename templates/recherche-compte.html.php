<?php require_once __DIR__ . '/layout/partials/sidebar.layout.php'; ?>

<div class="container p-4">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Recherche de Compte</h1>
            <a href="/accueil" class="text-orange-500 hover:text-orange-600 flex items-center">
                <i class='bx bx-arrow-back mr-1'></i>
                <span>Retour à l'accueil</span>
            </a>
        </div>

        <?php if (isset($error) && !empty($error)) : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl p-6 shadow-md mb-6">
            <form action="/comptes/recherche" method="GET">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="telephone" class="block text-gray-700 font-medium mb-2">Numéro de téléphone / compte</label>
                        <input type="text" name="telephone" id="telephone" required
                              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-orange-500"
                              placeholder="Entrez le numéro de téléphone" value="<?= htmlspecialchars($telephone ?? '') ?>">
                    </div>
                    <div class="self-end">
                        <button type="submit" class="w-full md:w-auto px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <i class='bx bx-search mr-2'></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($compte)) : ?>
            <div class="bg-white rounded-xl p-6 shadow-md mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Détails du Compte</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Numéro:</p>
                        <p class="font-medium"><?= htmlspecialchars($compte['telephone']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Type de compte:</p>
                        <p class="font-medium"><?= ucfirst(htmlspecialchars($compte['typecompte'])) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Solde:</p>
                        <p class="font-medium text-green-600"><?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Propriétaire:</p>
                        <p class="font-medium"><?= isset($personne) ? htmlspecialchars($personne['prenom'] . ' ' . $personne['nom']) : 'N/A' ?></p>
                    </div>
                </div>
            </div>

            <?php if (isset($transactions) && !empty($transactions)) : ?>
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Transactions Récentes</h2>
                    
                    <div class="bg-gray-100 rounded-lg p-3 mb-4">
                        <div class="grid grid-cols-12 font-medium text-gray-700">
                            <div class="col-span-1">#</div>
                            <div class="col-span-3">Date</div>
                            <div class="col-span-3">Type</div>
                            <div class="col-span-3">Motif</div>
                            <div class="col-span-2 text-right">Montant</div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <?php foreach ($transactions as $index => $transaction) : 
                            $typeClass = $transaction['type'] === 'depot' ? 'text-cyan-600' : 'text-orange-600';
                        ?>
                            <div class="border-b pb-2">
                                <div class="grid grid-cols-12 items-center">
                                    <div class="col-span-1 text-gray-500"><?= $index + 1 ?></div>
                                    <div class="col-span-3"><?= date('d/m/Y H:i', strtotime($transaction['date'])) ?></div>
                                    <div class="col-span-3 font-medium <?= $typeClass ?>">
                                        <?= ucfirst(htmlspecialchars($transaction['type'])) ?>
                                        <?php if (isset($transaction['etat']) && $transaction['etat'] === 'canceled') : ?>
                                            <span class="text-xs text-gray-500 ml-1">(Annulée)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-span-3 text-gray-600 truncate"><?= htmlspecialchars($transaction['motif'] ?? '-') ?></div>
                                    <div class="col-span-2 font-medium text-right <?= $typeClass ?>">
                                        <?= number_format($transaction['montant'], 0, ',', ' ') ?> FCFA
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($transactions) >= 10) : ?>
                        <div class="mt-4 text-center">
                            <a href="/transactions?compte=<?= urlencode($compte['telephone']) ?>" class="text-orange-500 hover:text-orange-600">
                                Voir toutes les transactions
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif (isset($compte)) : ?>
                <div class="bg-white rounded-xl p-6 shadow-md text-center">
                    <div class="flex flex-col items-center space-y-3 py-6">
                        <i class='bx bx-calendar-x text-gray-400 text-5xl'></i>
                        <p class="text-gray-600">Aucune transaction trouvée pour ce compte</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
