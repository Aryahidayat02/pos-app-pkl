<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        foreach ([
            Category::class,
            Customer::class,
            Product::class,
            Purchase::class,
            PurchaseItem::class,
            Sale::class,
            SaleItem::class,
            Setting::class,
            Supplier::class,
            User::class,
        ] as $model) {
            $model::observe(ActivityLogObserver::class);
        }

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ($expression), 0, ',', '.'); ?>";
        });
    }
}
