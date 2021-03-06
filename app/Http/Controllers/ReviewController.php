<?php

namespace App\Http\Controllers;

use App\Models\ReviewMeeting;
use Config;
use Gate;
use App\Exceptions\AppException;
use App\Logic\Stage\ReviewMeetingStages\StageHandler as ReviewMeetingStageHandler;
use App\Logic\Role\RoleFactory;

class ReviewController extends Controller
{
    public function apply($id = null)
    {
        if (is_null($id)) {

            if (Gate::forUser($this->loginUser)->denies('apply-review-meeting')) {
                throw new AppException('RVWCTL001', ERROR_MESSAGE_NOT_AUTHORIZED);
            }

            $reviewMeetingIns = new ReviewMeeting();
            $reviewMeetingIns->stage = STAGE_ID_REVIEW_MEETING_INITIATE;
            $reviewMeetingIns->year = date('Y');
            $reviewMeetingIns->lanID = $this->loginUser->getUserInfo()->lanID;
            $reviewMeetingIns->save();

            $id = $reviewMeetingIns->id;
        }

        return redirect()->action('ReviewController@display', ['id' => $id]);
    }

    public function edit()
    {
        if (empty($reviewMeetingID = trim(request()->input('reviewMeetingID')))
            || empty($attr = trim(request()->input('attr')))) {
            throw new AppException('RVWCTL002');
        }

        if (empty($value = trim(request()->input('value')))) {
            return response()->json(['status'=>'good']);
        }

        $reviewMeetingIns = ReviewMeeting::getIns($reviewMeetingID);
        if (STAGE_ID_REVIEW_MEETING_INITIATE != $reviewMeetingIns->stage) {
            throw new AppException('RVWCTL003', 'Incorrect Review Meeting Info');
        }

        if (Gate::forUser($this->loginUser)->denies('review-meeting-operable', $reviewMeetingIns)) {
            throw new AppException('RVWCTL004', ERROR_MESSAGE_NOT_AUTHORIZED);
        }

        if ($value != $reviewMeetingIns->$attr) {
            $reviewMeetingIns->$attr = $value;
            $reviewMeetingIns->save();

            if (in_array($attr, ['date', 'time', 'venue'])) {
                $reviewMeetingIns->participants()->update(['willAttend' => null]);
            }
        }

        return response()->json(['status'=>'good']);
    }

    public function display($id)
    {
        $reviewMeetingIns = ReviewMeeting::getIns($id);
        if (Gate::forUser($this->loginUser)->denies('review-meeting-visible', $reviewMeetingIns)) {
            throw new AppException('RVWCTL005', ERROR_MESSAGE_NOT_AUTHORIZED);
        }

        $stageView = ReviewMeetingStageHandler::renderReviewMeetingStageView($reviewMeetingIns);

        return view('review.display')
                ->with('title', PAGE_NAME_REVIEW_MEETING_DISPLAY)
                ->with('reviewMeetingIns', $reviewMeetingIns)
                ->with('stageView', $stageView)
                ->with('logs', $reviewMeetingIns->log)
                ->with('stageNames', Config::get('constants.stageNames'));
    }

    public function remove()
    {
        if (Gate::forUser($this->loginUser)->denies('project-removable')) {
            throw new AppException('RVWCTL006', ERROR_MESSAGE_NOT_AUTHORIZED_OPERATE);
        }

        if (empty($reviewID = trim(request()->input('meetingID')))) {
            throw new AppException('RVWCTL007');
        }

        $reviewIns = ReviewMeeting::getIns($reviewID);
        $reviewIns->delete();

        return response()->json(['status' => 'good']);
    }

    public function listAll()
    {
        $roleIns = RoleFactory::create($this->loginUser->getActiveRole()->roleID);
        $reviewMeetings = ReviewMeeting::all()->filter(function ($ins) use ($roleIns) {
            return $roleIns->reviewMeetingVisible($ins);
        });
        $removable = $this->loginUser->getActiveRole()->roleID == ROLE_ID_APP_ADMIN;

        return view('review.listall')
                ->with('title', PAGE_NAME_REVIEW_MEETING_LIST)
                ->with('removable', $removable)
                ->with('reviewMeetings', $reviewMeetings);
    }
}
