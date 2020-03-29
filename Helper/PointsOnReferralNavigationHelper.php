<?php

namespace Plugin\PointsOnReferral\Helper;

class PointsOnReferralNavigationHelper {

    protected $app;
    protected $closing_ul = "</ul>";
    protected $closing_body = "</body>";

    public function __construct($app) {
        $this->app = $app;
    }

    public function getNavHtml($html) {
        $array = array();
        $regex = "/<ul[^>]*id(\s)*=('|\")navi_list(\s)*('|\")>((.|\n|\r|(\n\r))*?)<\/ul>/i";
        preg_match($regex, $html, $array);
        if (count($array)) {
            return $array[0];
        } else {
            return "";
        }
    }

    /**
     * @param $html
     * @return string
     */
    public function addLink($html) {
        $nav_html = $this->getNavHtml($html);
        if (PointsOnReferralHelper::endsWith($nav_html, $this->closing_ul)) {
            $html = $this->addLinkHtml($html, $nav_html);
        } else {
            $html = $this->addLinkJavaScript($html);
        }
        return $this->addFixWidthScript($html);
    }

    public function isMyPageReferral($html) {
        return preg_match("/data-mypageno(\s)*=('|\")*referral('\")*/", $html) ? true : false;
    }

    protected function addLinkHtml($html, $nav_html) {
        $link_html = $this->renderView($html,'PointsOnReferral/Resource/template/default/MyPage/nav_link_add.twig');
        $nav_link_added_html = str_replace($this->closing_ul, $link_html . $this->closing_ul, $nav_html);
        return str_replace($nav_html, $nav_link_added_html, $html);
    }

    protected function addLinkJavaScript($html) {
        $this->closing_body = "</body>";
        if (strpos($html, $this->closing_body) === false) {
            return $html;
        }
        $link_html = $this->renderView($html,'PointsOnReferral/Resource/template/default/MyPage/nav_link_add.twig');
        $link_script = $this->renderView($html,'PointsOnReferral/Resource/template/default/MyPage/nav_link_add_javascript.twig');
        return str_replace($this->closing_body, $link_script . $this->closing_body, $html);
    }

    protected function addFixWidthScript($html) {
        $this->closing_body = "</body>";
        if (strpos($html, $this->closing_body) === false) {
            return $html;
        }
        $script = $this->renderView($html, 'PointsOnReferral/Resource/template/default/MyPage/nav_link_fix_width.twig');
        return str_replace($this->closing_body, $script . $this->closing_body, $html);
    }

    protected function renderView($html, $template) {
        if ($this->isMyPageReferral($html)) {
            return $this->app->renderView($template, array(
               'mypageno' => 'referral'
            ));
        } else {
            return $this->app->renderView($template);
        }
    }
}
