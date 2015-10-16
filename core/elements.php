<?php

namespace PanelBar;

use C;

class Elements {

  public $site;
  public $page;

  protected $output;
  protected $templates;
  protected $assets;
  protected $css;
  protected $js;

  public function __construct($output, $assets) {
    $this->site   = site();
    $this->page   = page();

    $this->output = $output;
    $this->assets = $assets;
  }


  /**
   *  PANEL
   */

  public function panel() {
    return Build::link(array(
      'id'      => __FUNCTION__,
      'icon'    => 'cogs',
      'url'     => tools::url(''),
      'label'   => '<span class="in-compact">Go to </span>Panel',
      'title'   => 'Alt + P',
      'compact' => true,
    ));
  }

  public function index() {
    // register assets
    $this->assets->setHook('css', tools::load('css', 'elements/index.css'));

    // prepare output
    $items = array();
    $home  = $this->site->homePage();
    $index = $this->site->index()->prepend($home->id(), $home);

    foreach($index as $page) {
      array_push($items, array(
        'label' => tools::load('html', 'elements/index/label.php', array(
          'title'   => $page->title(),
          'num'     => $page->num(),
          'depth'   => $page->depth() - 1,
          'visible' => $page->isVisible()
        )),
        'url'   => $page->url(),
      ));
    }

    return Build::dropdown(array(
      'id'     => __FUNCTION__,
      'icon'   => 'th',
      'label'  => 'Index',
      'items'  => $items,
      'class'  => 'panelbar-index',
    ));


  }


  /**
   *  ADD
   */

  public function add() {
    $this->_registerIframe();

    return Build::dropdown(array(
      'id'     => __FUNCTION__,
      'icon'   => 'plus',
      'label'  => 'Add',
      'items'  => array(
        'child' => array(
          'url'   => tools::url('add', $this->page),
          'label' => 'Child',
        ),
        'sibling' => array(
          'url'   => tools::url('add', $this->page->parent()),
          'label' => 'Sibling',
        ),
      ),
    ));
  }


  /**
   *  EDIT
   */

  public function edit() {
    $this->_registerIframe();

    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'pencil',
      'url'    => tools::url('show', $this->page),
      'label'  => 'Edit',
      'title'  => 'Alt + E',
    ));
  }


  /**
   *  TOGGLE
   */

  public function toggle() {
    // register assets
    $this->assets->setHook('css', tools::load('css', 'elements/toggle.css'));

    if(!tools::version("2.2.0")) {
      $js = 'currentURI="'.$this->page->uri().'";siteURL="'.$this->site->url().'";';
      $this->assets->setHook('js',  tools::load('js', 'elements/toggle.min.js'));
      $this->assets->setHook('js',  $js);
    } else {
      $this->_registerIframe();
      $this->assets->setHook('js',  'panelbarIframe.init([".panelbar--toggle a"]);');
    }


    if($this->page->isInvisible() and !tools::version("2.2.0")) {
      // prepare output
      $siblings = array();
      array_push($siblings, array(
        'url'   => tools::url('toggle', $this->page),
        'label' => '&rarr;<span class="gap"></span>&larr;',
        'title' => 'Publish page at this position'
      ));
      foreach ($this->page->siblings()->visible() as $sibling) {
        array_push($siblings, array('label' => $sibling->title()));
        array_push($siblings, array(
          'url'   => tools::url('toggle', $this->page),
          'label' => '&rarr;<span class="gap"></span>&larr;',
          'title' => 'Publish page at this position'
        ));
      }

      return Build::dropdown(array(
        'id'     => __FUNCTION__,
        'icon'   => 'toggle-off',
        'label'  => 'Invisible',
        'items'  => $siblings,
      ));

    } else {
      return Build::link(array(
        'id'     => __FUNCTION__,
        'icon'   => $this->page->isVisible() ? 'toggle-on' : 'toggle-off',
        'label'  => $this->page->isVisible() ? 'Visible' : 'Invisible',
        'url'    => tools::url('toggle', $this->page),
      ));
    }
  }


  /**
   *  FILES
   */

  public function files($type = null, $function = null) {
    if ($files = $this->_files($type)) {
      $this->_registerIframe();

      if    (count($files) > 12)  $count = '12more';
      elseif(count($files) > 2)   $count = 'default';
      elseif(count($files) == 2)  $count = '2';
      elseif(count($files) == 1)  $count = '1';

      return Build::fileviewer(array(
        'id'     => is_null($function) ? __FUNCTION__ : $function,
        'icon'   => ($type == 'image') ? 'photo' : 'file',
        'label'  => ($type == 'image') ? 'Images' : 'Files',
        'items'  => $files,
        'count'  => 'panelbar-fileviewer--' . $count,
        'all'    => tools::url('index', $this->page->files()->first()),
      ));
    }
  }


  /**
   *  IMAGES
   */

  public function images() {
    return $this->files('image', __FUNCTION__);
  }


  /**
   *  FILELIST
   */

  public function filelist($type = null, $function = null) {
    if ($files = $this->_files($type)) {
      $this->_registerIframe();

      return Build::filelist(array(
        'id'     => is_null($function) ? __FUNCTION__ : $function,
        'icon'   => 'th-list',
        'label'  => ($type == 'image') ? 'Images' : 'Files',
        'items'  => $files,
        'all'    => tools::url('index', $this->page->files()->first()),
      ));
    }
  }


  /**
   *  IMAGELIST
   */

  public function imagelist() {
    return $this->filelist('image', __FUNCTION__);
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

      // register output
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
    $this->_registerIframe();

    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'user',
      'url'    => tools::url('edit', $this->site->user()),
      'label'  => $this->site->user(),
      'float'  => 'right',
    ));
  }


  /**
   *  LOGOUT
   */

  public function logout() {
    return Build::link(array(
      'id'     => __FUNCTION__,
      'icon'   => 'power-off',
      'url'    => tools::url('logout'),
      'label'  => 'Logout',
      'float'  => 'right',
    ));
  }



  /**
   *  TOOL: iFrame
   */

  private function _registerIframe() {
    if(c::get('panelbar.enhancedJS', true)) {
      // register assets
      $this->assets->setHook('js', 'siteURL="'.$this->site->url().'";');
      $this->assets->setHook('js',  tools::load('js',  'components/iframe.min.js'));
      $this->assets->setHook('css', tools::load('css', 'components/iframe.css'));
      // register output
      $this->output->setHook('before',   tools::load('html', 'iframe/iframe.php'));
      $this->output->setHook('elements', tools::load('html', 'iframe/btn.php'));
    }
  }


  /**
   *  TOOL: Files
   */

  private function _files($type = null) {
    $files = $this->page->files();
    if (!is_null($type)) {
      $files = $files->filterBy('type', '==', $type);
    }

    if ($files->count() > 0) {
      $items = array();
      foreach($files as $file) {
        $args = array(
          'type'      => $file->type(),
          'url'       => tools::url('show', $file),
          'label'     => $file->name(),
          'extension' => $file->extension(),
          'size'      => $file->niceSize(),
        );
        if ($file->type() == 'image') $args['image']  = $file->url();
        array_push($items, $args);
      }
      return $items;

    } else {
      return false;
    }
  }


}
