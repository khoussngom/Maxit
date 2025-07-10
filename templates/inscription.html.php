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


    <div class="bg-white rounded-xl shadow-lg py-10  flex  flex-col items-center w-[80%] p-8">

        <?php $errors = $_SESSION['flash_errors'] ?? []; ?>
        <?php $old = $_SESSION['old_input'] ?? []; ?>

        <form method="post" action="<?= getenv('BASE_URL') ?>/inscription" enctype="multipart/form-data">
            
            <input type="hidden" name="debug" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Téléphone</label>
                    <input type="tel" name="telephone" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" 
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['telephone'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['telephone']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Numéro CNI</label>
                    <input type="text" name="numeroIdentite" value="<?= htmlspecialchars($old['numeroIdentite'] ?? '') ?>"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['numeroIdentite'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['numeroIdentite']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Nom :</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre nom">
                    <?php if (!empty($errors['nom'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['nom']) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Prénom :</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre prénom">
                    <?php if (!empty($errors['prenom'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['prenom']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-orange-500 font-medium mb-2">Adresse :</label>
                <input type="text" name="adresse" value="<?= htmlspecialchars($old['adresse'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Entrez votre adresse">
                <?php if (!empty($errors['adresse'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['adresse']) ?></div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Photo Recto CNI</label>
                    <input type="file" name="photorecto" accept="image/*"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['photorecto'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['photorecto']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Photo Verso CNI</label>
                    <input type="file" name="photoverso" accept="image/*"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['photoverso'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['photoverso']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Login</label>
                    <input type="text" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>"
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['login'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['login']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-orange-500 font-medium mb-2">Mot de passe</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php if (!empty($errors['password'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600">
                S'inscrire
            </button>
        </form>
    </div>
</body>

</html>
<?php unset($_SESSION['flash_errors'], $_SESSION['old_input']); ?>
