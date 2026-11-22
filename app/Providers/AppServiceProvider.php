<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Support\CampusContext;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CampusContext::class, fn () => new CampusContext());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('[Lost & Found] Permintaan Atur Ulang Kata Sandi')
                ->greeting('Halo ' . ($notifiable->name ?? 'Pengguna') . ',')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Lost & Found Anda.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Tautan ini akan kedaluwarsa dalam 60 menit.')
                ->line('Jika Anda tidak merasa meminta pengaturan ulang sandi, abaikan email ini.');
        });
    }
}