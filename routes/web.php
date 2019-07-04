<?php
use Illuminate\Http\Request;
use Carbon\Carbon;
//$currentLang = \App\Language::getCurrentLang();
Route::get('/install', 'InstallController@index')->name('install.create');
Route::post('/install', 'InstallController@store')->name('install.store');

Route::get('conferences/{acronym}/{edition}/send/mail', 'ComiteController@sendMail');
Route::get('/test', function()
{
    Artisan::call('config:cache');
});


Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/login', 'Auth\LoginController@showLogForm')->name('login');
Route::post('/register', 'Auth\RegisterController@registerUser');
Route::get('/settings', 'Auth\SettingsController@settings')->name('users.settings');;
Route::post('/settings', 'Auth\SettingsController@store')->name('users.settings.store');;



Route::get('/user/activation/{token}', 'Auth\RegisterController@userActivation');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Auth::routes();


//Lang routes
Route::post('/changeLanguage/{lang}', function(Request $request,$lang){
        $request->session()->forget('lang');
        $request->session()->put('lang', $lang);
        Carbon::setLocale(LC_TIME,$lang);
        return back();
});

//find routes
Route::post('/find', 'SearchController@find')->name('find');

//not found route
Route::get('/error/{code}', function($code){
       return view('errors.notfound',compact('code'));
})->name('notfound');


Route::get('/', 'HomeController@index')->name('home');

//Download routes
Route::get('/download/{model}/{confid}/{type}', 'DownloadController@excel')->name('conferences.download.excel');
Route::get('/download/zip', 'DownloadController@zip')->name('conferences.download.zip');
//Admin routes

Route::prefix('/admin')->group(function(){
    Route::get('/conferences', 'Admin\ConferencesController@index')->name('admin.dashboard.conferences');;
    Route::post('/conferences/approve', 'Admin\ConferencesController@approve')->name('admin.conferences.approve');
    Route::post('/conferences/delete', 'Admin\ConferencesController@delete')->name('admin.conferences.delete');
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::post('/logout', 'Admin\AdminsController@logout')->name('admin.logout');
    Route::get('/', 'Admin\AdminsController@index')->name('admin.dashboard');
    
});

Route::post('/conferences/create/{conference}', 'ConferencesController@createFrom')->name('conferences.create-from');
Route::post('/conferences/{conference}/delete', 'ConferencesController@destroy')->name('conferences.delete');


//Conferences routes
Route::get('/conference/activation/{token}', 'ConferencesController@confActivation')->name('conferences.activate');
Route::get('/conferences/create', 'ConferencesController@create')->name('conferences.create');
Route::post('/conferences/create', 'ConferencesController@store')->name('conferences.store');
Route::get('/conferences/{acronym}/{edition}', 'ConferencesController@show')->name('conferences.show');
Route::get('/conferences/{acronym}/{edition}/edit', 'ConferencesController@edit')->name('conferences.edit');
Route::post('/conferences/{acronym}/{edition}/edit', 'ConferencesController@update')->name('conferences.update');
Route::get('/conferences/{acronym}/{edition}/submission/edit', 'ConferencesController@editSubmission')->name('conferences.submission.edit');
Route::post('/conferences/{acronym}/{edition}/submission/edit', 'ConferencesController@updateSubmission')->name('conferences.submission.update');




//Messages routes

Route::get('/conferences/{acronym}/{edition}/messages/all', 'MessagetempsController@getData')->name('conferences.messages.indexall');
Route::get('/conferences/{acronym}/{edition}/messages', 'MessagetempsController@index')->name('conferences.messages.index');
Route::get('/conferences/{acronym}/{edition}/messages/create', 'MessagetempsController@create')->name('conferences.messages.create');
Route::post('/conferences/{acronym}/{edition}/messages/create', 'MessagetempsController@store')->name('conferences.messages.store');
Route::get('/conferences/{acronym}/{edition}/messages/{messagetemp}/showbody', function($acronym,$edition,\App\Messagetemp $messagetemp){
                                                                                  return $messagetemp;
                                                                              })->name('conferences.messages.showBody');
Route::get('/conferences/{acronym}/{edition}/messages/{messagetemp}/edit', 'MessagetempsController@edit')->name('conferences.messages.edit');
Route::post('/conferences/{acronym}/{edition}/messages/{messagetemp}/edit', 'MessagetempsController@update')->name('conferences.messages.update');
Route::post('/conferences/{acronym}/{edition}/messages/{messagetemp}/delete', 'MessagetempsController@destroy')->name('conferences.messages.delete');

//Topics routes

Route::get('/conferences/{acronym}/{edition}/topics/all', 'TopicsController@getData')->name('conferences.topics.indexall');
Route::get('/conferences/{acronym}/{edition}/topics', 'TopicsController@index')->name('conferences.topics.index');
Route::post('/conferences/{acronym}/{edition}/topics/create', 'TopicsController@store')->name('conferences.topics.store');
Route::get('/conferences/{acronym}/{edition}/topics/create', 'TopicsController@create')->name('conferences.topics.create');
Route::get('/conferences/{acronym}/{edition}/topics/{topic}/edit', 'TopicsController@edit')->name('conferences.topics.edit');
Route::post('/conferences/{acronym}/{edition}/topics/{topic}/edit', 'TopicsController@update')->name('conferences.topics.update');
Route::post('/conferences/{acronym}/{edition}/topics/{topic}/delete', 'TopicsController@destroy')->name('conferences.topics.delelte');



//Papers routes

Route::get('/conferences/{acronym}/{edition}/papers/all', 'PapersController@getData')->name('conferences.papers.indexall');
Route::get('/conferences/{acronym}/{edition}/papers', 'PapersController@index')->name('conferences.papers.index');
Route::get('/conferences/{acronym}/{edition}/papers/saveCamready', 'PapersController@saveCamReady')->name('conferences.papers.saveCamReady');
Route::get('/conferences/{acronym}/{edition}/papers/create', 'PapersController@create')->name('conferences.papers.create');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}', 'PapersController@show')->name('conferences.papers.show');
Route::post('/conferences/{acronym}/{edition}/papers/create', 'PapersController@store')->name('conferences.papers.store');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}/edit', 'PapersController@edit')->name('conferences.papers.edit');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/edit', 'PapersController@update')->name('conferences.papers.update');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}/camReady', 'PapersController@uploadCr')->name('conferences.papers.uploadCr');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/camReady', 'PapersController@storeCr')->name('conferences.papers.storeCr');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/delete', 'PapersController@destroy')->name('conferences.papers.delete');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}/download/{CR?}', 'PapersController@download')->name('conferences.papers.download');
Route::get('/conferences/{acronym}/{edition}/papers/keywords/{keys}', 'PapersController@keywords')->name('conferences.papers.keys');
Route::get('/conferences/{acronym}/{edition}/papers/assign/{paper}', 'PapersController@assign')->name('conferences.papers.assign');
Route::post('/conferences/{acronym}/{edition}/papers/assign/{paper}', 'PapersController@storeAssign')->name('conferences.papers.storeAssign');

//PaperQuestions routes

Route::get('/conferences/{acronym}/{edition}/pquestions/all', 'PaperquestionsController@getData')->name('conferences.pquestions.indexall');
Route::get('/conferences/{acronym}/{edition}/pquestions', 'PaperquestionsController@index')->name('conferences.pquestions.index');
Route::get('/conferences/{acronym}/{edition}/pquestions/create', 'PaperquestionsController@create')->name('conferences.pquestions.create');
Route::post('/conferences/{acronym}/{edition}/pquestions/create', 'PaperquestionsController@store')->name('conferences.pquestions.store');
Route::get('/conferences/{acronym}/{edition}/pquestions/{paperquestion}/edit', 'PaperquestionsController@edit')->name('conferences.pquestions.edit');
Route::post('/conferences/{acronym}/{edition}/pquestions/{paperquestion}/edit', 'PaperquestionsController@update')->name('conferences.pquestions.update');
Route::post('/conferences/{acronym}/{edition}/pquestions/{paperquestion}/delete', 'PaperquestionsController@destroy')->name('conferences.pquestions.delete');

Route::post('/conferences/{acronym}/{edition}/pquestions/{paperquestion}/edit/{choice}/delete', 'PaperquestionsController@deleteChoice')->name('conferences.choices.delete');

//ReviewQuestions routes

Route::get('/conferences/{acronym}/{edition}/rquestions/all', 'ReviewquestionsController@getData')->name('conferences.rquestions.indexall');
Route::get('/conferences/{acronym}/{edition}/rquestions', 'ReviewquestionsController@index')->name('conferences.rquestions.index');
Route::get('/conferences/{acronym}/{edition}/rquestions/create', 'ReviewquestionsController@create')->name('conferences.rquestions.create');
Route::post('/conferences/{acronym}/{edition}/rquestions/create', 'ReviewquestionsController@store')->name('conferences.rquestions.store');
Route::get('/conferences/{acronym}/{edition}/rquestions/{reviewquestion}/edit', 'ReviewquestionsController@edit')->name('conferences.rquestions.edit');
Route::post('/conferences/{acronym}/{edition}/rquestions/{reviewquestion}/edit', 'ReviewquestionsController@update')->name('conferences.rquestions.update');
Route::post('/conferences/{acronym}/{edition}/rquestions/{reviewquestion}/delete', 'ReviewquestionsController@destroy')->name('conferences.rquestions.delete');

Route::post('/conferences/{acronym}/{edition}/rquestions/{reviewquestion}/edit/{choice}/delete', 'ReviewquestionsController@deleteChoice')->name('conferences.rchoices.delete');

//Criteria routes

Route::get('/conferences/{acronym}/{edition}/criterias/all', 'CriteriasController@getData')->name('conferences.criterias.indexall');
Route::get('/conferences/{acronym}/{edition}/criterias', 'CriteriasController@index')->name('conferences.criterias.index');
Route::post('/conferences/{acronym}/{edition}/criterias/create', 'CriteriasController@store')->name('conferences.criterias.store');
Route::get('/conferences/{acronym}/{edition}/criterias/create', 'CriteriasController@create')->name('conferences.criterias.create');
Route::get('/conferences/{acronym}/{edition}/criterias/{criteria}/edit', 'CriteriasController@edit')->name('conferences.criterias.edit');
Route::post('/conferences/{acronym}/{edition}/criterias/{criteria}/edit', 'CriteriasController@update')->name('conferences.criterias.update');
Route::post('/conferences/{acronym}/{edition}/criterias/{criteria}/delete', 'CriteriasController@destroy')->name('conferences.criterias.delelte');

//paper status routes

Route::get('/conferences/{acronym}/{edition}/paperstatus/all', 'PaperstatusController@getData')->name('conferences.paperstatus.indexall');
Route::get('/conferences/{acronym}/{edition}/paperstatus', 'PaperstatusController@index')->name('conferences.paperstatus.index');
Route::post('/conferences/{acronym}/{edition}/paperstatus/create', 'PaperstatusController@store')->name('conferences.paperstatus.store');
Route::get('/conferences/{acronym}/{edition}/paperstatus/create', 'PaperstatusController@create')->name('conferences.paperstatus.create');
Route::get('/conferences/{acronym}/{edition}/paperstatus/{pstatus}/edit', 'PaperstatusController@edit')->name('conferences.paperstatus.edit');
Route::post('/conferences/{acronym}/{edition}/paperstatus/{pstatus}/edit', 'PaperstatusController@update')->name('conferences.paperstatus.update');
Route::post('/conferences/{acronym}/{edition}/paperstatus/{pstatus}/delete', 'PaperstatusController@destroy')->name('conferences.paperstatus.delelte');
Route::get('/conferences/{acronym}/{edition}/pstatus/{ps}/showbody', function($acronym,$edition,\App\Paperstatus $ps){
                                                                                  return $ps;
                                                                              })->name('conferences.pstatus.showBody');

//Authors routes
Route::get('/conferences/{acronym}/{edition}/authors/mypapers/all', 'AuthorsController@getPapersAuthors')->name('conferences.authors.papers.indexall');
Route::get('/conferences/{acronym}/{edition}/authors/mypapers', 'AuthorsController@myPapers')->name('conferences.authors.mypapers');
Route::get('/conferences/{acronym}/{edition}/authors/all', 'AuthorsController@getData')->name('conferences.authors.indexall');
Route::get('/conferences/{acronym}/{edition}/authors', 'AuthorsController@index')->name('conferences.authors.index');
Route::get('/conferences/{acronym}/{edition}/authors/{author}/papers/', 'AuthorsController@papersAuthor')->name('conferences.authors.papers');

//Comite routes
Route::get('/conferences/{acronym}/{edition}/committee/all', 'ComiteController@getData')->name('conferences.comite.indexall');
Route::get('/conferences/{acronym}/{edition}/committee', 'ComiteController@index')->name('conferences.comite.index');
Route::post('/conferences/{acronym}/{edition}/committee/send', 'ComiteController@send')->name('conferences.comite.send');
Route::get('/conferences/{acronym}/{edition}/committee/notifyAuthors', 'ComiteController@notifyAuthors')->name('conferences.comite.notifyAuthors');
Route::post('/conferences/{acronym}/{edition}/committee/add', 'ComiteController@add')->name('conferences.comite.add');
Route::post('/conferences/{acronym}/{edition}/committee/accept/{role}', 'ComiteController@accept')->name('conferences.comite.accept');
Route::post('/conferences/{acronym}/{edition}/committee/decline/{role}', 'ComiteController@decline')->name('conferences.comite.decline');
Route::get('/conferences/{acronym}/{edition}/committee/assign/{user}', 'ComiteController@assign')->name('conferences.comite.assign');
Route::post('/conferences/{acronym}/{edition}/committee/assign/{user}', 'ComiteController@storeAssign')->name('conferences.comite.storeAssign');
Route::get('/conferences/{acronym}/{edition}/chooseTopics', 'HomeController@choose')->name('conferences.comite.choose');
Route::post('/conferences/{acronym}/{edition}/chooseTopics', 'HomeController@storeChoose')->name('conferences.comite.storeChoose');
Route::post('/editTopics/{acronym}/{edition}', 'HomeController@storeEdit')->name('conferences.comite.storeEdit');
Route::get('/conferences/{acronym}/{edition}/reviewer/mypapers/all', 'ComiteController@getPapersRevs')->name('conferences.comite.mypapers.indexall');
Route::get('/conferences/{acronym}/{edition}/reviewer/mypapers', 'ComiteController@myPapers')->name('conferences.comite.mypapers');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}/review', 'ComiteController@review')->name('conferences.comite.review');
Route::get('/conferences/{acronym}/{edition}/papers/{paper}/{user}/review/show', 'ComiteController@showReview')->name('conferences.comite.showReview');
Route::get('/conferences/{acronym}/{edition}/papers/review/{review}/json', 'ComiteController@showReviewJson')->name('conferences.comite.showReviewJson');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/review', 'ComiteController@storeReview')->name('conferences.comite.storeReview');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/review/edit', 'ComiteController@updateReview')->name('conferences.comite.updateReview');
Route::get('/conferences/{acronym}/{edition}/assignstatus/all', 'ComiteController@getAssignStatus')->name('conferences.comite.assignStatus.all');
Route::get('/conferences/{acronym}/{edition}/assignstatus', 'ComiteController@assignStatus')->name('conferences.comite.assignStatus');
Route::post('/conferences/{acronym}/{edition}/assignstatus', 'ComiteController@storeAssignStatus')->name('conferences.comite.storeAssignStatus');
Route::post('/conferences/{acronym}/{edition}/papers/{paper}/{user}/reviewer/delete', 'ComiteController@deleteReviewer')->name('conferences.comite.deleteReviewer');
Route::get('/conferences/{acronym}/{edition}/rate', 'ComiteController@rate')->name('conferences.comite.rate');
Route::post('/conferences/{acronym}/{edition}/rate/store', 'ComiteController@storeRate')->name('conferences.comite.storeRate');
Route::get('/conferences/{acronym}/{edition}/rate/show', 'ComiteController@showRate')->name('conferences.comite.showRate');

//Slots routes

Route::get('/conferences/{acronym}/{edition}/program/doc', 'SlotsController@docProgram')->name('conferences.slots.docProgram');
Route::get('/conferences/{acronym}/{edition}/program/', 'SlotsController@index')->name('conferences.slots.index');
Route::post('/conferences/{acronym}/{edition}/program/', 'SlotsController@commit')->name('conferences.slots.commit');
Route::get('/conferences/{acronym}/{edition}/program/preview', 'SlotsController@preview')->name('conferences.slots.preview');
Route::get('/conferences/{acronym}/{edition}/slots/{slot}', 'SlotsController@jsonSlot')->name('conferences.slots.json');
Route::post('/conferences/{acronym}/{edition}/slots/create', 'SlotsController@store')->name('conferences.slots.store');
Route::post('/conferences/{acronym}/{edition}/slots/update', 'SlotsController@update')->name('conferences.slots.update');
Route::post('/conferences/{acronym}/{edition}/slots/{slot}/delete', 'SlotsController@delete')->name('conferences.slots.delete');

//Sessions routes
Route::get('/conferences/{acronym}/{edition}/sessions/{session}', 'SessionsController@jsonSession')->name('conferences.sessions.json');
Route::post('/conferences/{acronym}/{edition}/sessions/create', 'SessionsController@store')->name('conferences.sessions.store');
Route::post('/conferences/{acronym}/{edition}/sessions/{session}/papers', 'SessionsController@assignPapers')->name('conferences.sessions.papers');
Route::post('/conferences/{acronym}/{edition}/sessions/{session}/papers/update', 'SessionsController@updatePapers')->name('conferences.sessions.updatepapers');
Route::post('/conferences/{acronym}/{edition}/sessions/update', 'SessionsController@update')->name('conferences.sessions.update');
Route::post('/conferences/{acronym}/{edition}/sessions/{session}/delete', 'SessionsController@delete')->name('conferences.sessions.delete');
