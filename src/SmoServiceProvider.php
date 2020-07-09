<?php

namespace Vasilysmolin\smo;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class SmoServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $filesystem)
    {
        if (function_exists('config_path')) { // function not available and 'publish' not relevant in Lumen
            $this->publishes([
                __DIR__.'/../config/smoConfig.php' => config_path('smoConfig.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_smo_tables.php.stub' => $this->getMigrationFileName($filesystem),
            ], 'migrations');
        }

//        $this->registerMacroHelpers();

        $this->commands([
            Commands\CacheReset::class
        ]);

//        $this->registerModelBindings();
//
//        $permissionLoader->clearClassPermissions();
//        $permissionLoader->registerPermissions();

//        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
//            return $permissionLoader;
//        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/smoConfig.php',
            'permission'
        );

//        $this->app->bind('smo', function () {
//            return new Smo;
//        }, true);
//        $this->app->bindIf('smo', function () {
//            return new Smo;
//        }, true);
//        $this->app->singleton('smo', function ($app) {
//            return new Smo;
//        });
//
//
//        $this->app->bindMethod('smoBind', function (){
//            return 1123;
//        });
//        $this->app->instance('smo', Smo::class);
//        $this->app->alias('smo', Smo::class);
//        $r = \Closure::fromCallable(function (){
//            return 2 + 3;
//        });
//        $this->app->extend('smo', $r);
//        dd($this->app->make('smo'));
//        dd($this->app->isAlias(Smo::class));
//        dd($this->app->make(Smo::class));
//        $this->app->__set(222,2);
//        dd($this->app->__get(222));
//        dd($this->app->bound(Smo::class));
//        dd($this->app->has('smo'));
//        dd($this->app->resolved(Smo::class));
//        dd($this->app->isShared('smo'));
//        dd($this->app->hasMethodBinding('smoBind'));

//        dd($this->app->callMethodBinding('smoBind',Smo::class));
//            dd(SmoFacade::generate());

//        $this->registerBladeExtensions();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Smo::class, 'smo'];
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['smoConfig.models'];

        if (! $config) {
            return;
        }

//        $this->app->bind(PermissionContract::class, $config['permission']);
//        $this->app->bind(RoleContract::class, $config['role']);
    }

    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('role', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('elserole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php elseif(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasrole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasanyrole', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAnyRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasallroles', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAllRoles({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('unlessrole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php if(!auth({$guard})->check() || ! auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endunlessrole', function () {
                return '<?php endif; ?>';
            });
        });
    }

    protected function registerMacroHelpers()
    {
        if (! method_exists(Route::class, 'macro')) { // Lumen
            return;
        }

        Route::macro('role', function ($roles = []) {
            if (! is_array($roles)) {
                $roles = [$roles];
            }

            $roles = implode('|', $roles);

            $this->middleware("role:$roles");

            return $this;
        });

        Route::macro('permission', function ($permissions = []) {
            if (! is_array($permissions)) {
                $permissions = [$permissions];
            }

            $permissions = implode('|', $permissions);

            $this->middleware("permission:$permissions");

            return $this;
        });
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path.'*_create_smo_tables.php');
            })->push($this->app->databasePath()."/migrations/{$timestamp}_create_smo_tables.php")
            ->first();
    }
}
