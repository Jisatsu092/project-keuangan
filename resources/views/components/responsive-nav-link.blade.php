<!-- Responsive Navigation Menu -->
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
        
        <!-- Accounts Mobile -->
        <div class="border-t border-gray-200"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            {{ __('Chart of Accounts') }}
        </div>
        <x-responsive-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.index')">
            {{ __('Daftar Akun') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('accounts.create')" :active="request()->routeIs('accounts.create')">
            {{ __('Buat Akun Baru') }}
        </x-responsive-nav-link>

        <!-- Journals Mobile -->
        <div class="border-t border-gray-200"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            {{ __('Jurnal') }}
        </div>
        <x-responsive-nav-link :href="route('journals.index')" :active="request()->routeIs('journals.index')">
            {{ __('Daftar Jurnal') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('journals.create')" :active="request()->routeIs('journals.create')">
            {{ __('Buat Jurnal Baru') }}
        </x-responsive-nav-link>

        <!-- Reports Mobile -->
        <div class="border-t border-gray-200"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            {{ __('Laporan') }}
        </div>
        <x-responsive-nav-link :href="route('reports.trial-balance')" :active="request()->routeIs('reports.trial-balance')">
            {{ __('Neraca Saldo') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('reports.balance-sheet')" :active="request()->routeIs('reports.balance-sheet')">
            {{ __('Neraca') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('reports.income-statement')" :active="request()->routeIs('reports.income-statement')">
            {{ __('Laba Rugi') }}
        </x-responsive-nav-link>
    </div>
</div>