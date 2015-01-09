<?php
namespace Acme\MainBundle\Service;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Tests\EventListener\TestKernelThatThrowsException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Perelink
{

    private $container;
    private $url;
    private $content;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->url = "http://perelink.binet.pro/service/otvet.php?proj=dvestrahovki.ru&url=";

        $this->content = "[]";
    }

    public function getInfo($uri)
    {
        $text = false;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . $uri);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $text = curl_exec($ch);
            if (trim($text) == '') {
                curl_setopt($ch, CURLOPT_URL, $this->url . $uri);
                $text = curl_exec($ch);
            }
            curl_close($ch);
        } else {
            $text = file_get_contents($this->url . $uri);
        }

        if ($text)
            $this->content = json_decode($text, true);
    }

    public function updateText($text)
    {
        return $this->change_content_perelink($text);
    }

    public function getLinksAfter()
    {
        $links = array();
        $data = $this->content;
        if (is_array($data)) {
            foreach ($data as $pismo) {
                if ($pismo[3] == 'posle') {
                    $first = mb_strtoupper(mb_substr($pismo[2], 0, 1, 'UTF-8'), 'UTF-8');
                    $slov = $first . mb_substr($pismo[2], 1, mb_strlen($pismo[2], 'UTF-8'), 'UTF-8');

                    $links[] = array(
                        'key' => $slov,
                        'link' => $pismo[1]
                    );
                }
            }
        }

        return $links;
    }


    private function change_content_perelink($content)
    {
        $data = $this->content;
        if (is_array($data)) {
//            preg_match_all('#\[caption[^\]]*\][^\[]*#uis', $content, $wp_cap);
//            for ($j = 0; $j < count($wp_cap[0]); $j++) {
//                $content = str_replace($wp_cap[0][$j], '<!--wp_cap' . $j . '-->', $content);
//            }
            foreach ($data as $pismo)
                if ($pismo[3] == 'text') {
                    $pismo[2] = '';

                    $content_save = $this->check_vpis_ssil($content, $pismo, 1);
                    if ($content_save != '')
                        $content = $content_save;
                }
//            for ($j = 0; $j < count($wp_cap[0]); $j++) {
//                $content = str_replace('<!--wp_cap' . $j . '-->', $wp_cap[0][$j], $content);
//            }
        }

        return $content;
    }

    private function check_vpis_ssil($content, $pismo, $cikl)
    {
        $rep_in = array('(', ')');
        $rep_out = array('\(', '\)');
        $content = preg_replace("#(" . str_replace($rep_in, $rep_out, $pismo[4]) . ")([^а-яa-z0-9_])#iu", '<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>\\1</a>\\2', $content, 1);
        if ($cikl > 10)
            return $content;
        if (!stristr($content, '<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>')) {
            $pismo[4] = str_replace('–', '&\#8211;', str_replace($rep_in, $rep_out, $pismo[4]));
            $content = preg_replace("#(" . $pismo[4] . ")([^а-яa-z0-9_])#iu", '<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>\\1</a>\\2', $content, 1);
        }
//        if (preg_match('#<h[1-6][^>]*>[^<]*<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>' . str_replace($rep_in, $rep_out, $pismo[4]) . '</a>[^<]*</h[1-6]>#ui', $content, $h2)) {
//            $zamena = explode($h2[0], $content);
//            $zamena[1] = $this->check_vpis_ssil($zamena[1], $pismo, $cikl + 1);
//            $content = implode(preg_replace('#<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>([^<]*)</a>#ui', '\\1', $h2[0]), $zamena);
//        }
//        if (preg_match('#(title="[^>]*)<a href="' . $pismo['1'] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>' . str_replace($rep_in, $rep_out, $pismo[4]) . '</a>#ui', $content, $ssil)) {
//            $zamena = explode($ssil[0], $content);
//            $zamena[1] = $this->check_vpis_ssil($zamena[1], $pismo, $cikl + 1);
//            $content = implode($ssil[1] . $pismo[4], $zamena);
//        }
//        if (preg_match('#(alt="[^>]*)<a href="' . $pismo['1'] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . '>' . str_replace($rep_in, $rep_out, $pismo[4]) . '</a>#ui', $content, $ssil)) {
//            $zamena = explode($ssil[0], $content);
//            $zamena[1] = $this->check_vpis_ssil($zamena[1], $pismo, $cikl + 1);
//            $content = implode($ssil[1] . $pismo[4], $zamena);
//        }
//        if (preg_match('#(<a[^>]*>[^<]*)<a href="' . $pismo[1] . '" ' . ($pismo[2] != '' ? 'title="' . $pismo[2] . '"' : '') . $pismo[2] . '>' . str_replace($rep_in, $rep_out, $pismo[4]) . '</a>#iu', $content, $ssil)) {
//            $zamena = explode($ssil[0], $content);
//            $zamena[1] = explode('</a>', $zamena[1]);
//            $temp_zamen = $zamena[1][0];
//            unset($zamena[1][0]);
//            $zamena[1] = implode('</a>', $zamena[1]);
//            $zamena[1] = $this->check_vpis_ssil($zamena[1], $pismo, $cikl + 1);
//            $zamena[1] = $temp_zamen . '</a>' . $zamena[1];
//            $content = implode($ssil[1] . $pismo[4], $zamena);
//        }

        return $content;
    }
}