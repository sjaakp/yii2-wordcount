<?php
/**
 * sjaakp/yii2-wordcount
 * ----------
 * Word count behavior for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2020
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-wordcount
 * https://sjaakpriester.nl
 */

namespace sjaakp\wordcount;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class WordCount
 * @package sjaakp\wordcount
 */
class WordCount extends Behavior
{
    /**
     * @var string|array
     *  - if string: name of word counted attribute
     *  - if array:
     *          - element is string: word counted attribute
     *          - or element '<attr>' => '<wordCountAttr>'
     *  Attribute without explicitly set wordCountAttribute get '<attr>_count'
     */
    public $attribute = [];

    /**
     * @var string list of additional characters which will be considered as 'word'
     * Third parameter of PHP's str_word_count function.
     * Default adds diacritic characters of most European languages plus single and double quotes.
     * @link https://www.php.net/manual/en/function.str-word-count.php
     */
    public $charlist = 'à..ü‘’“”';


    protected $flip = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        $attrs = $this->attribute;
        if (is_string($attrs)) $attrs = [ $attrs ];

        foreach ($attrs as $key => $attr)
        {
            if (is_numeric($key))
            {
                unset($attrs[$key]);
                $attrs[$attr] = "{$attr}_count";
            }
        }
        $this->flip = array_flip($attrs);
        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        if (isset($this->flip[$name]))  {
            /* @var $owner BaseActiveRecord */
            $owner = $this->owner;
            $text = $owner->getAttribute($this->flip[$name]);

            return str_word_count(strip_tags($text), 0, $this->charlist);
        }
        return parent::__get($name);
    }

    /**
     * @inheritDoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (isset($this->flip[$name])) return true;
        return parent::canGetProperty($name, $checkVars);
    }
}
