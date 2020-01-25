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

use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;

/**
 * Class Totals
 * @package sjaakp\wordcount
 */
abstract class Totals
{
    /**
     * @param $query ActiveQuery
     * @param $attribute string|array
     * @return string|array
     * Calculate the total of $attribute-values in records found by $query
     * - if $attribute is string: return integer
     * - if $attribute is array of attribute names: return array of totals, keys are attribute names
     */
    public static function count($query, $attribute)
    {
        $atts = $attribute;
        if (is_string($atts)) $atts = [ $atts ];
        $all = $query->all();
        $acc = array_combine($atts, array_fill(0, count($atts), 0));
        $sums = array_reduce($all, function($carry, $item) use ($atts) {
            /* @var $item BaseActiveRecord */
            $r = [];
            foreach($atts as $att)  {
                $r[$att] = $carry[$att] + $item->$att;
            }
            return $r;
        }, $acc);
        return is_string($attribute) ? current($sums) : $sums;
    }
}
