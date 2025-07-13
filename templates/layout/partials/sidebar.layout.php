
<div class="fixed left-0 top-0 h-full w-16 bg-gray-900 flex flex-col items-center py-4 space-y-6">
    <!-- Logo ou icône principale -->
    <a href="/accueil" class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
        <i class='bx bx-wallet text-white text-xl'></i>
    </a>
    
    <!-- Navigation items -->
    <nav class="flex flex-col space-y-4">
        <!-- Icône du tableau de bord / accueil -->
        <a href="/accueil" class="w-10 h-10 flex items-center justify-center text-orange-500 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-home text-xl'></i>
        </a>
        
        <!-- Icône des comptes -->
        <a href="/comptes" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-credit-card text-xl'></i>
        </a>
        
        <!-- Icône des transactions -->
        <a href="/transactions" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-transfer-alt text-xl'></i>
        </a>
        
        <!-- Icône de rapport/graphique -->
        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-bar-chart-alt-2 text-xl'></i>
        </div>
    </nav>
    
    <!-- Spacer pour pousser les éléments du bas -->
    <div class="flex-1"></div>
    
    <!-- Éléments du bas -->
    <div class="flex flex-col space-y-4">
        <!-- Icône de paramètres -->
        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-cog text-xl'></i>
        </div>
        
        <!-- Icône de profil -->
        <div class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-user text-xl'></i>
        </div>
        
        <!-- Icône de déconnexion -->
        <a href="/logout" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
            <i class='bx bx-log-out text-xl'></i>
        </a>
    </div>
</div>