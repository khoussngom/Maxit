<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Maxit' ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Boxicons pour les icônes -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316', // Orange pour Maxit
                        secondary: '#0f172a',
                        light: '#f8fafc',
                        dark: '#1e293b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
        }
        .sidebar-content-layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex-grow: 1;
            padding: 1.5rem;
            margin-left: 4rem; /* 64px pour la sidebar */
        }
    </style>
</head>
<body>
    <div class="sidebar-content-layout">
        
        <main class="main-content">
            <div class="max-w-7xl mx-auto">
                <header class="mb-6">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">
                            <?= $title ?? 'Maxit' ?>
                        </h1>
                        <?php if (isset($user)): ?>
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-600">
                                    <?= htmlspecialchars($user->prenom ?? '') ?> <?= htmlspecialchars($user->nom ?? '') ?>
                                </span>
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-700">
                                    <?= strtoupper(substr($user->prenom ?? 'U', 0, 1)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
                
                <?php echo $contentForLayout; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-auto-close');
                alerts.forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>
