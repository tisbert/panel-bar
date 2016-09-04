<?php

namespace Kirby\distantnative\panelBar\Patterns;

use A;
use Tpl;

class Pattern {

  public function __construct($core) {
    $this->core = $core;
  }

  protected function asset($type, $asset) {
    $asset = $this->core->assets->load($type, $asset);
    $this->core->assets->add($type, $asset);
  }

  protected function base($args) {
    $dir = dirname(__DIR__) . DS . '..' . DS . 'snippets' . DS . 'patterns';
    return tpl::load($dir . DS . 'base.php', a::merge([
      'class'   => null,
      'url'     => null,
      'label'   => null,
      'icon'    => null,
      'mobile'  => 'icon',
      'content' => null,
      'title'   => null,
      'right'   => false
    ], $args));
  }

}