<?php

namespace App\Logic\Stage;


use App\Models\StageLog;
use App\Logic\LoginUser\LoginUserKeeper;

trait TLogOperation
{
    public function logOperation($comment = null)
    {
        $log = new StageLog();
        $log->fromStage = $this->stageID;
        $log->toStage = $this->stageID;
        $log->dept = LoginUserKeeper::getUser()->getActiveRole()->lanID;
        $log->lanID = LoginUserKeeper::getUser()->getUserInfo()->lanID;
        $log->comment = $comment;
        $log->timeAt = date('Y-m-d H:i:s');

        if ($this->canStageUp()) {
            $log->toStage = $this->getNextStage()->getStageID();

            // stage up
            $this->referrer->stage = $log->toStage;
            $this->referrer->save();
        }

        if (isset($para['operation'])) { // for stage pass sign
            $log->data1 = $para['operation'];
        }

        $this->referrer->log()->save($log);
    }

    public function operate($paras = null)
    {
        $this->logOperation();
    }
}