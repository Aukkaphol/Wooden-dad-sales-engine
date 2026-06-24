<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::post('/webhooks/line', function (Request $request) {
    Log::info('LINE webhook payload', $request->all());
    return response('OK', 200);
})->withoutMiddleware([
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
]);
use App\Http\Controllers\AdminCompanySettingController;
use App\Http\Controllers\AdminCustomerReviewController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminFacebookSettingController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AdminInstallationScheduleController;
use App\Http\Controllers\AdminLeadController;
use App\Http\Controllers\AdminLineSettingController;
use App\Http\Controllers\AdminMarketingCenterController;
use App\Http\Controllers\AdminMarketingHomepageController;
use App\Http\Controllers\AdminMaterialController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminProductionController;
use App\Http\Controllers\AdminPurchaseController;
use App\Http\Controllers\AdminPurchaseRequestController;
use App\Http\Controllers\AdminQuotationController;
use App\Http\Controllers\AdminSupplierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacebookWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\SetLocaleFromUrl;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/th');

foreach ([
    'portfolio',
    'bedroom-set',
    'reviews',
    'lead',
    'thank-you',
    'login',
    'admin/dashboard',
    'admin/leads',
    'admin/quotations',
    'admin/products',
    'admin/materials',
    'admin/production',
    'admin/purchase',
    'admin/suppliers',
    'admin/settings/line',
    'admin/settings/facebook',
    'admin/settings/company',
] as $legacyPath) {
    Route::redirect('/'.$legacyPath, '/th/'.$legacyPath);
}

Route::get('/webhooks/facebook', [FacebookWebhookController::class, 'verify'])->name('webhooks.facebook.verify');
Route::post('/webhooks/facebook', [FacebookWebhookController::class, 'receive'])->name('webhooks.facebook.receive');

Route::prefix('{locale}')
    ->where(['locale' => 'th|en'])
    ->middleware('setlocale')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::view('/bedroom-set', 'bedroom-set')->name('bedroom-set');
        Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::view('/lead', 'lead')->name('lead.create');
        Route::post('/lead', [LeadController::class, 'store'])->name('lead.store');
        Route::view('/thank-you', 'thank-you')->name('thank-you');

        Route::middleware('guest')->group(function (): void {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        });

        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

        Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
            Route::get('/marketing/website-leads', [AdminLeadController::class, 'websiteLeads'])->name('marketing.website-leads');
            Route::get('/marketing/facebook-leads', [AdminLeadController::class, 'facebookLeads'])->name('marketing.facebook-leads');
            Route::get('/marketing/line-leads', [AdminLeadController::class, 'lineLeads'])->name('marketing.line-leads');
            Route::get('/leads/export', [AdminLeadController::class, 'export'])->name('leads.export');
            Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
            Route::patch('/leads/{lead}', [AdminLeadController::class, 'update'])->name('leads.update');
            Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
            Route::post('/leads/{lead}/notes', [AdminLeadController::class, 'storeNote'])->name('leads.notes.store');
            Route::get('/leads/{lead}/quotations/create', [AdminQuotationController::class, 'createFromLead'])->name('leads.quotations.create');
            Route::post('/leads/{lead}/quotations', [AdminQuotationController::class, 'store'])->name('leads.quotations.store');
            Route::get('/quotations', [AdminQuotationController::class, 'index'])->name('quotations.index');
            Route::get('/quotations/create', [AdminQuotationController::class, 'create'])->name('quotations.create');
            Route::post('/quotations', [AdminQuotationController::class, 'store'])->name('quotations.store');
            Route::get('/quotations/{quotation}', [AdminQuotationController::class, 'show'])->name('quotations.show');
            Route::get('/quotations/{quotation}/edit', [AdminQuotationController::class, 'edit'])->name('quotations.edit');
            Route::put('/quotations/{quotation}', [AdminQuotationController::class, 'update'])->name('quotations.update');
            Route::delete('/quotations/{quotation}', [AdminQuotationController::class, 'destroy'])->name('quotations.destroy');
            Route::patch('/quotations/{quotation}/approve', [AdminQuotationController::class, 'approve'])->name('quotations.approve');
            Route::patch('/quotations/{quotation}/status', [AdminQuotationController::class, 'updateStatus'])->name('quotations.status');
            Route::get('/quotations/{quotation}/pdf', [AdminQuotationController::class, 'pdf'])->name('quotations.pdf');
            Route::get('/production', [AdminProductionController::class, 'index'])->name('production.index');
            Route::get('/production/{productionOrder}', [AdminProductionController::class, 'show'])->name('production.show');
            Route::patch('/production/{productionOrder}/status', [AdminProductionController::class, 'updateStatus'])->name('production.status');
            Route::patch('/production/{productionOrder}/craftsmen', [AdminProductionController::class, 'assignCraftsmen'])->name('production.craftsmen');
            Route::get('/installation-schedule', [AdminInstallationScheduleController::class, 'index'])->name('installation.index');
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
            Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
            Route::get('/products/{product}/bom', [AdminProductController::class, 'bom'])->name('products.bom');
            Route::post('/products/{product}/bom', [AdminProductController::class, 'storeBomItem'])->name('products.bom.store');
            Route::delete('/products/{product}/bom/{bomItem}', [AdminProductController::class, 'destroyBomItem'])->name('products.bom.destroy');
            Route::get('/materials', [AdminMaterialController::class, 'index'])->name('materials.index');
            Route::get('/materials/create', [AdminMaterialController::class, 'create'])->name('materials.create');
            Route::post('/materials', [AdminMaterialController::class, 'store'])->name('materials.store');
            Route::get('/materials/{material}/edit', [AdminMaterialController::class, 'edit'])->name('materials.edit');
            Route::put('/materials/{material}', [AdminMaterialController::class, 'update'])->name('materials.update');
            Route::get('/purchase-requests', [AdminPurchaseRequestController::class, 'index'])->name('purchase-requests.index');
            Route::get('/purchase-requests/{purchaseRequest}', [AdminPurchaseRequestController::class, 'show'])->name('purchase-requests.show');
            Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
            Route::post('/inventory/materials/{material}/transactions', [AdminInventoryController::class, 'storeTransaction'])->name('inventory.transactions.store');
            Route::get('/settings/line', [AdminLineSettingController::class, 'edit'])->name('settings.line.edit');
            Route::patch('/settings/line', [AdminLineSettingController::class, 'update'])->name('settings.line.update');
            Route::post('/settings/line/test-notification', [AdminLineSettingController::class, 'testNotification'])->name('settings.line.test-notification');
            Route::get('/settings/line/logs', [AdminLineSettingController::class, 'logs'])->name('settings.line.logs');
            Route::get('/settings/facebook', [AdminFacebookSettingController::class, 'edit'])->name('settings.facebook.edit');
            Route::post('/settings/facebook', [AdminFacebookSettingController::class, 'update'])->name('settings.facebook.update');
            Route::get('/settings/company', [AdminCompanySettingController::class, 'edit'])->name('settings.company.edit');
            Route::patch('/settings/company', [AdminCompanySettingController::class, 'update'])->name('settings.company.update');
            Route::get('/marketing/homepage', [AdminMarketingHomepageController::class, 'index'])->name('marketing.homepage');
            Route::get('/marketing/campaigns', [AdminMarketingCenterController::class, 'campaigns'])->name('marketing.campaigns');
            Route::get('/marketing/analytics', [AdminMarketingCenterController::class, 'analytics'])->name('marketing.analytics');
            Route::post('/marketing/homepage/sections/{section}', [AdminMarketingHomepageController::class, 'updateSection'])->name('marketing.homepage.sections.update');
            Route::post('/marketing/homepage/categories/{category}', [AdminMarketingHomepageController::class, 'updateCategory'])->name('marketing.homepage.categories.update');
            Route::get('/marketing/reviews', [AdminCustomerReviewController::class, 'index'])->name('marketing.reviews.index');
            Route::post('/marketing/reviews', [AdminCustomerReviewController::class, 'store'])->name('marketing.reviews.store');
            Route::patch('/marketing/reviews/{review}', [AdminCustomerReviewController::class, 'update'])->name('marketing.reviews.update');
            Route::delete('/marketing/reviews/{review}', [AdminCustomerReviewController::class, 'destroy'])->name('marketing.reviews.destroy');
            Route::get('/portfolio', [AdminPortfolioController::class, 'index'])->name('portfolio.index');
            Route::post('/portfolio', [AdminPortfolioController::class, 'store'])->name('portfolio.store');
            Route::patch('/portfolio/{portfolioImage}', [AdminPortfolioController::class, 'update'])->name('portfolio.update');
            Route::delete('/portfolio/{portfolioImage}', [AdminPortfolioController::class, 'destroy'])->name('portfolio.destroy');
            Route::resource('suppliers', AdminSupplierController::class)->except('destroy');
            Route::get('/purchase', [AdminPurchaseController::class, 'index'])->name('purchase.index');
            Route::get('/purchase/pr/create', [AdminPurchaseController::class, 'createPr'])->name('purchase.pr.create');
            Route::post('/purchase/pr', [AdminPurchaseController::class, 'storePr'])->name('purchase.pr.store');
            Route::get('/purchase/pr/{purchaseRequisition}', [AdminPurchaseController::class, 'showPr'])->name('purchase.pr.show');
            Route::patch('/purchase/pr/{purchaseRequisition}/status', [AdminPurchaseController::class, 'updatePrStatus'])->name('purchase.pr.status');
            Route::get('/purchase/po/create', [AdminPurchaseController::class, 'createPo'])->name('purchase.po.create');
            Route::post('/purchase/po', [AdminPurchaseController::class, 'storePo'])->name('purchase.po.store');
            Route::get('/purchase/po/{purchaseOrder}', [AdminPurchaseController::class, 'showPo'])->name('purchase.po.show');
            Route::post('/purchase/receive/{purchaseOrderItem}', [AdminPurchaseController::class, 'receive'])->name('purchase.receive');
            Route::post('/purchase/auto-pr/material/{material}', [AdminPurchaseController::class, 'autoPrFromLowStock'])->name('purchase.auto-pr.material');
            Route::post('/purchase/auto-pr/production/{productionOrder}', [AdminPurchaseController::class, 'autoPrFromProduction'])->name('purchase.auto-pr.production');
            Route::get('/purchase/reports/{type}/{format}', [AdminPurchaseController::class, 'report'])->name('purchase.reports.export');
            Route::get('/settings/users-roles', [AdminMarketingCenterController::class, 'usersRoles'])->name('settings.users-roles');
        });
    });


Route::redirect('/{any}', '/th')->where('any', '^(?!(th|en|api|line|facebook|webhooks)$).+');
    
Route::post('/webhooks/line', function (\Illuminate\Http\Request $request) {
    \Log::info('LINE webhook payload', $request->all());
    return response('OK', 200);
});
