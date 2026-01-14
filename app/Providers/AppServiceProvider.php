<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    // Ajusta la clase si su namespace difiere
    Livewire::component('flowforge::board', \Relaticle\Flowforge\Http\Livewire\Board::class);
}
}
