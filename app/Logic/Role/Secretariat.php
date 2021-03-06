<?php

namespace App\Logic\Role;


use App\Models\User;
use App\Models\ReviewMeeting;

class Secretariat extends AbstractRole
{
    protected $roleID = 4;
    protected $roleName = ROLE_NAME_SECRETARIAT;
    protected $roleSpecPages = [
        ROUTE_NAME_REVIEW_APPLY,
        'ReviewMeetingList'
    ];
    protected $operableStages= [
        STAGE_ID_PRETRIAL,
        STAGE_ID_AUDIT,
        STAGE_ID_REVIEW_MEETING_INITIATE,
        STAGE_ID_REVIEW_MEETING_GENERATE_MINUTES,
        STAGE_ID_REVIEW_MEETING_DECIDE_PROCUREMENT_MODE,
    ];

    public function getCandidates()
    {
        return User::inService()->orderBy('uEngName')->get();
    }

    public function reviewMeetingVisible(ReviewMeeting $reviewMeeting)
    {
        return true;
    }

    public function pendingReviewMeetingParticipate(ReviewMeeting $reviewMeeting)
    {
        return $reviewMeeting->date >= date('Y-m-d')
                && in_array($reviewMeeting->stage, $this->stages['reviewMeetingPendingParticipate']);
    }
}