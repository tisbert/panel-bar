<?php

namespace Kirby\Plugins\distantnative\panelBar\Elements;

use C;

class Edit extends Element {

  //====================================
  //   Output
  //====================================

  public function render() {
    // register iFrame output and assets
    $this->withFrame();

    // return pattern output
    return $this->pattern('link', [
      'id'    => $this->name(),
      'label' => 'Edit',
      'icon'  => 'pencil',
      'url'   => $this->page->url('edit'),
      'title' => c::get('plugin.panelBar.keys', true) ? 'Alt + M' : null,
    ]);
  }

}
