<?php

namespace App\Http\Controllers;


use App\Exceptions\AppException;
use App\Models\Document;
use App\Models\Project;
use App\Models\PutRecord;
use Gate;
use Config;

class DocumentController extends Controller
{
    public function display($id, $name)
    {
        $documentIns = Document::find($id);
        if (is_null($documentIns)) {
            throw new AppException('DOC001', 'Incorrect Document ID.');
        }

        // Check Visibility
        $referenceIns = $documentIns->documentable;
        if ($referenceIns instanceof Project) {
            if (Gate::forUser($this->loginUser)->denies('document-visible', $documentIns)) {
                throw new AppException('DOC002', ERROR_MESSAGE_NOT_AUTHORIZED);
            }
        } elseif ($referenceIns instanceof PutRecord) {
            $reviewMeeting = $referenceIns->topics->first()->reviewMeeting;
            if (Gate::forUser($this->loginUser)->denies('review-meeting-visible', $reviewMeeting)) {
                throw new AppException('DOC008', ERROR_MESSAGE_NOT_AUTHORIZED);
            }
        }

        return response()->file($documentIns->getFullPath());
    }

    public function upload()
    {
        if (empty($reference = request()->input('reference'))
            || empty($referenceID = request()->input('id'))
            || empty($fileType = request()->input('filetype'))
            || empty ($fileIns = request()->file('upload-doc'))) {
            throw new AppException('DOC003', 'Data Error');
        }

        switch ($reference) {
            case 'Projects':
                $referenceIns = Project::find($referenceID);

                if (DOC_TYPE_OTHER_DOCS == $fileType) {
                    if (Gate::forUser($this->loginUser)->denies('project-visible', $referenceIns)) {
                        throw new AppException('DOC004', ERROR_MESSAGE_NOT_AUTHORIZED);
                    }
                } else {
                    if (Gate::forUser($this->loginUser)->denies('project-operable', $referenceIns)) {
                        throw new AppException('DOC006', ERROR_MESSAGE_NOT_AUTHORIZED);
                    }
                }

                break;

            case 'PutRecords':
                $referenceIns = PutRecord::getIns($referenceID);
                $reviewMeeting = $referenceIns->topics->first()->reviewMeeting;

                if (Gate::forUser($this->loginUser)->denies('review-meeting-operable', $reviewMeeting)) {
                    throw new AppException('DOC007', ERROR_MESSAGE_NOT_AUTHORIZED);
                }
                break;
        }

        if (is_null($referenceIns) || !array_key_exists($fileType, Config::get('constants.documentTypeNames'))) {
            throw new AppException('DOC005', 'Incorrect Doc Info.');
        }

        $documentIns = Document::storeFile($fileIns, $referenceIns, $fileType);

        return response()->json([
            'status' => 'good',
            'info' => [
                'fileType' => Config::get('constants.documentTypeNames.'.$fileType),
                'documentIns' => $documentIns,
                'userInfo' => $this->loginUser->getUserInfo(),
            ],
        ]);
    }

}
