<?php

class FeedbackAutocomplete extends LimeSurveyPlugin
{

    static protected $name = 'Feeback Autocomplete';
    static protected $description = 'Autocomplete answers by previously given one\'s';

    public function init()
    {
        $this->subscribe('afterQuestionSave');
        $this->subscribe('beforeQuestionRender');
        $this->subscribe('newQuestionAttributes');
        $this->subscribe('afterSurveyComplete');
    }

    public function afterQuestionSave()
    {
        if ($this->hasFeedbackAutocomplete() && !$this->hasOtherOption()) {
            $this->question()->other = 'Y';
            $this->question()->save();
        }
    }

    public function beforeQuestionRender()
    {
        $isAutocompleteQuery =
            $this->request()->getQuery('query') != null &&
            $this->request()->getQuery('qid') != null;

        if ($isAutocompleteQuery) {
            $this->renderSuggestions();
        } else if ($this->hasFeedbackAutocomplete()) {
            $this->enhanceQuestion();
        }
    }

    private function enhanceQuestion()
    {
        $this->registerScriptFile('assets/bootstrap3-typeahead.min.js');
        $this->registerScriptFile('assets/feedback-autocomplete.js');
        $this->registerScript("autocomplete({$this->question()->qid});");
    }

    private function renderSuggestions()
    {
        $query = $this->request()->getQuery('query');
        $qid = $this->request()->getQuery('qid');
        $suggestions = array();

        foreach ($this->suggestionsFor($query, $qid) as $suggestion) {
            array_push($suggestions, array(
                'id' => $suggestion['code'],
                'name' => $suggestion['answer']
            ));
        }

        return $this->returnJson($suggestions);
    }

    private function suggestionsFor($query, $qid)
    {
        return Yii::app()->db->createCommand()
            ->select('code, answer')
            ->from(Answer::model()->tableName())
            ->where('qid=:qid AND answer LIKE :query')
            ->order('answer asc')
            ->limit(10)
            ->bindValue(":qid", $qid, PDO::PARAM_INT)
            ->bindValue(":query", '%' . $query . '%', PDO::PARAM_STR)
            ->queryAll();
    }

    public function afterSurveyComplete()
    {
        $surveyId = $this->getEvent()->get('surveyId');
        $responseId = $this->getEvent()->get('responseId');
        $questions = Question::model()->findAllByAttributes(array('sid' => $surveyId));

        foreach ($questions as $question) {
            if ($this->attribute('feedbackAutocomplete', $question->qid) != "1") {
                continue;
            }
            
            $response = Response::model($surveyId)->findByPk($responseId);
            $selectColumn = "{$question->sid}X{$question->gid}X{$question->qid}";
            $otherColumn = "{$selectColumn}other";

            if ($response[$otherColumn] == '') {
                continue;
            }

            $code = 'x' . substr(md5($response[$otherColumn]), 0, 4);
            Answer::model()->insertRecords(array(
                'qid' => $question->qid,
                'code' => $code,
                'answer' => $response[$otherColumn],
                'sortorder' => 0
            ));

            $response[$selectColumn] = $code;
            $response[$otherColumn] = '';
            $response->save();
        }
    }

    public function newQuestionAttributes()
    {
        $this->getEvent()->append('questionAttributes', array(
            'feedbackAutocomplete' => array(
                'types'     => '!',
                'category'  => gT('Autocomplete'),
                'sortorder' => 1,
                'inputtype' => 'switch',
                'default'   => 0,
                'caption'   => 'Suggest previous answers'
            )
        ));
    }

    private function hasFeedbackAutocomplete()
    {
        return $this->attribute('feedbackAutocomplete');
    }
}
