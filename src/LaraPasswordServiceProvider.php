<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword;

use Illuminate\Contracts\Foundation\Application;
use Yekovalenko\LaraPassword\Commands\HashCommand;
use Illuminate\Support\ServiceProvider;

class LaraPasswordServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // migrations
        $this->publishes([
            __DIR__ . '/resources/migrations/' => database_path('migrations')
        ], 'migrations');

        // config
        $this->publishes([
            __DIR__ . '/config/larapassword.php' => config_path('larapassword.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLaraPassword($this->app);

        // configurations
        $this->mergeConfigFrom(__DIR__ . '/config/larapassword.php', 'larapassword');

        $this->app->bind('command.larapassword:hash', HashCommand::class);

        $this->commands([
            'command.larapassword:hash',
        ]);
    }

    /**
     * Register the Lara Password.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerLaraPassword(Application $app)
    {
        $this->app->singleton('larapassword', function () {
            return new LaraPasswordManage();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            LaraPasswordManage::class,
            'lara.password',
        ];
    }
}
