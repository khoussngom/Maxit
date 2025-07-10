<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 h-screen flex flex-col items-center p-4">

    <h1 class="text-3xl font-bold text-orange-500 mb-10 text-center">INSCRIPTION</h1>


    <div class="bg-white rounded-xl shadow-lg py-10  flex w-[80%] p-8">

        <form class="space-y-6 w-full">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Numéro de téléphone :</label>
                    <input type="tel" name="telephone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre numéro de téléphone">
                    <?php if (!empty($errors['telephone'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['telephone']) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Numéro CNI :</label>
                    <input type="text" name="numeroIdentite" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre numéro CNI">
                    <?php if (!empty($errors['numeroIdentite'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['numeroIdentite']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Nom :</label>
                    <input type="text" name="nom" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre nom">
                    <?php if (!empty($errors['nom'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['nom']) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Prénom :</label>
                    <input type="text" name="prenom" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre prénom">
                    <?php if (!empty($errors['prenom'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['prenom']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-orange-500 font-medium mb-2">Adresse :</label>
                <input type="text" name="adresse" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre adresse">
                <?php if (!empty($errors['adresse'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['adresse']) ?></div>
                <?php endif; ?>
            </div>

            <div class="flex justify-center gap-8 my-6">
                <div class="text-center">
                    <p class="text-orange-500 font-medium mb-2">Recto</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 cursor-pointer hover:border-orange-500 transition-colors">
                        <i class="bx bx-image text-4xl text-teal-500"></i>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-orange-500 font-medium mb-2">Verso</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 cursor-pointer hover:border-orange-500 transition-colors">
                        <i class="bx bx-image text-4xl text-teal-500"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-gray-800 text-orange-500 font-bold py-4 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                S'inscrire
            </button>
        </form>
    </div>
</body>

</html>
<?php unset($_SESSION['flash_errors']); ?>
