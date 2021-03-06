<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('dummyEntry', function () {
    return view('dummyEntry');
});


Route::group(['middleware' => ['normal']], function () {

    Route::get('test/{id}', function ($id) {
        DB::enableQueryLog();
        $projectIns = \App\Models\Project::getIns($id);
        $projectIns->negotiations;
        $projectIns->negotiations;
//        \App\Logic\DocumentHandler::getHyperDocList($projectIns);
        echo "<pre>"; var_dump(DB::getQueryLog());
    })->name('test');

    Route::get('role/list', 'RoleController@listPage')->name(ROUTE_NAME_ROLE_LIST);
    Route::post('role/remove', 'RoleController@remove')->name(ROUTE_NAME_ROLE_REMOVE);
    Route::post('role/add', 'RoleController@add')->name(ROUTE_NAME_ROLE_ADD);
    Route::post('role/select', 'RoleController@select')->name(ROUTE_NAME_ROLE_SELECT);

    Route::get('project/apply', 'ProjectController@apply')->name(ROUTE_NAME_PROJECT_APPLY);
    Route::get('project/display/{id}', 'ProjectController@display')->name(ROUTE_NAME_PROJECT_DISPLAY);
    Route::get('project/list', 'ProjectController@listPage')->name(ROUTE_NAME_PROJECT_LIST);
    Route::post('project/remove', 'ProjectController@remove')->name('ProjectRemove');
    Route::post('project/create', 'ProjectController@create')->name(ROUTE_NAME_PROJECT_CREATE);

    Route::post('stage/selectmode', 'StageController@selectMode')->name(ROUTE_NAME_STAGE_SELECT_MODE);
    Route::post('stage/finishrecord', 'StageController@finishRecord')->name(ROUTE_NAME_STAGE_FINISH_RECORD);
    Route::post('stage/summarize', 'StageController@summarize')->name(ROUTE_NAME_STAGE_SUMMARIZE);
    Route::post('stage/approve', 'StageController@approve')->name(ROUTE_NAME_STAGE_APPROVE);
    Route::post('stage/complete', 'StageController@complete')->name(ROUTE_NAME_STAGE_COMPLETE);
    Route::post('stage/assignComplete', 'StageController@assignComplete')->name('StageAssignComplete');
    Route::post('stage/pretrial', 'StageController@pretrial')->name('StagePretrial');

    Route::post('memberDept/add', 'MemberDepartmentController@add')->name('MemberDepartmentAdd');
    Route::post('memberDept/remove', 'MemberDepartmentController@remove')->name('MemberDepartmentRemove');

    Route::post('assignmaker/add', 'AssignMakerController@add')->name(ROUTE_NAME_ASSIGN_MAKER_ADD);
    Route::post('assignmaker/remove', 'AssignMakerController@remove')->name(ROUTE_NAME_ASSIGN_MAKER_REMOVE);

    Route::get('score/edittemplate/{id}', 'ScoreController@editTemplate')->name(ROUTE_NAME_SCORE_EDIT_TEMPLATE);
    Route::post('score/selecttemplate', 'ScoreController@selectTemplate')->name(ROUTE_NAME_SCORE_SELECT_TEMPLATE);
    Route::post('score/commititems', 'ScoreController@commitItems')->name(ROUTE_NAME_SCORE_COMMIT_ITEMS);
    Route::get('score/page/{id}', 'ScoreController@scorePage')->name(ROUTE_NAME_SCORE_PAGE);
    Route::post('score/submit', 'ScoreController@submitScore')->name(ROUTE_NAME_SCORE_SUBMIT_SCORE);
    Route::get('score/overview/{id}', 'ScoreController@overview')->name(ROUTE_NAME_SCORE_OVERVIEW);

    Route::post('vendor/add', 'VendorController@add')->name(ROUTE_NAME_VENDOR_ADD);
    Route::post('vendor/remove', 'VendorController@remove')->name(ROUTE_NAME_VENDOR_REMOVE);

    Route::post('negotiation/add', 'PriceNegotiationController@add')->name(ROUTE_NAME_NEGOTIATION_ADD);
    Route::get('negotiation/show/{id}', 'PriceNegotiationController@showInPage');

    Route::post('duediligence/addrequest', 'DueDiligenceController@addRequest')->name(ROUTE_NAME_DUE_DILIGENCE_ADD_REQUEST);
    Route::post('duediligence/removerequest', 'DueDiligenceController@removeRequest')->name(ROUTE_NAME_DUE_DILIGENCE_REMOVE_REQUEST);
    Route::post('duediligence/answer', 'DueDiligenceController@answer')->name(ROUTE_NAME_DUE_DILIGENCE_ANSWER);

    Route::get('hyper/doc/due/diligence/{id}', 'HyperDocController@dueDiligence');
    Route::get('pass/sign/show/in/page/{id}', 'HyperDocController@passSign');

    Route::get('document/display/{id}/{name}', 'DocumentController@display')->name(ROUTE_NAME_DOCUMENT_DISPLAY);
    Route::post('document/upload', 'DocumentController@upload')->name(ROUTE_NAME_DOCUMENT_UPLOAD);

    Route::post('conversation/add', 'ConversationController@add')->name(ROUTE_NAME_CONVERSATION_ADD);

    Route::get('review/apply/{id?}', 'ReviewController@apply')->name(ROUTE_NAME_REVIEW_APPLY);
    Route::get('review/display/{id}', 'ReviewController@display')->name(ROUTE_NAME_REVIEW_DISPLAY);
    Route::post('review/edit', 'ReviewController@edit')->name(ROUTE_NAME_REVIEW_EDIT);
    Route::post('review/remove', 'ReviewController@remove')->name('ReviewRemove');
    Route::get('review/list', 'ReviewController@listAll')->name('ReviewMeetingList');

    Route::post('review/stage/complete', 'ReviewStageController@complete')->name(ROUTE_NAME_REVIEW_STAGE_COMPLETE);
    Route::post('review/stage/approve', 'ReviewStageController@approve')->name(ROUTE_NAME_REVIEW_STAGE_APPROVE);
    Route::post('review/stage/decideMode', 'ReviewStageController@decideMode')->name('ReviewStageDecideMode');

    Route::post('topic/addproject', 'TopicController@addProject')->name(ROUTE_NAME_TOPIC_ADD_PROJECT);
    Route::post('topic/addputrecord', 'TopicController@addPutRecord')->name(ROUTE_NAME_TOPIC_ADD_PUT_RECORD);
    Route::post('topic/remove', 'TopicController@remove')->name(ROUTE_NAME_TOPIC_REMOVE);

    Route::post('participant/edit', 'ReviewParticipantController@edit')->name(ROUTE_NAME_REVIEW_PARTICIPANT_EDIT);

    Route::post('meetingMinutes/add/meta', 'MeetingMinutesController@addMetaInfo')->name('MeetingMinutesAddMeta');
    Route::post('meetingMinutes/add/content', 'MeetingMinutesController@addContent')->name('MeetingMinutesAddContent');
    Route::get('meetingMinutes/reviewMeeting/{id}', 'MeetingMinutesController@reviewMeeting');
    Route::get('meetingMinutes/topic/{id}', 'MeetingMinutesController@topic');

});

Route::group(['middleware' => ['welcome']], function () {

    Route::match(['get', 'post'], 'welcome', 'WelcomeController@index')->name(ROUTE_NAME_WELCOME);
});

