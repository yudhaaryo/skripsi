<?php

namespace App\Providers\Filament;

use AdminDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use App\Filament\Resources\AlatResource;
use App\Filament\Resources\AlatDetailResource;
use App\Filament\Resources\PeminjamanResource;
use App\Filament\Resources\PengembalianResource;
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\BarangMasukResource;
use App\Filament\Resources\BarangKeluarResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\PeranResource;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->colors([
                'primary' => Color::Green,
            ])
            ->brandLogo(asset('img/Logosmk3.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                AdminDashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([

            ])


            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,

            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->databaseNotifications();




    }
    public function navigation(NavigationBuilder $builder): NavigationBuilder
{
    return $builder
        ->groups([
            NavigationGroup::make()
                ->label('Peminjaman Alat')
                ->items([
                    AlatResource::class,
                    AlatDetailResource::class,
                    PeminjamanResource::class,
                    PengembalianResource::class,
                ]),
            NavigationGroup::make()
                ->label('Inventaris Barang')
                ->items([
                    BarangKeluarResource::class,
                    BarangMasukResource::class,
                    BarangResource::class,
                ]),
            NavigationGroup::make()
                ->label('Manajemen Pengguna')
                ->items([
                    UserResource::class,
                ]),
            NavigationGroup::make()
                ->label('Pelindung')
                ->items([
                    PeranResource::class,
                ]),
        ]);
}


    // public function boot(): void
    // {
    //     Filament::serving(function () {
    //         /** @var \App\Models\User $user */
    //         $user = Auth::user();

    //         if (Auth::check() && !$user?->hasAnyRole(['admin', 'guru', 'siswa'])) {
    //             abort(403, 'Anda tidak memiliki akses ke panel admin.');
    //         }
    //     });

    // }

}
