<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <div class="ml-16 p-6">
        <div class="max-w-4xl mx-auto">

            <div class="bg-gray-900 rounded-xl p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-orange-500 text-xl font-semibold">Solde:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-white text-xl"><?= number_format($solde, 0, ',', ' ') ?> FCFA</span>
                            <i class='bx bx-show text-orange-500'></i>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-orange-500 text-xl font-semibold">changer compte:</span>
                        <div class="flex items-center space-x-2">
                            <i class='bx bx-dollar-circle text-orange-500'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl p-4 mb-4">
                <div class="flex justify-between items-center text-white">
                    <span class="text-orange-500 text-xl font-semibold">Type</span>
                    <span class="text-orange-500 text-xl font-semibold">Montant</span>
                </div>
            </div>

            <div class="space-y-3">

                <div class="bg-white rounded-xl p-4 border-2 border-orange-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-cyan-500 font-semibold">Dépôt</span>
                        </div>
                        <span class="text-cyan-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border-2 border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-cyan-500 font-semibold">Dépôt</span>
                            <i class='bx bx-chevron-right text-gray-400'></i>
                        </div>
                        <span class="text-cyan-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border-2 border-orange-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-orange-500 font-semibold">retrait</span>
                        </div>
                        <span class="text-orange-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border-2 border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-cyan-500 font-semibold">Dépôt</span>
                        </div>
                        <span class="text-cyan-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border-2 border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-cyan-500 font-semibold">Dépôt</span>
                        </div>
                        <span class="text-cyan-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border-2 border-orange-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-orange-500 font-semibold">retrait</span>
                        </div>
                        <span class="text-orange-500 font-semibold">25 000 frcs</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button class="bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors flex items-center space-x-2">
                    <span>voir plus</span>
                    <i class='bx bx-chevron-right'></i>
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Tableau de bord</h1>

        <?php if (isset($user)): ?>
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Bienvenue, <?= htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()) ?></h2>
            <p class="text-gray-600">
                Téléphone: <?= htmlspecialchars($user->getTelephone()) ?><br>
                Adresse: <?= htmlspecialchars($user->getAdresse()) ?>
            </p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Mes comptes</h2>

            <?php if (empty($comptes)): ?>
            <p class="text-gray-600">Vous n'avez pas encore de compte.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solde
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comptes as $compte): ?>
                        <tr>
                            <td class="py-4 px-4 border-b border-gray-200"><?= htmlspecialchars($compte['telephone']) ?></td>
                            <td class="py-4 px-4 border-b border-gray-200"><?= htmlspecialchars($compte['typecompte']) ?></td>
                            <td class="py-4 px-4 border-b border-gray-200"><?= number_format($compte['solde'], 2, ',', ' ') ?> FCFA
                            </td>
                            <td class="py-4 px-4 border-b border-gray-200">
                                <a href="/compte/<?= htmlspecialchars($compte['telephone']) ?>" class="text-blue-500 hover:text-blue-700">Détails</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section des 10 dernières transactions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Mes 10 dernières transactions</h2>

            <?php if (empty($transactions)): ?>
            <p class="text-gray-600">Aucune transaction récente.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compte
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td class="py-4 px-4 border-b border-gray-200">
                                <?= $transaction->getFormattedDate() ?>
                            </td>
                            <td class="py-4 px-4 border-b border-gray-200">
                                <?= htmlspecialchars($transaction->getCompteTelephone()) ?>
                            </td>
                            <td class="py-4 px-4 border-b border-gray-200">
                                <?php
                                $typeClass = '';
                                switch ($transaction->getType()) {
                                    case 'depot':
                                        $typeClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'retrait':
                                        $typeClass = 'bg-red-100 text-red-800';
                                        break;
                                    case 'transfert':
                                        $typeClass = 'bg-blue-100 text-blue-800';
                                        break;
                                    default:
                                        $typeClass = 'bg-gray-100 text-gray-800';
                                }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?= $typeClass ?>">
                                    <?= htmlspecialchars(ucfirst($transaction->getType())) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 border-b border-gray-200">
                                <?php
                                $amountClass = $transaction->getType() === 'depot' ? 'text-green-600' : 'text-red-600';
                                if ($transaction->getType() === 'transfert') {
                                    $amountClass = 'text-blue-600';
                                }
                                ?>
                                <span class="font-medium <?= $amountClass ?>">
                                    <?= $transaction->getFormattedMontant() ?> FCFA
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>