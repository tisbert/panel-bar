<?php

namespace panelBar;

require_once('bootstrap.php');

use C;
use F;
use panelBar\Pattern;

class Core {

  public $elements;
  public $output;
  public $assets;
  public $css;
  public $js;

  public $panel;
  public $page;

  public function __construct($opt = array()) {
    $this->page   = page();
    $this->panel  = f::load(__DIR__ . '/lib/integrate.php');
    $this->panel  = panel();

    // Assets
    $this->css    = isset($opt['css']) ? $opt['css'] : true;
    $this->js     = isset($opt['js'])  ? $opt['js']  : true;
    $this->assets = new Assets(array(
      'css' => $this->css,
      'js'  => $this->js
    ));

    // Output
    $visible      = !(isset($opt['hide']) and $opt['hide'] === true);
    $this->output = new Output($visible);

    // Elements
    $this->elements = $this->selectElements($opt);
  }


  //====================================
  //   Output
  //====================================

  protected function getOutput() {
    if($this->canUsePanelBar()) {
      $this->hookControls();
      $this->hookElements();
      $this->hookAssets();
      return $this->output->get();

    } elseif($this->canLogin()) {
      $url = $this->panel->urls()->index();
      return $this->output->getLoginIcon($url);
    }
  }

  //====================================
  //   Elements
  //====================================

  protected function selectElements($opt) {
    if(isset($opt['elements']) and is_array($opt['elements'])) {
      return $opt['elements'];
    } else {
      return c::get('panelbar.elements',$this->defaults);
    }
  }

  protected function hookElements() {
    foreach ($this->elements as $id => $element) {
      $this->loadElement($element);

      // $element is standard or plugin element
      if($class  = 'panelBar\\Elements\\'.$element and
         class_exists($class)) {
        $element = $this->getElementObj($class);

      // $element is callable
      } elseif(is_callable($element)) {
        $element = $this->getElementCallable($element);

      // $element is string
      } elseif(is_string($element)) {
        $element = $this->getElementString($element, $id);
      }

      $this->hookElement($element);
    }
  }

  public function loadElement($name) {
    $sources = array(
      __DIR__ . '/../elements' . DS,
      __DIR__ . '/../plugins'  . DS
    );

    foreach($sources as $source) {
      f::load($source . $name . '.php');
      f::load($source . $name . DS . $name . '.php');
    }
  }

  protected function getElementObj($class) {
    $obj = new $class($this);
    return $obj->html();
  }

  protected function getElementCallable($callable) {
    return call_user_func_array($callable, array($this->output, $this->assets));
  }

  protected function getElementString($string, $id) {
    return pattern::element(null, $string, array('id' => $id));
  }

  protected function hookElement($element) {
    // $element has specified various hooks
    if(is_array($element)) {
      if(isset($element['assets'])) {
        $this->assets->setHooks($element['assets']);
      }
      if(isset($element['html'])) {    $this->output->setHooks($element['html']);
      }
      if(isset($element['element'])) {
        $this->output->setHook('elements', $element['element']);
      }

    // $element is only a string
    } else {
      $this->output->setHook('elements', $element);
    }
  }


  //====================================
  //   Controls
  //====================================

  protected function hookControls() {
    $this->output->setHook('next', tpl::load('components/controls'));
  }

  //====================================
  //   Assets
  //====================================

  protected function hookAssets() {
    foreach(array('css', 'js') as $type) {
      if($this->$type !== false) {
        $this->output->setHook('after', $this->assets->$type());
      }
    }
  }

  //====================================
  //   Checks
  //====================================

  protected function canUsePanelBar() {
    return $user = site()->user() and $user->hasPanelAccess();
  }

  protected function canLogin() {
    return c::get('panelbar.login', true);
  }


   //====================================
   //   PLACEHOLDERS for public static methods
   //====================================

  public static function show()               { }
  public static function hide()               { }
  public static function css($args = array()) { }
  public static function js($args = array())  { }
  public static function defaults()           { }

}
