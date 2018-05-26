<?php


namespace App\Http\Service;


use App\Http\DAL\AnswerDAL;
use App\Http\DAL\NoticeDAL;
use App\Http\DAL\QuestionDAL;
use App\Http\DAL\User_questionDAL;
use App\Http\DAL\UserInfoDAL;

class NoticeService
{

    const ADD_ANSWER = 1;
    const LIKE_ANSWER = 2;
    const FOLLOW_YOUR_QUESTION = 3;
    const FOLLOW_QUESTION_NEW_ANSWER = 4;

    public static function setNotice($rID, $userID, $type)
    {
        $content = '';
        $toUID = 0; //要推送的用户
        $qID = 0;
        $aID = 0;

        $userInfo = UserInfoDAL::tableFirst(['userID' => $userID]);

        if ($userInfo->receiveNotice != 1) {
            return;
        }

        switch ($type) {
            case self::ADD_ANSWER:
                $qID = $rID;
                $questoin = QuestionDAL::tableFirst(['id' => $qID]);
                $content = sprintf('%s回复了你的问题[%s]。', $userInfo->nickName, $questoin->title);
                $toUID = $questoin->createUID;
                break;
            case self::LIKE_ANSWER:
                $aID = $rID;
                $answer = AnswerDAL::tableFirst(['id' => $rID]);
                $qID = $answer->qID;
                $questoin = QuestionDAL::tableFirst(['id' => $qID]);
                $content = sprintf('%s点赞了你的回复[%s]。', $userInfo->nickName, $answer->answer);
                $toUID = $answer->answerUID;
                break;
            case self::FOLLOW_YOUR_QUESTION:
                $qID = $rID;
                $questoin = QuestionDAL::tableFirst(['id' => $qID]);
                $content = sprintf('%s关注了你的问题[%s]。', $userInfo->nickName, $questoin->title);
                $toUID = $questoin->createUID;
                break;
        }

        $insers = [];//入库信息

        if ($content) {

            PushService::pushToUID($toUID, $content);

            $insers[] = [
                'uID' => $toUID,
                'otherUID' => $userInfo->id,
                'questionID' => $qID,
                'question' => $questoin->title,
                'answerID' => $aID,
                'addTime' => time(),
                'type' => $type
            ];

            //关注的问题有新回复
            if ($type == self::ADD_ANSWER) {
                $infos = User_questionDAL::tableGet(['qID' => $rID]);

                $content = sprintf('我关注的问题[%s]有了新的回复。', $questoin->title);
                foreach ($infos as $one) {

                    PushService::pushToUID($one->uID, $content);

                    $insers[] = [
                        'uID' => $one->uID,
                        'otherUID' => $userInfo->id,
                        'questionID' => $qID,
                        'question' => $questoin->title,
                        'answerID' => $aID,
                        'addTime' => time(),
                        'type' => $type
                    ];
                }
            }




            NoticeDAL::tableInsert($insers);
        }
    }
}