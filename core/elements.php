<?php

namespace panelBar;

use C;

class Elements {

  private $panel;
  private $output;
  private $assets;
  private $css;
  private $js;

  public function __construct($page, $output, $assets) {
    $this->output = $output;
    $this->assets = $assets;

    $this->panel  = panel();
    $this->site   = $this->panel->site();
    $this->page   = $this->panel->page($page->id());
  }


  /**
   *  PANEL
   */

  public function panel() {
    // register assets
    $this->_registerIframe(__FUNCTION__);

    // return output
    return Build::link(array(
      'id'      => __FUNCTION__,
      'icon'    => 'cogs',
      'url'     => purl(),
      'label'   => '<span class="in-compact">Go to </span>Panel',
      'compact' => true,
    ));
  }

  public function index() {
    // register assets
    $this->assets->setHook('css', tools::load('css', 'elements/index'));

    // prepare output
    $home  = $this->site->homePage();
    $index = $this->site->index()->prepend($home->id(), $home);
    $items = array();

    foreach($index as $page) {
      array_push($items, array(
        'label' => tools::load('html', 'elements/index/label', array(
          'title'   => $page->title(),
          'num'     => $page->num(),
          'depth'   => $page->depth() - 1,
          'visible' => $page->isVisible()
        )),
        'url'   => $page->url(),
      ));
    }

    // return output
    return Build::dropdown(array(
      'id'     => __FUNCTION__,
      'icon'   => 'th',
      'label'  => 'Index',
      'items'  => $items,
      'class'  => 'panelBar-index',
    ));


  }


  /**
   *  ADD
   */

  public function add() {
    // register assets
    $this->_registerIframe(__FUNCTION__);

    // return output
    return Build::dropdown(array(
      'id'     => __FUNCTION__,
      'icon'   => 'plus',
      'label'  => 'Add',
      'items'  => array(
        'child' => array(
          'url'   => $this->page->url('add'),
          'label' => 'Child',
        ),
        'sibling' => array(
          'url'   => $this->page->parent()->url('add'),
          'label' => 'Sibling',
        ),
      ),
    ));
  }


  /**
   *  EDIT
   */

  public function edit() {
    // register assets
    $this->_registerIframe(__FUNCTION__);

    // return output
    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'pencil',
      'url'    => $this->page->url('edit'),
      'label'  => 'Edit',
      'title'  => 'Alt + E',
    ));
  }


  /**
   *  TOGGLE
   */

  public function toggle() {
    // register assets
    $this->_registerIframe(__FUNCTION__);

    // return output
    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => $this->page->isVisible() ? 'toggle-on' : 'toggle-off',
      'label'  => $this->page->isVisible() ? 'Visible'   : 'Invisible',
      'url'    =>$this->page->url('toggle'),
    ));
  }


  /**
   *  IMAGES
   */

  public function images($type = 'image', $function = __FUNCTION__) {
    if($images = $this->_files($type)) {
      // register assets
      $this->_registerIframe($function);

      // prepare output
      if    (count($images) > 12)  $count = '12more';
      elseif(count($images) > 2)   $count = 'default';
      elseif(count($images) == 2)  $count = '2';
      elseif(count($images) == 1)  $count = '1';

      // return output
      return Build::images(array(
        'id'     => $function,
        'icon'   => ($type == 'image') ? 'photo'  : 'file',
        'label'  => ($type == 'image') ? 'Images' : 'Files',
        'items'  => $images,
        'count'  => 'panelBar-images--' . $count,
        'all'    => $this->page->files()->first()->url('index'),
      ));
    }
  }


  /**
   *  FILEVIEW
   */

  public function fileview() {
    return $this->images(null, __FUNCTION__);
  }


  /**
   *  FILES
   */

  public function files($type = null, $function = __FUNCTION__) {
    if($files = $this->_files($type)) {
      // register assets
      $this->_registerIframe($function);

      // return output
      return Build::files(array(
        'id'     => $function,
        'icon'   => 'th-list',
        'label'  => ($type == 'image') ? 'Images' : 'Files',
        'items'  => $files,
        'all'    => $this->page->url('files'),
      ));
    }
  }


  /**
   *  IMAGELIST
   */

  public function imagelist() {
    return $this->files('image', __FUNCTION__);
  }


  /**
   *  LANGUAGES
   */

  public function languages() {
    if ($languages = $this->site->languages()) {
      // prepare output
      $items = array();
      foreach($languages->not($this->site->language()->code()) as $language) {
        array_push($items, array(
          'url'   => $language->url() . '/' . $this->page->uri(),
          'label' => strtoupper($language->code()),
        ));
      }

      // return output
      return Build::dropdown(array(
        'id'      => __FUNCTION__,
        'icon'    => 'flag',
        'label'   => strtoupper($this->site->language()->code()),
        'items'   => $items,
        'mobile'  => 'label',
      ));
    }
  }


  /**
   *  LOADTIME
   */

  public function loadtime() {
    // return output
    return Build::label(array(
      'id'     => __FUNCTION__,
      'icon'   => 'clock-o',
      'label'  => number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 2),
      'mobile' => 'label',
    ));
  }


  /**
   *  USER
   */

  public function user() {
    // register assets
    $this->_registerIframe(__FUNCTION__);

    // return output
    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'user',
      'url'    => $this->site->user()->url('edit'),
      'label'  => $this->site->user(),
      'float'  => 'right',
    ));
  }


  /**
   *  LOGOUT
   */

  public function logout() {
    // return output
    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'power-off',
      'url'    => $this->panel->urls()->logout(),
      'label'  => 'Logout',
      'float'  => 'right',
    ));
  }



  /**
   *  TOOL: iFrame
   */

  private function _registerIframe($element) {
    if(c::get('panelbar.enhancedJS', true)) {
      // register assets
      $this->assets->setHook('js', 'siteURL="'.$this->site->url().'";');
      $this->assets->setHook('js',  tools::load('js',  'components/iframe.min'));
      $this->assets->setHook('js',  'pbIframe.add(".panelBar--' . $element . ' a");');
      $this->assets->setHook('css', tools::load('css', 'components/iframe'));
      // register output
      $this->output->setHook('before',   tools::load('html', 'iframe/iframe'));
      $this->output->setHook('elements', tools::load('html', 'iframe/btn'));
    }
  }


  /**
   *  TOOL: Files
   */

  private function _files($type = null) {
    // get files collection
    $files = $this->page->files()->sortBy('extension', 'asc', 'name', 'asc');
    if (!is_null($type)) $files = $files->filterBy('type', '==', $type);

    if ($files->count() > 0) {
      // prepare output
      $items = array();
      foreach($files as $file) {
        $args = array(
          'type'      => $file->type(),
          'url'       => $file->url('edit'),
          'label'     => $file->name(),
          'extension' => $file->extension(),
          'size'      => $file->niceSize(),
        );

        if($file->type() == 'image') $args['image']  = $file->url();
        else                         $args['icon']   = $this->_fileicon($file);
        array_push($items, $args);
      }
      return $items;

    } else {
      return false;
    }
  }

  private function _fileicon($file) {
    switch($file->type()) {
      case 'archive':
        return 'file-archive-o';
        break;
      case 'code':
        return 'code';
        break;
      case 'audio':
        return 'volume-up';
        break;
      case 'video':
        return 'film';
        break;
      case 'document':
        switch ($file->extension()) {
          case 'pdf':
            return 'file-pdf-o';
            break;
          case 'doc':
          case 'docx':
            return 'file-word-o';
            break;
          case 'xls':
          case 'xlsx':
            return 'file-excel-o';
            break;
          case 'ppt':
          case 'pptx':
            return 'file-powerpoint-o';
            break;
          default:
            return 'file-text-o';
            break;
        }
        break;

      default:
        return 'file-o';
        break;
    }
  }

}
