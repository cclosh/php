<?php


namespace App\Http\Service;


use App\Http\Commands\RedisHelper;
use App\Http\DAL\WordDAL;
use DB;

class WordsService
{
    public static function checkWords($text)
    {

        $words = RedisHelper::get('answer_words', function () {

            $words = WordDAL::tableGet(['status' => 1]);

            $words_arr = [];

            foreach ($words as $word) {
                $words_arr[] = [
                    'id' => $word->id,
                    'title' => $word->title
                ];
            }

            return @serialize($words_arr);

        }, 60 * 10);

        $words = @unserialize($words);

        if (!is_array($words)) {
            $words = [];
        }

        $matchWordIDs = [];
        $status = false;

        if ($words) {


            foreach ($words as $word) {
                if ($text) {
                    if (strpos($text, $word['title']) !== false) {
                        $matchWordIDs[] = $word['id'];
                    }
                }
            }

            if ($matchWordIDs) {
                foreach ($matchWordIDs as $id) {
                    WordDAL::addCount($id, 1);
                }

                $status = false;

            } else {
                $status = true;
            }


        } else {
            $status = true;
        }

        return [$status, $matchWordIDs];

    }
}