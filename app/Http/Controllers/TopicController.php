<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Models\Project;
use App\Models\PutRecord;
use App\Models\ReviewMeeting;
use App\Models\ReviewTopic;
use Gate;

class TopicController extends Controller
{
    private $reviewMeetingIns;

    public function __construct()
    {
        parent::__construct();

        if (empty($reviewMeetingID = trim(request()->input('reviewMeetingID')))) {
            throw new AppException('TPCCTL001');
        }

        $this->reviewMeetingIns = ReviewMeeting::getIns($reviewMeetingID);

        // stage check
        if (STAGE_ID_REVIEW_MEETING_INITIATE != $this->reviewMeetingIns->stage) {
            throw new AppException('TPCCTL004', 'Incorrect Review Meeting Info');
        }

        // operable check
        if (Gate::forUser($this->loginUser)->denies('review-meeting-operable', $this->reviewMeetingIns)) {
            throw new AppException('TPCCTL002', ERROR_MESSAGE_NOT_AUTHORIZED);
        }
    }

    public function addProject()
    {
        // parameter check
        if (empty($type = trim(request()->input('type')))
            || empty($projectID = trim(request()->input('projectID')))) {
            throw new AppException('TPCCTL003');
        }

        // existence check
        $projectIns = Project::getIns($projectID);
        $topics = $this->reviewMeetingIns->topics()->with('topicable')->get();
        $existence = $topics->filter(function ($ins, $key) use ($projectIns, $type) {
            $referrerIns = $ins->topicable;
            return ($referrerIns instanceof Project)
                && ($referrerIns->id == $projectIns->id)
                && ($ins->type == $type);
        });

        if (0 != $existence->count()) {
            throw new AppException('TPCCTL005', 'Topic is already added.');
        }

        $topic = $this->reviewMeetingIns->topics()->create([]);
        $topic->type = $type;
        $projectIns->topics()->save($topic);

        return response()->json(['status'=>'good']);
    }

    public function addPutRecord()
    {
        // parameter check, stage check, operable check, existence
        if (empty($name = trim(request()->input('name')))
            || empty($type = trim(request()->input('type')))) {
            throw new AppException('TPCCTL006');
        }

        // create new put record and add relationship to the review meeting
        $putRecord = new PutRecord();
        $putRecord->name = $name;
        $putRecord->save();

        $topic = $this->reviewMeetingIns->topics()->create([]);
        $topic->type = $type;
        $putRecord->topics()->save($topic);

        return response()->json(['status'=>'good']);
    }

    public function remove()
    {
        if (empty($topicID = trim(request()->input('topicID')))) {
            throw new AppException('TPCCTL008');
        }

        $topicIns = ReviewTopic::getIns($topicID);
        $topicIns->delete();

        return response()->json(['status'=>'good']);
    }
}
