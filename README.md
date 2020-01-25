Yii2-wordcount
--------------
#### Word count behavior for Yii2 ####

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-wordcount/v/stable)](https://packagist.org/packages/sjaakp/yii2-wordcount)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-wordcount/downloads)](https://packagist.org/packages/sjaakp/yii2-wordcount)
[![License](https://poser.pugx.org/sjaakp/yii2-wordcount/license)](https://packagist.org/packages/sjaakp/yii2-wordcount)

This is a word counting behavior for [ActiveRecords](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord) 
in the [Yii 2.0](https://yiiframework.com/ "Yii") PHP Framework. It counts the words
in one or more designated attributes. The count(s) are exposed through new 
[virtual attributes](https://www.yiiframework.com/wiki/167/understanding-virtual-attributes-and-getset-methods).

A demonstration of **yii2-wordcount** is [here](https://sjaakpriester.nl/software/wordcount).

## Installation ##

The preferred way to install **yii2-wordcount** is through [Composer](https://getcomposer.org/). 
Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-wordcount": "*"` 

Or run:

`composer require sjaakp/yii2-wordcount "*"` 

You can manually install **yii2-wordcount** by
 [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-wordcount/archive/master.zip).

## Using WordCount ##

**WordCount** is a [Behavior](https://www.yiiframework.com/doc/api/2.0/yii-base-behavior)
for an [ActiveRecord](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord). It has
one property:

- **$attribute** `string|array` The name of the attribute of wihich we want to count the
words. Can also be an array of attribute names. Moreover, it can be an array with
`'<attrName>' => '<countAttrName>'` elements.

If the count attribute name is not explicitly set in the `$attribute` array, the virtual count attribute
is called `'<attrName>_count'` automatically.

Here is the simplest way to set up an ActiveRecord with **WordCount**:

    namespace app\models;
    
    use yii\db\ActiveRecord;
    use sjaakp\wordcount\WordCount;
    
    class Article extends ActiveRecord
    {
        public static function tableName()
        {
            return 'article';
        }
        // ...
            
        public function behaviors()
        {
            return [
                [
                    'class' => WordCount::class,
                    'attribute' => 'bodytext'
                ],
                // ... other behaviors ...
            ];
        }
        // ...
    }

Class `Article` will now have a new virtual attribute with the name `'bodytext_count'`.
It's value is an integer and it can be queried just like any other attribute:

    $wordsCounted = $model->bodytext_count
    
 A slightly more involved way to set up an Activerecord with **WordCount** would be:
 
     // ...     
     class Article extends ActiveRecord
     {
         // ...
             
         public function behaviors()
         {
             return [
                 [
                     'class' => WordCount::class,
                     'attribute' => [
                        'bodytext' => 'textcount',
                        'title' => 'titlecount'
                     ]
                 ],
                 // ... other behaviors ...
             ];
         }
         // ...
     }

It gives two new virtual attributes, named `'textcount'` and `'titlecount'`.

**Notice** that **WordCount** uses the PHP function
[`str_word_count()`](https://www.php.net/manual/en/function.str-word-count.php).
This is not the most perfect way to count words, so you should consider the results
as no more than good approximations.

## Totals ##

**Totals** is a helper class with one method:

    public static function count($query, $attribute)
    
This static function returns the total of `$attribute` values in the ActiveRecords
found by [ActiveQuery](https://www.yiiframework.com/doc/api/2.0/yii-db-activequery)
`$query`. If `$attribute` is a `string`, the return value will be an `integer`.
If `$attribute` is an array of attribute names, `count()` will return an array
with `'<attr>' => <total>` elements.

Usage example:

    use sjaakp\wordcount\Totals;

    $totals = Totals::count(Article::find(), [ 'titlecount', 'textcount' ]);
    
**Notice** that `count()` also works with non-virtual attributes. However, it would
be much wiser to use [`ActiveQuery::sum()`](https://www.yiiframework.com/doc/api/2.0/yii-db-query#sum()-detail)
in that case.
