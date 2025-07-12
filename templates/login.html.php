<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Maxit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black h-screen w-screen flex items-center justify-center">
    <div class="flex bg-white rounded-2xl shadow-2xl overflow-hidden w-full h-full">

        <div class="flex-1 bg-black flex flex-col items-center justify-center p-8 relative overflow-hidden">
            <img src="/static/logina.png" class="max-w-[80%]" alt="login">

            <div class="fixed bottom-6 left-6">
                <img src="/static/logoORange.png" class="w-20" alt="login">
            </div>
        </div>

        <div class="flex-1 p-8 flex flex-col justify-center">
            <div class="max-w-sm p-10 rounded-xl shadow-[0_0px_15px_black] mx-auto w-full">
                <h2 class="text-3xl font-bold text-orange-500 mb-8 text-center">LOGIN</h2>

                <?php if (isset($success) && $success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error) && $error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Affichage des erreurs de validation -->
                <?php if (isset($errors) && is_array($errors) && !empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php if (isset($errors['global'])): ?>
                            <p><?= htmlspecialchars($errors['global']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (count($errors) > (isset($errors['global']) ? 1 : 0)): ?>
                            <ul class="list-disc ml-5">
                                <?php foreach ($errors as $field => $message): ?>
                                    <?php if ($field !== 'global'): ?>
                                        <li><?= htmlspecialchars($message) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/login" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Numéro de téléphone ou Login</label>
                        <input type="text"  placeholder="login"name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:border-orange-500 transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Mot de Passe</label>
                        <input placeholder="Mot de passe" type="password" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:border-orange-500 transition-colors duration-200">
                    </div>

                    <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors duration-200">
                        Se connecter
                    </button>
                </form>

                <div class="mt-6 text-center text-sm">
                    <a href="#" class="text-black hover:text-black">Mot de passe oublié?</a>
                    <a href="/inscription" class="text-orange-500 hover:text-orange-600 ml-3">S'inscrire</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php unset($_SESSION['flash_errors'], $_SESSION['old_input']); ?>