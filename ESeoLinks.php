<?
/*
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

        Как использовать:

        1) файл ESeoLinks.php положить в папку /extensions/
        2) прописать в конфиге:
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

        3) прописать в /layouts/main.php:
        echo Yii::app()->eseolinks->processHtml($content);

*/

class ESeoLinks
{
        public $links=array();

        public function init()
        {
        }

	public static function maskContent($txt)
        {
		$txt = str_replace("\'","'",$txt);
		$result = base64_encode($txt);
		return $result;
	}

	public static function unmaskContent($txt)
        {
		$result = base64_decode($txt);
		return $result;
	}

        public function processHtml($body)
        {
		if(count($this->links)==0) return $body;

		$uri=Yii::app()->request->getRequestUri();

		$wordFormWildCards = array(
			'\\\.'	=>	'#-#',
			'\\\*'	=>	'#--#',
			'.'	=>	'[a-zа-яіїєґ]?',
			'*'	=>	'[a-zа-яіїєґ]*'
		);
		$clearRegs = array(
			'#-#'	=>	'\\.',
			'#--#'	=>	'\\*'
		);

		$body = preg_replace("~(<\!\-\-seoLinks skip\-\->)(.*?)(<\!\-\-\/seoLinks skip\-\->)~sie",'"<:ZyX>".ESeoLinks::maskContent("\\2")."<:ZyX/>"',$body);
		$body = preg_replace("~(<script)(.*?)(<\/script>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		$body = preg_replace("~(<\!\-\-)(.*?)(\-\-\>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		$body = preg_replace("~(<style)(.*?)(<\/style>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		$body = preg_replace("~(<h[1-6])(.*?)(<\/h[1-6]>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		$body = preg_replace("~(<a)(.*?)(<\/a>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		$body = preg_replace("~(<[a-z])(.*?)(>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
		foreach($this->links as $word=>$link)
                {
                        if(is_array($link['excludeUrls']) and in_array($uri,$link['excludeUrls'])) continue;

                        if( (is_array($link['onlyUrls']) and in_array($uri,$link['onlyUrls'])) or empty($link['onlyUrls']) )
                        {
        			$word = str_replace(', ',',',$word);
        			$word = str_replace(' ,',',',$word);
        			$word = str_replace(',','|',$word);
        			$word = str_replace(array_keys($wordFormWildCards),array_values($wordFormWildCards),addslashes($word));
        			$word = str_replace(array_keys($clearRegs),array_values($clearRegs),$word);

        			if($link['hasNum']>=$link['maxNum']) continue;

                                $replace = CHtml::link('$2',$link['url'],$link['htmlOptions']);
        			if(isset($link['template'])) $replace = strtr($link['template'],array('{url}'=>$replace));
        			$replace = '$1'.$replace.'$3';
        			$search = "~([\s\.\,\;\!\?\:\>\(\)\'\"\*\/В«])(".$word.")([\*\/\'\"\(\)\<\s\.\,\;\!\?\:В»])~siu";
        			$body = preg_replace("~(<a)(.*?)(?=<\/a>)(<\/a>)~sie",'"<:ZyX>".ESeoLinks::maskContent("$1$2$3")."<:ZyX/>"',$body);
        			$body = preg_replace($search, $replace, $body, $link['maxNum']);
        			if(empty($body)) return $body;
        			$link['hasNum']+= substr_count($body,'<a ');
                        }
		}
		$body = preg_replace("~<\:ZyX>(.*?)(?=<\:ZyX\/>)<\:ZyX\/>~sie",'ESeoLinks::unmaskContent("$1")',$body);
		$body = preg_replace("~<\:ZyX>(.*?)(?=<\:ZyX\/>)<\:ZyX\/>~sie",'ESeoLinks::unmaskContent("$1")',$body);

                return $body;
        }

}
