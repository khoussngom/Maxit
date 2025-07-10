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
</body>

</html>