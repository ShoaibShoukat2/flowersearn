<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\User\UserController;


Route::get('/ref/{ref}', function (Request $request, $ref) {
    $ref = trim((string) $ref);
    if ($ref !== '') {
        $request->session()->put('ref', $ref);
    }

    // Keep the code in the URL so signup still works if the session cookie is dropped
    // (WhatsApp/in-app browsers, www vs non-www, etc.)
    return redirect('/user/register' . ($ref !== '' ? ('?ref=' . urlencode($ref)) : ''));
})->name('referral.link');


// Route::get('/user/pending', [UserController::class, 'pending'])->name('user.pending');



Route::middleware('auth')->get('/about', function () {
    return view('templates.basic.sections.about');
})->name('about');

Route::get('/user/wallet', function () {
    return view('templates.basic.user.wallet');
})->name('user.wallet');

Route::get('/rules', function () {
    return view('templates.basic.user.rules');
});

Route::get('/feedback', function () {
    return view('templates.basic.user.feedback');
});


Route::get('/user/guide', function () {
    return view('templates.basic.user.guide');
});


Route::get('/clear-caches', function () {
    $output = new \Symfony\Component\Console\Output\BufferedOutput;
    
    \Illuminate\Support\Facades\Artisan::call('cache:clear', [], $output);
    echo "Cache cleared<br>";
    
    \Illuminate\Support\Facades\Artisan::call('route:clear', [], $output);
    echo "Routes cleared<br>";
    
    \Illuminate\Support\Facades\Artisan::call('config:clear', [], $output);
    echo "Config cleared<br>";
    
    \Illuminate\Support\Facades\Artisan::call('view:clear', [], $output);
    echo "Views cleared<br>";
    
    echo "<strong>All done! Remove this route for security.</strong>";
});


Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->group(function () {
    Route::get('/', 'supportTicket')->name('ticket');
    Route::get('/new', 'openSupportTicket')->name('ticket.open');
    Route::post('/create', 'storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'replyTicket')->name('ticket.reply');
    Route::post('/close/{ticket}', 'closeTicket')->name('ticket.close');
    Route::get('/download/{ticket}', 'ticketDownload')->name('ticket.download');
});


Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('company-policy/{id}/{slug}', 'SiteController@policy')->name('links');

    Route::get('plans', 'SiteController@plans')->name('plans');

    Route::get('blog', 'SiteController@blog')->name('blog');
    Route::get('blog-details/{id}', 'SiteController@blogDetail')->name('blogDetail');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});


