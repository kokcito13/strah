<?php
namespace Acme\MainBundle\Service;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Tests\EventListener\TestKernelThatThrowsException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PostEdit
{
    public function __construct()
    {

    }

    public function setAdvertising($text)
    {
        $arrAdvert = array();
        /* $arrAdvert['images'] - вставляет код после каждой картинки в статье*/
//            $arrAdvert['images'] = <<<EODIMAGES
//Тут код нашего адика
//EODIMAGES;
//
//            /* $arrAdvert['videos'] - вставляет код после каждого видео в статье*/
//            $arrAdvert['videos'] = <<<EODVIDEOS
//Тут код нашего адика
//EODVIDEOS;

        $arrAdvert['1'] = <<<EOD1
<p><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Links Top 1.1, DveStrahovki -->
<ins class="adsbygoogle"
     style="display:block;height:300px;"
     data-ad-client="ca-pub-3861532892125732"
     data-ad-slot="9189303239"
     data-ad-format="link"
     data-full-width-responsive="true"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></p>
EOD1;
        $arrAdvert['last'] = <<<EOD9
<p><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Links Bot 1.1, DveStrahovki -->
<ins class="adsbygoogle"
     style="display:block;height:300px;"
     data-ad-client="ca-pub-3861532892125732"
     data-ad-slot="1825818536"
     data-ad-format="link"
     data-full-width-responsive="true"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></p>
EOD9;

        return $this->getContents($text, $arrAdvert);
    }

    public function getContents($pageText = '', $arrAdvert = array() )
    {
        if (substr_count($pageText, '<p') > 2) {
            $text = '';
            $contents = explode("</p>", $pageText);
            foreach ($contents as $k => $content) {
                $text .= $content;
                $text .= (count($contents) == ($k + 1)) ? "" : "</p>";
                if (isset($arrAdvert[$k + 1])  && count($contents) > 4)
                    $text .= $arrAdvert[$k + 1];
                if (isset($arrAdvert['prelast']) && count($contents) > 4) {
                    if ((count($contents) - 3) == $k) {
                        $text .= $arrAdvert['prelast'];
                    }
                }
                if (isset($arrAdvert['last'])) {
                    if (count($contents) == ($k + 1)) {
                        $text .= $arrAdvert['last'];
                    }
                }
                $text .= "";
            }
        } else {
            $text =  $pageText;
        }

        return $text;
    }

    public function setReadMore($text, $links = array())
    {
        if (count($links) > 0 && !empty($text)) {
            if (substr_count($text, '<h2') > 2) {
                $pageText = $text;
                $text = '';
                $i = 0;
                $contents = explode("<h2", $pageText);
                foreach ($contents as $k => $content) {
                    $text .= $content;
                    if ( count($links) > 0 && in_array($k, array(2,4,6,7)) ) {
                        $text .= "<!--noindex--><p class='read_more_p'>Читайте также:</p><!--/noindex-->";
                        $text .= "<ul class='read_more_ul'>
                                        <li>
                                            <a href=\"".$links[$i]['link']."\"
                                                title=\"".$links[$i]['key']."\"
                                            >".$links[$i]['key']."</a>
                                        </li>
                                   </ul>";
                        unset($links[$i]);
                        $i++;
                    }

                    $text .= (count($contents) == ($k + 1)) ? "" : "<h2";
                    $text .= "";
                }
            }
        }

        return array($text, $links);
    }

    /**
     * @param $text
     * @return array
     */
    public function setContents($text)
    {
        $contents = array();
        preg_match_all("/<h(2|3).*?<\/h(2|3)>/si", $text, $m);

        if (!empty($m) && isset($m[0]) && !empty($m[0])) {
            foreach ($m[0] as $value) {
                $value = strip_tags($value);
                $contents[] = trim($value);
            }
        }

        return $contents;
    }
}