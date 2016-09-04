<?php

namespace panelBar\Elements;

use panelBar\Pattern;
use panelBar\Assets;

class Index extends \panelBar\Element {

  //====================================
  //   HTML output
  //====================================

  public function html() {
    // register assets
    $this->assets->setHook('css', $this->css('index'));

    // return output
    return pattern::dropdown(array(
      'id'     => $this->getElementName(),
      'icon'   => 'th',
      'label'  => 'Index',
      'items'  => $this->items(),
      'class'  => 'panelBar-index',
    ));
  }

  //====================================
  //   Items
  //====================================

  private function items() {
    $home  = $this->site->homePage();
    $index = $this->site->index()->prepend($home->id(), $home);
    $items = array();

    foreach($index as $page) {
      array_push($items, array(
        'label' => $this->tpl('label', array(
          'title'   => $page->title(),
          'num'     => $page->num(),
          'depth'   => $page->depth() - 1,
          'visible' => $page->isVisible()
        )),
        'url'   => $page->url(),
      ));
    }

    return $items;
  }

}