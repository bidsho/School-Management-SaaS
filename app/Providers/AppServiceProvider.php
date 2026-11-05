<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Services\TenantContext;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // Keep the Fortify login response binding.
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );

        // Ensure the tenant context is scoped to each request lifecycle.
        $this->app->scoped(
            TenantContext::class,
            fn () => new TenantContext()
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        // Reapply tenant resolution to Livewire component requests.
        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\ResolveSchoolTenant::class,
        ]);

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
