        Данное расширение основано на http://allforjoomla.ru/xplugins/plg-seolinks
        
        Адаптация для Yii - kosenka@gmail.com

        ESeoLinks представляет собой удобный инструмент SEO оптимизатора для внутренней перелинковки сайта.
        Он позволяет составить набор слов или словосочетаний и назначить им ссылки.
        ESeoLinks будет искать в тексте слова и заменять их на соответствующие ссылки.
        Если одной ссылке вы хотите назначить несколько слов или словосочетаний, то их следует перечислить через запятую.
        Из каждого набора слов ищется и делает ссылкой в тексте только одно слово или фразу, которую первую найдет.
        
        Ссылки не проставляются внутри тэгов:
        <!--seoLinks skip--><!--/seoLinks skip-->
        <!-- -->
        <style></style>
        <script></script>
        <h1></h1>
        <h2></h2>
        <h3></h3>
        <h4></h4>
        <h5></h5>
        <h6></h6>
        <a></a>

## Как использовать:
* файл ESeoLinks.php положить в папку /extensions/
* прописать в конфиге:
```php
        'components' => array(
                                'eseolinks'=>array(
                                            'class'=>'ext.ESeoLinks',
                                            'links'=>array(
                                                            //это правило обработает слова: тельфер, тельферы, тельферов и т.д.
                                                           'тельфер*'=>array(
                                                                           'url'=>'/', // проставляемая ссылка
                                                                           'maxNum'=>2, // кол-во ссылок
                                                                           'excludeUrls'=>array('/'), // какие url исключить из обработки
                                                                           //'onlyUrls'=>array('/87-elektrotelfery_kanatnye.html',), // только на указанных url делать обработку
                                                                          ),

                                                           //это правило обработает слова: таль, талей и т.д.
                                                           'тал*'=>array(
                                                                           'url'=>'/',
                                                                           'maxNum'=>2,
                                                                           'excludeUrls'=>array('/'),
                                                                           //'onlyUrls'=>array('/87-elektrotelfery_kanatnye.html',),
                                                                          ),

                                                           //это правило обработает слова: склад, склады, складу и т.д.
                                                           'склад.'=>array(
                                                                           'url'=>'/',
                                                                           'maxNum'=>2,
                                                                           'excludeUrls'=>array('/'),
                                                                           //'onlyUrls'=>array('/87-elektrotelfery_kanatnye.html',),
                                                                          ),

                                                          ),
                                           ),
```
* прописать в /layouts/main.php:
```php
        echo Yii::app()->eseolinks->processHtml($content);
```
