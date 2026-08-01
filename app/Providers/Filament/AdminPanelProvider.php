<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use App\Filament\Widgets\OrderStats;
use App\Filament\Widgets\ProductChart;
use App\Filament\Widgets\RevenueChart;
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
            ->brandName("Blide'Qua")
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
            ])
           ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    /* Background sidebar */
                    .fi-sidebar {
                        background-color: #0f172a !important;
                        border-color: #1e293b !important;
                        width: 14rem !important;
                    }
                    .fi-sidebar-header {
                        background-color: #0f172a !important;
                        border-bottom: 1px solid #1e293b !important;
                        margin-bottom: 0.5rem !important;
                    }

                    /* Brand name */
                    .fi-sidebar-header .fi-logo,
                    .fi-sidebar-header a {
                        color: #f8fafc !important;
                    }

                    /* Nav spacing */
                    .fi-sidebar-nav {
                        padding: 0.5rem 0.75rem !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item {
                        margin-bottom: 0.35rem !important;
                    }

                    /* Menu idle state */
                    .fi-sidebar-nav .fi-sidebar-item .fi-sidebar-item-button {
                        color: #cbd5e1 !important;
                        border-radius: 0.5rem !important;
                        transition: all 0.15s ease !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item .fi-sidebar-item-label {
                        color: #cbd5e1 !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item .fi-icon {
                        color: #94a3b8 !important;
                        background-color: rgba(255, 255, 255, 0.06) !important;
                        padding: 0.35rem !important;
                        border-radius: 0.5rem !important;
                        width: 1.75rem !important;
                        height: 1.75rem !important;
                    }

                    /* Hover state */
                    .fi-sidebar-nav .fi-sidebar-item:hover .fi-sidebar-item-button {
                        background-color: rgba(255, 255, 255, 0.08) !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item:hover .fi-sidebar-item-label,
                    .fi-sidebar-nav .fi-sidebar-item:hover .fi-icon {
                        color: #f8fafc !important;
                    }

                    /* Active/selected item */
                    .fi-sidebar-nav .fi-sidebar-item-active .fi-sidebar-item-button {
                        background-color: rgba(59, 130, 246, 0.2) !important;
                        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.3), 0 2px 8px rgba(59, 130, 246, 0.15) !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item-active .fi-sidebar-item-label,
                    .fi-sidebar-nav .fi-sidebar-item-active .fi-icon {
                        color: #60a5fa !important;
                    }
                    .fi-sidebar-nav .fi-sidebar-item-active .fi-icon {
                        background-color: rgba(59, 130, 246, 0.25) !important;
                    }

                    /* Group label */
                    .fi-sidebar-group-label {
                        color: #64748b !important;
                    }

                    /* Scrollbar sidebar */
                    .fi-sidebar::-webkit-scrollbar {
                        width: 6px;
                    }
                    .fi-sidebar::-webkit-scrollbar-track {
                        background: #0f172a;
                    }
                    .fi-sidebar::-webkit-scrollbar-thumb {
                        background: #334155;
                        border-radius: 3px;
                    }

                    /* Topbar / header */
                    .fi-topbar {
                        background-color: #0f172a !important;
                        border-color: #1e293b !important;
                    }
                    .fi-topbar nav {
                        background-color: #0f172a !important;
                    }
                    .fi-topbar .fi-icon-btn {
                        color: #cbd5e1 !important;
                    }
                    .fi-topbar .fi-icon-btn:hover {
                        background-color: rgba(255, 255, 255, 0.08) !important;
                    }
                    .fi-topbar .fi-avatar {
                        border: 2px solid white !important;
                    }
                </style>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                OrderStats::class,
                RevenueChart::class,
                ProductChart::class
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
