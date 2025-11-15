<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
    
    <!-- Tambahin menu baru -->
    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
        {{ __('Transaksi') }}
    </x-nav-link>
    
    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
        {{ __('Laporan') }}
    </x-nav-link>
</div>