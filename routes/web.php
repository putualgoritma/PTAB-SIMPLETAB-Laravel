<?php

//Route::get('member/register', 'MembersController@register');
// Route::resource('member', 'MembersController');

Route::redirect('/', '/login');

Route::redirect('/home', '/admin');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::post('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::post('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');

    Route::resource('users', 'UsersController');




    // keluhan pelanggan
    Route::resource('customers', 'CustomersController');

    Route::delete('customers/destroy', 'CustomersController@massDestroy')->name('customers.massDestroy');

    Route::resource('categories', 'CategoriesController');

    Route::delete('categories/destroy', 'CategoriesController@massDestroy')->name('categories.massDestroy');

    Route::resource('dapertements', 'DapertementsController');

    Route::delete('dapertements/destroy', 'DapertementsController@massDestroy')->name('dapertements.massDestroy');

    Route::resource('staffs', 'StaffsController');

    Route::delete('staffs/destroy', 'StaffsController@massDestroy')->name('staffs.massDestroy');

    // tickets


    Route::resource('tickets', 'TicketsController');

    Route::get('tickets/print/{ticket_id}', 'TicketsController@print')->name('tickets.print');

    Route::get('tickets/print-action/{ticket_id}', 'TicketsController@printAction')->name('tickets.printAction');

    Route::delete('tickets/destroy', 'TicketsController@massDestroy')->name('tickets.massDestroy');

    Route::get('ticket/printservice/{ticket_id}', 'TicketsController@printservice')->name('tickets.printservice');

    Route::get('ticket/printspk/{ticket_id}', 'TicketsController@printspk')->name('tickets.printspk');

    Route::get('ticket/printreport/{ticket_id}', 'TicketsController@printreport')->name('tickets.printreport');

    // action & action staff
    // start surya buat
    Route::get('actions/printservice', 'ActionsController@printservice')->name('actions.printservice');

    Route::get('actions/printspk', 'ActionsController@printspk')->name('actions.printspk');

    Route::get('actions/printreport', 'ActionsController@printreport')->name('actions.printreport');
    // end surya buat

    Route::resource('actions', 'ActionsController', ['only' => ['index', 'store', 'edit', 'update', 'destroy']]);

    Route::get('actions/create/{ticket_id}', 'ActionsController@create')->name('actions.create');

    Route::post('actions/staff', 'ActionsController@staff')->name('actions.staff');

    Route::get('actions/list/{action}', 'ActionsController@list')->name('actions.list');

    Route::delete('actions/destroy', 'ActionsController@massDestroy')->name('actions.massDestroy');

    Route::get('actions/staff/{action}', 'ActionsController@actionStaff')->name('actions.actionStaff');

    Route::get('actions/staff/create/{action}', 'ActionsController@actionStaffCreate')->name('actions.actionStaffCreate');

    Route::post('actions/staff/store/', 'ActionsController@actionStaffStore')->name('actions.actionStaffStore');

    Route::get('actions/staff/{action}/edit', 'ActionsController@actionStaffEdit')->name('actions.actionStaffEdit');

    Route::put('actions/staff/update', 'ActionsController@actionStaffUpdate')->name('actions.actionStaffUpdate');

    Route::put('actions/staff/update', 'ActionsController@actionStaffUpdate')->name('actions.actionStaffUpdate');

    Route::delete('users/staff/delete/{action}/{staff}', 'ActionsController@actionStaffDestroy')->name('actions.actionStaffDestroy');

    //customer request
    Route::resource('customerrequests', 'CustomerRequestController');

    //ctm request
    Route::resource('ctmrequests', 'CtmRequestController');

    //pbk
    Route::resource('pbks', 'CtmPbkController');
    Route::get('pbk/status/{id}', 'CtmPbkController@editStatus')->name('pbks.status');
    Route::put('pbk/update', 'CtmPbkController@updateStatus')->name('pbks.statusUpdate');

    //test
    Route::resource('test-customers', 'TestController');
    Route::get('test-get', 'TestController@getTest');

    Route::resource('subdapertements', 'SubdapertementsController');

    Route::delete('subdapertements/destroy', 'SubdapertementsController@massDestroy')->name('subdapertements.massDestroy');

    Route::get('get-subdapertement', 'StaffsController@getSubdapertement')->name('staffs.subdepartment');

    Route::get('reports/reportssubhumas', 'ReportsController@reportSubHumas')->name('report.subhumas');

    Route::post('reports/reportssubhumas/proses', 'ReportsController@reportSubHumasProses')->name('report.subhumasproses');

    Route::get('reports/reportssubdistribusi', 'ReportsController@reportSubDistribusi')->name('report.subdistribusi');

    Route::post('reports/reportssubdistribusi/proses', 'ReportsController@reportSubDistribusiProses')->name('report.subdistribusiproses');

    Route::get('get-staff', 'StaffsController@getStaff')->name('staffs.staff');

    Route::get('segel-meter', 'SegelMeterController@index')->name('segelmeter.index');

    Route::get('segel-meter/show/{id}', 'SegelMeterController@show')->name('segelmeter.show');

    Route::get('segel-meter/sppprint/{id}', 'SegelMeterController@sppPrint')->name('segelmeter.sppprint');

    Route::get('segel-deligate', 'SegelMeterController@deligate')->name('segelmeter.deligate');

    Route::get('file-upload', 'PdfUploadController@fileUpload')->name('file.upload');

    Route::get('file-uploadCreate', 'PdfUploadController@fileUploadCreate')->name('file.create');

    Route::post('file-upload', 'PdfUploadController@fileUploadPost')->name('file.upload.post');

    Route::delete('file-upload/delete/{audited}', 'PdfUploadController@fileUploadDestroy')->name('file.upload.destroy');

    Route::resource('lock', 'LockController');

    Route::get('lock/staff/{action}', 'LockController@lockactionStaff')->name('lock.actionStaff');

    Route::get('lock/staff/create/{action}', 'LockController@lockactionStaffCreate')->name('lock.actionStaffCreate');

    Route::post('lock/staff/store/', 'LockController@lockactionStaffStore')->name('lock.actionStaffStore');

    Route::delete('lock/staff/delete/{action}/{staff}', 'LockController@lockactionStaffDestroy')->name('lock.actionStaffDestroy');

    Route::get('lock/list/{action}', 'LockController@list')->name('lock.list');

    Route::get('lock/create/{lock_id}', 'LockController@actioncreate')->name('lock.lockcreate');

    Route::post('lock/action/store/', 'LockController@lockstore')->name('lock.lockstore');

    Route::delete('lock/action/delete/{action}', 'LockController@lockactionDestroy')->name('lock.actiondestroy');

    Route::get('lock/action/{action}/view', 'LockController@lockView')->name('lock.LockView');

    Route::get('lock/sppprint/{id}', 'LockController@sppPrint')->name('lock.sppprint');




    //dibuat pada 2022-09-07
    Route::resource('lockT', 'LockTController');

    Route::get('lockT/staff/{action}', 'LockTController@lockactionStaff')->name('lockT.actionStaff');

    Route::get('lockT/staff/create/{action}', 'LockTController@lockactionStaffCreate')->name('lockT.actionStaffCreate');

    Route::post('lockT/staff/store/', 'LockTController@lockactionStaffStore')->name('lockT.actionStaffStore');

    Route::delete('lockT/staff/delete/{action}/{staff}', 'LockTController@lockactionStaffDestroy')->name('lockT.actionStaffDestroy');

    Route::get('lockT/list/{action}', 'LockTController@list')->name('lockT.list');

    Route::get('lockT/create/{lock_id}', 'LockTController@actioncreate')->name('lockT.lockcreate');

    Route::post('lockT/action/store/', 'LockTController@lockstore')->name('lockT.lockstore');

    Route::delete('lockT/action/delete/{action}', 'LockTController@lockactionDestroy')->name('lockT.actiondestroy');

    Route::get('lockT/action/{action}/view', 'LockTController@lockView')->name('lockT.LockView');

    Route::get('lockT/sppprint/{id}', 'LockTController@sppPrint')->name('lockT.sppprint');
    //dibuat pada 2022-09-07

    Route::get('spp/Sppall', 'SppController@index')->name('spp.index');

    Route::get('spp/sppprintall', 'SppController@sppPrintAll')->name('spp.sppprintall');


    Route::get('customwa', 'CustomWaController@index')->name('customwa.index');
    Route::post('customwa', 'CustomWaController@import')->name('customwa.import');
    // history Wa
    Route::get('test', 'TestWaController@index')->name('test.index');
    Route::get('historywa', 'HistoryWaController@index')->name('historywa.index');
    Route::delete('historywa/destroy/{id}', 'HistoryWaController@destroy')->name('historywa.destroy');
    Route::delete('wablast/destroy', 'WhatsappBlastController@massDestroy')->name('wablast.massDestroy');
    Route::delete('wablast/destroy/{id}', 'WhatsappBlastController@destroy')->name('wablast.destroy');

    Route::get('historywa/deleteall', 'HistoryWaController@deleteAll')->name('historywa.deleteall');
    Route::get('historywa/deletefilter', 'HistoryWaController@deleteFilter')->name('historywa.deletefilter');

    Route::get('checkphone', 'CheckPhoneController@index')->name('checkphone.index');

    // device Wa
    Route::get('devicewa', 'DeviceWaController@index')->name('devicewa.index');
    Route::get('devicewa/disconect', 'DeviceWaController@disconect')->name('devicewa.disconect');
    route::get('devicewa/create', 'DeviceWaController@create')->name('devicewa.create');
    route::post('devicewa/store', 'DeviceWaController@store')->name('devicewa.store');

    //whatsapp
    Route::get('wablast', 'WhatsappBlastController@index')->name('wablast.index');
    Route::post('wablast', 'WhatsappBlastController@templateP')->name('wablast.templateP');

    //perbaikan
    Route::get('wablast/createmessageperview', 'WhatsappBlastController@createMessagePerView')->name('wablast.createmessageperview');
    Route::post('wablast/storemessageper', 'WhatsappBlastController@storeMessagePer')->name('wablast.storemessageper');
    Route::get('wablast/area', 'WhatsappBlastController@area')->name('wablast.area');
    Route::post('wablast/templateper', 'WhatsappBlastController@templatePer')->name('wablast.templateper');
    Route::post('wablast/createmessageperp', 'WhatsappBlastController@createmessagePerP')->name('wablast.createmessageperp');
    Route::post('wablast/createmessageper', 'WhatsappBlastController@createmessagePer')->name('wablast.createmessageper');

    //tunggakan
    Route::get('wablast/categoryt', 'WhatsappBlastController@categoryT')->name('wablast.categoryt');
    Route::post('wablast/templatet', 'WhatsappBlastController@templateT')->name('wablast.templatet');
    Route::post('wablast/createmessagetp', 'WhatsappBlastController@createmessageTP')->name('wablast.createmessagetp');
    Route::get('wablast/createmessaget', 'WhatsappBlastController@createmessageT')->name('wablast.createmessaget');
    Route::post('wablast/storemessaget', 'WhatsappBlastController@storeMessageT')->name('wablast.storemessaget');


    Route::post('wablast/create', 'WhatsappBlastController@create')->name('wablast.create');
    Route::get('wablast/creater', 'WhatsappBlastController@creater')->name('wablast.creater');
    Route::post('wablast/store', 'WhatsappBlastController@store')->name('wablast.store');

    Route::post('wablast/template1', 'WhatsappBlastController@template1')->name('wablast.template1');
    Route::get('wablast/template2', 'WhatsappBlastController@template2')->name('wablast.template2');
    Route::get('wablast/template3', 'WhatsappBlastController@template3')->name('wablast.template3');
    Route::post('wablast/template4p', 'WhatsappBlastController@template4P')->name('wablast.template4P');
    Route::get('wablast/template4', 'WhatsappBlastController@template4')->name('wablast.template4');
    Route::post('wablast/template5', 'WhatsappBlastController@template5')->name('wablast.template5');

    Route::post('wablastperbaikan', 'WhatsappBlastController@templateperbaikan')->name('wablast.templateperbaikan');
    Route::post('wablast/createmessage1p', 'WhatsappBlastController@createMessage1P')->name('wablast.createmessage1P');
    Route::get('wablast/createmessage1', 'WhatsappBlastController@createMessage1')->name('wablast.createmessage1');
    Route::post('wablast/storetemplate', 'WhatsappBlastController@storeTemplate')->name('wablast.storetemplate');
    Route::get('wablast/history', 'WhatsappBlastController@history')->name('wablast.history');

    Route::get('suratsegel', 'SuratSegelController@index')->name('suratsegel.index');
    Route::get('suratsegel/create/{id}', 'SuratSegelController@create')->name('suratsegel.create');
    Route::post('suratsegel/suratPdf', 'SuratSegelController@suratPdf')->name('suratsegel.suratPdf');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'], function () {
    Route::post('wablast/callback', 'WhatsappBlastController@callback')->name('wablast.callback');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin\whatsapp', 'middleware' => ['auth']], function () {
    Route::delete('categoryWA/destroy', 'CategoryController@massDestroy')->name('categoryWA.massDestroy');
    Route::resource('categoryWA', 'CategoryController');
    Route::delete('WaTemplate/destroy', 'WaTemplateController@massDestroy')->name('WaTemplate.massDestroy');
    Route::resource('WaTemplate', 'WaTemplateController');
    //tunggakan
    // Route::get('wa/tunggakan', 'TunggakanController@index')->name('tunggakan.index');
    // Route::post('wa/tunggakan/template', 'TunggakanController@template')->name('tunggakan.template');
    // Route::post('wa/tunggakan/create', 'TunggakanController@create')->name('tunggakan.create');
    // Route::post('wa/tunggakan/store', 'TunggakanController@store')->name('tunggakan.store');
});