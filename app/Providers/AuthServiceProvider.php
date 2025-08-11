<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\Pagos;
use App\Policies\ClientePolicy;
use App\Policies\ProductoPolicy;
use App\Policies\FacturaPolicy;
use App\Policies\PagoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Cliente::class => ClientePolicy::class,
        Producto::class => ProductoPolicy::class,
        Factura::class => FacturaPolicy::class,
        Pagos::class => PagoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Ejemplo: Gates globales o shortcuts si se requieren
        Gate::define('is-admin', fn (User $user) => $user->hasRole('Administrador'));
    }
}
