<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Загрузка всех сервисов для приложения.
     *
     * @return void
     */
    public function boot()
    {
        // Passport маршруты должны быть зарегистрированы явно для старых версий
       

       
    }
}
