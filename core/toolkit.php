<?php

namespace PanelBar;

use Tpl;

class PB {

  /**
   *  LOAD
   */

  public static function load($type, $file, $array = array()) {
    return tpl::load(self::path($type, $file), $array);
  }


  /**
   *  PATHS
   */

  public static function path($type, $append) {
    $paths = array(
      'css'       => DS . 'assets' . DS . 'css' . DS,
      'js'        => DS . 'assets' . DS . 'js' . DS,
      'html'      => DS . 'templates' . DS,
    );
    return realpath(__DIR__ . '/..') . $paths[$type] . $append;
  }

  public static function font($append) {
    $base = str_ireplace(kirby()->roots()->index(), '', __DIR__);
    $base = substr_count($base, '/');
    $base = str_repeat('../', $base);
    return $base . 'panel' . DS . 'assets' . DS . 'fonts' . DS . $append;
  }


  /**
   *  PANEL 'API'
   */

  public static function url($target, $end = null) {
    $site = site();
    if(is_a($end, 'Page')) $end = $end->uri();

    $urls = array(
      'panel'  => $site->url() . '/panel',
      'add'    => $site->url() . '/panel/#/pages/add/'    . $end,
      'edit'   => $site->url() . '/panel/#/pages/show/'   . $end,
      'toggle' => $site->url() . '/panel/#/pages/toggle/' . $end,
      'files'  => $site->url() . '/panel/#/files/index/'  . $end,
      'file'   => $site->url() . '/panel/#/files/show/'   . $end,
      'user'   => $site->url() . '/panel/#/users/edit/'   . $end,
      'logout' => $site->url() . '/panel/logout',
    );
    return $urls[$target];
  }

  public static function version($version) {
    $kirby = kirby();
    return version_compare($kirby::$version, $version, '>=');
  }

}
