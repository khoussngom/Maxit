
<div class="fixed left-0 top-0 h-full w-16 bg-gray-900 flex flex-col items-center py-4 space-y-6">
    <a href="/accueil" class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
        <i class='bx bx-wallet text-white text-xl'></i>
    </a>
    

    <nav class="flex flex-col space-y-4">

        <a href="/accueil" class="w-10 h-10 flex items-center justify-center text-orange-500 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-home text-xl'></i>
        </a>
        

        <a href="/comptes" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-credit-card text-xl'></i>
        </a>
        

        <a href="/transactions" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-transfer-alt text-xl'></i>
        </a>
        
        <?php if (isset($user) && isset($user->typepersonne) && $user->typepersonne === 'commercial'): ?>

        <a href="/comptes/recherche" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors" title="Rechercher un compte">
            <i class='bx bx-search-alt text-xl'></i>
        </a>
        <?php endif; ?>
        

        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-bar-chart-alt-2 text-xl'></i>
        </div>
    </nav>
    

    <div class="flex-1"></div>
    

    <div class="flex flex-col space-y-4">

        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-cog text-xl'></i>
        </div>
        
        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-user text-xl'></i>
        </div>
        
        <a href="/logout" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-log-out text-xl'></i>
        </a>
    </div>
</div>