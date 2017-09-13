<?php

class LimeSurveyPlugin extends \ls\pluginmanager\PluginBase
{

    protected $storage = 'DbStorage';

    protected function attributes($qid = null)
    {
        if (!isset($qid))
            $qid = $this->question()->qid;

        return QuestionAttribute::model()->getQuestionAttributes($qid);
    }

    protected function attribute($name, $qid = null)
    {
        $attributes = $this->attributes($qid);
        return $this->arrayValueFor($name, $attributes);
    }

    private function arrayValueFor($key, $array)
    {
        if (array_key_exists($key, $array))
            return $array[$key];
        else
            return null;
    }

    protected function request()
    {
        return Yii::app()->request;
    }

    protected function returnJson($data) {
        ob_end_clean();
        ob_start();
        header("Content-type: application/json");
        exit(json_encode($data));
    }

    protected function hasOtherOption() {
        return $this->question()->other == 'Y';
    }

    protected function question()
    {
        if ($this->getEvent()->get('model') != null)
            return $this->getEvent()->get('model');
        else {
            $qid = $this->getEvent()->get('qid');
            return Question::model()->findByAttributes(array('qid' => $qid));
        }
    }

    protected function say($message)
    {
        return Yii::app()->setFlashMessage(gT($message));
    }

    protected function registerScriptFile($path)
    {
        App()->clientScript->registerScriptFile(App()->assetManager->publish(dirname(__FILE__) . '/' . $path), CClientScript::POS_END);
    }

    protected function registerScript($script)
    {
        App()->clientScript->registerScript(md5($script), $script, CClientScript::POS_END);
    }

    protected function registerStyleFile($path)
    {
        App()->clientScript->registerCssFile(App()->assetManager->publish(dirname(__FILE__) . '/' . $path));
    }
}
