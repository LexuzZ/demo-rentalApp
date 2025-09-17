<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CarResource\Widgets\AvailableCarsOverview;
use App\Filament\Resources\CarResource\Widgets\MobilReadyStats;
use App\Filament\Widgets\AnnouncementWidget;
use App\Filament\Widgets\ArusKasTable;
use App\Filament\Widgets\AvailableCarsOverview as WidgetsAvailableCarsOverview;
use App\Filament\Widgets\DashboardMonthlySummary;
use App\Filament\Widgets\InvoiceTable;
use App\Filament\Widgets\MobilKeluar;
use App\Filament\Widgets\MobilKembali;
use App\Filament\Widgets\MonthlyStaffRankingWidget;
use App\Filament\Widgets\OverdueTasksWidget;
use App\Filament\Widgets\StaffRankingWidget;
use App\Filament\Widgets\TempoDueToday;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Widgets\ChartWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            // ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(true)
            ->favicon(asset('semetonpesiar.png'))
            //   ->brandLogo(asset('semetonpesiar.svg'))
            // ->domain('');
            ->colors([
                'primary' => Color::Indigo,   // untuk tombol utama, link
                'success' => Color::Emerald,  // status mobil ready / transaksi sukses
                'danger' => Color::Rose,     // mobil rusak / gagal transaksi
                'warning' => Color::Amber,    // overdue, jatuh tempo
                'info' => Color::Sky,      // informasi umum
                'gray' => Color::Zinc,     // background / teks netral
            ])
            // ->theme(asset('css/filament/admin/theme.css'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->font('0.2rem')
            ->brandName('Demo Aplikasi')
            ->font('Poppins')
            // ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            // ->spa()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([


                WidgetsAvailableCarsOverview::class,
                // StaffRankingWidget::class,
                // ArusKasTable::class,
                // MonthlyStaffRankingWidget::class,
                // DashboardMonthlySummary::class,
                // OverdueTasksWidget::class,
                // MobilKembali::class,
                // TempoDueToday::class,

                    // \App\Filament\Widgets\MonthlyRevenueChart::class,
                // MobilKeluar::class,


                // MobilReadyStats::class,


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
            ]);
    }
}
