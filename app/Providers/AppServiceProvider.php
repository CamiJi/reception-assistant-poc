<?php

namespace App\Providers;

use App\Services\ReceptionAssistant\AiGateway;
use App\Services\ReceptionAssistant\LocalAssistantGateway;
use App\Services\ReceptionAssistant\OpenAiAssistantGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiGateway::class, function () {
            return filled(config('services.openai.api_key'))
                ? new OpenAiAssistantGateway
                : new LocalAssistantGateway;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
