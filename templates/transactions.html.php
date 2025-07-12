<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Transactions - Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/layout/partials/sidebar.layout.php'; ?>

    <div class="ml-16 p-6">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Toutes mes transactions</h1>
                <a href="/accueil" class="text-orange-500 hover:text-orange-600 flex items-center">
                    <i class='bx bx-arrow-back mr-1'></i>
                    <span>Retour à l'accueil</span>
                </a>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <div class="bg-gray-900 rounded-xl p-4 text-white lg:w-1/4">
                    <div class="flex items-center space-x-3">
                        <span class="text-orange-500 text-lg font-semibold">Solde:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-white text-lg">
                                <?= !empty($comptes) ? number_format($comptes[0]['solde'], 0, ',', ' ') : '0' ?> FCFA
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-4 shadow lg:flex-1">
                    <form action="/transactions" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="type" name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Tous les types</option>
                                <option value="depot" <?= isset($filters['type']) && $filters['type'] === 'depot' ? 'selected' : '' ?>>Dépôt</option>
                                <option value="retrait" <?= isset($filters['type']) && $filters['type'] === 'retrait' ? 'selected' : '' ?>>Retrait</option>
                                <option value="transfert" <?= isset($filters['type']) && $filters['type'] === 'transfert' ? 'selected' : '' ?>>Transfert</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date de transaction</label>
                            <input type="date" id="date" name="date" value="<?= $filters['date'] ?? '' ?>" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            <p class="text-xs text-gray-500 mt-1">Filtrer les transactions effectuées à cette date précise</p>
                        </div>
                        <div class="md:col-span-3 flex justify-between items-center">
                            <div>
                                <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">Éléments par page</label>
                                <select id="perPage" name="perPage" class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" onchange="this.form.submit()">
                                    <option value="7" <?= ($pagination['perPage'] ?? 7) == 7 ? 'selected' : '' ?>>7</option>
                                    <option value="15" <?= ($pagination['perPage'] ?? 7) == 15 ? 'selected' : '' ?>>15</option>
                                    <option value="25" <?= ($pagination['perPage'] ?? 7) == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= ($pagination['perPage'] ?? 7) == 50 ? 'selected' : '' ?>>50</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    <i class='bx bx-search mr-1'></i> Rechercher
                                </button>
                                <a href="/transactions" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <i class='bx bx-reset mr-1'></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl p-4 mb-4">
                <div class="grid grid-cols-12 text-white">
                    <div class="col-span-1 text-orange-500 font-semibold">#</div>
                    <div class="col-span-3 text-orange-500 font-semibold">Date</div>
                    <div class="col-span-4 text-orange-500 font-semibold">Type</div>
                    <div class="col-span-4 text-orange-500 font-semibold text-right">Montant</div>
                </div>
            </div>

            <div class="space-y-2">
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $index => $transaction): 
                        $typeClass = $transaction['type'] === 'depot' ? 'text-cyan-500' : 'text-orange-500';
                        $borderClass = $transaction['type'] === 'depot' ? 'border-l-4 border-l-cyan-500' : 'border-l-4 border-l-orange-500';
                        $numero = ($pagination['currentPage'] - 1) * $pagination['perPage'] + $index + 1;
                        $date = date('d/m/Y H:i', strtotime($transaction['date']));
                    ?>
                        <div class="bg-white rounded-xl p-4 shadow-sm <?= $borderClass ?>">
                            <div class="grid grid-cols-12 items-center">
                                <div class="col-span-1 text-gray-500"><?= $numero ?></div>
                                <div class="col-span-3 text-gray-800"><?= $date ?></div>
                                <div class="col-span-4 font-medium <?= $typeClass ?>">
                                    <?= htmlspecialchars(ucfirst($transaction['type'])) ?>
                                </div>
                                <div class="col-span-4 font-medium text-right <?= $typeClass ?>">
                                    <?= number_format($transaction['montant'], 0, ',', ' ') ?> FCFA
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-xl p-8 text-center border border-gray-200">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <i class='bx bx-search text-gray-400 text-4xl'></i>
                            <span class="text-gray-500 text-lg">Aucune transaction trouvée</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($pagination) && $pagination['totalPages'] > 1): ?>
            <div class="mt-6 flex justify-center">
                <div class="flex space-x-2">
                    <?php if ($pagination['currentPage'] > 1): ?>
                        <a href="/transactions?page=<?= $pagination['currentPage'] - 1 ?><?= !empty($filters) ? '&' . http_build_query(array_merge($filters, ['perPage' => $pagination['perPage']])) : '&perPage=' . $pagination['perPage'] ?>" 
                           class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            <i class='bx bx-chevron-left'></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $pagination['currentPage'] - 2);
                    $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
                    
                    if ($startPage > 1) {
                        echo '<a href="/transactions?page=1' . (!empty($filters) ? '&' . http_build_query(array_merge($filters, ['perPage' => $pagination['perPage']])) : '&perPage=' . $pagination['perPage']) . '" 
                               class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="px-3 py-1">...</span>';
                        }
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        $activeClass = $i === $pagination['currentPage'] ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
                        echo '<a href="/transactions?page=' . $i . (!empty($filters) ? '&' . http_build_query(array_merge($filters, ['perPage' => $pagination['perPage']])) : '&perPage=' . $pagination['perPage']) . '" 
                               class="px-3 py-1 ' . $activeClass . ' rounded-md">' . $i . '</a>';
                    }
                    
                    if ($endPage < $pagination['totalPages']) {
                        if ($endPage < $pagination['totalPages'] - 1) {
                            echo '<span class="px-3 py-1">...</span>';
                        }
                        echo '<a href="/transactions?page=' . $pagination['totalPages'] . (!empty($filters) ? '&' . http_build_query(array_merge($filters, ['perPage' => $pagination['perPage']])) : '&perPage=' . $pagination['perPage']) . '" 
                                class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">' . $pagination['totalPages'] . '</a>';
                    }
                    ?>
                    
                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                        <a href="/transactions?page=<?= $pagination['currentPage'] + 1 ?><?= !empty($filters) ? '&' . http_build_query(array_merge($filters, ['perPage' => $pagination['perPage']])) : '&perPage=' . $pagination['perPage'] ?>" 
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            <i class='bx bx-chevron-right'></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="mt-4 text-center text-gray-600 text-sm">
                Affichage de 
                <?= empty($transactions) ? '0' : (($pagination['currentPage'] - 1) * $pagination['perPage'] + 1) ?> 
                à 
                <?= empty($transactions) ? '0' : min($pagination['currentPage'] * $pagination['perPage'], $pagination['total']) ?> 
                sur 
                <?= $pagination['total'] ?? 0 ?> résultats
            </div>
        </div>
    </div>
</body>

</html>
