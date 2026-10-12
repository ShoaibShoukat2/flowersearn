<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\PlanUpgrade;
use App\Models\Language;
use App\Models\Ptc;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $general = gs();
            $viewShare['general'] = $general;
            $activeTemplate = activeTemplate();
            $viewShare['activeTemplate'] = $activeTemplate;
            $viewShare['activeTemplateTrue'] = activeTemplate(true);
            $viewShare['language'] = Language::all();
            $viewShare['emptyMessage'] = 'Data not found';
            view()->share($viewShare);


            view()->composer('admin.partials.sidenav', function ($view) {
                $view->with([
                    'bannedUsersCount'           => User::banned()->count(),
                    'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                    'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                    'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                    'kycPendingUsersCount'   => User::kycPending()->count(),
                    'pendingTicketCount'         => SupportTicket::whereIN('status', [0,2])->count(),
                    'pendingDepositsCount'    => Deposit::pending()->count(),
                    'pendingWithdrawCount'    => Withdrawal::pending()->count(),
                    'pendingPlanUpgradesCount' => $this->pendingPlanUpgrades(),
                    'pendingPtcCount'    => Ptc::pending()->count(),
                ]);
            });

            view()->composer('admin.partials.topnav', function ($view) {
                $view->with([
                    'adminNotifications'=>AdminNotification::where('read_status',0)->with('user')->orderBy('id','desc')->take(10)->get(),
                    'adminNotificationCount'=>AdminNotification::where('read_status',0)->count(),
                ]);
            });

            view()->composer('partials.seo', function ($view) {
                $seo = Frontend::where('data_keys', 'seo.data')->first();
                $view->with([
                    'seo' => $seo ? $seo->data_values : $seo,
                ]);
            });

            if($general->force_ssl || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')){
                \URL::forceScheme('https');
            }


            Paginator::useBootstrapFour();
        } catch (\Exception $e) {
            \Log::error('AppServiceProvider boot error: ' . $e->getMessage());
            // Set minimal defaults to prevent complete failure
            view()->share([
                'general' => (object)[
                    'site_name' => 'SnapsTubes',
                    'cur_text' => 'USD',
                    'cur_sym' => '$',
                    'active_template' => 'basic',
                    'force_ssl' => false,
                    'whatsapp_community_link' => null,
                    'whatsapp_channel_link' => null,
                ],
                'activeTemplate' => 'templates.basic.',
                'activeTemplateTrue' => 'assets/templates/basic/',
                'language' => [],
                'emptyMessage' => 'Data not found',
            ]);
        }
    }

    protected function pendingPlanUpgrades(): int
    {
        try {
            if (!ensurePlanUpgradesTable()) {
                return 0;
            }

            return PlanUpgrade::pending()->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
