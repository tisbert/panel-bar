(function (panelBar) {
  var add = 'addEventListener';
  var rmv = 'removeEventListener';

  var setOverlay = function(show) {
    _.dom.buttons.all.style.display = show ? 'inline-block' : 'none';
    _.dom.wrapper.style.display     = show ? 'block'        : 'none';
    document.body.style.overflow    = show ? 'hidden'       : 'auto';

    var event = show ? add : rmv;
    _.dom.buttons.return[event]('click', _.show);
    _.dom.buttons.refresh[event]('click', refresh);
  };

  var setPanelbar = function(clear) {
    var elements = _.dom.elements;
    var i;
    for (i = 0; i < elements.length; i++) {
      elements[i].style.display = clear ? 'none' : 'inline-block';
    }

    var ctrl  = panelBar.dom.controls;
    var ctrlb = ctrl.visible.children[0];
    ctrl.position.style.display = clear ? 'none' : '';
    cl[ clear ? 'add' : 'remove'](ctrlb, 'fa-thumb-tack');
    cl[!clear ? 'add' : 'remove'](ctrlb, 'fa-times-circle');
    ctrl.visible[clear ? add : rmv]('click', redirect);
  };

  var setPosition = function(clear) {
    var position = panelBar.status.position;
    panelBar[clear ? 'top' : _.status.position]();
    _.status.position = clear ? position : null;
  };

  var refresh  = function() { location.reload(); };
  var redirect = function() {
    location.href = _.dom.iframe.src;
    panelBar.show();
  };

  var isLoaded = function() {
    _.dom.iframe.addEventListener("load", function() {
      var body = _.dom.iframe.contentDocument.querySelector('body.app');

      if(typeof body !== undefined) loadingScreen('');
      else setTimeout(redirect, 4000);
    });

    // wait and check if loading got cleared, if not redirect
    setTimeout(timeout, 1500);
  };

  var timeout = function() {
    if(_.dom.loading.innerHTML !== '') {
      _.loading.innerHTML = _.message.loadingFailed;
      setTimeout(function() {
        location.href = url;
      }, 2000);
    }
  };

  var loadingScreen = function(msg) {
    _.dom.loading.innerHTML = msg;
  };

  var parent  = panelBar.dom;
  var wrapper = parent.wrapper.querySelector(".panelBar-iframe__iframe");
  var buttons = parent.bar.querySelector(".panelBar-iframe__btns");

  panelBar.iframe = {
    dom : {
      wrapper:    wrapper,
      iframe:     wrapper.children[1],
      loading:    wrapper.children[0],
      elements:   parent.wrapper.querySelectorAll('.panelBar__bar > div'),
      buttons: {
        all:      buttons,
        return:   buttons.children[0],
        refresh:  buttons.children[1],
      },
    },

    status : {
      active:     false,
      position:   null,
      supported:  true,
    },

    message : {
      loadingFailed: 'Seems like something is blocking access to the panel inside an iframe. Redirecting…',
    },

    bind : function(element) {
      var links = panelBar.dom.bar.querySelectorAll(element);
      var i;
      for (i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function(e) {
          if(_.status.supported) {
            e.preventDefault();
            _.show(this.href);
          }
        });
      }
    },

    show : function(url) {
      _.status.active = /^(f|ht)tps?:\/\//i.test(url);
      setPosition(_.status.active);
      setPanelbar(_.status.active);
      setOverlay(_.status.active);
      _.load(url);
    },

    load : function(url) {
      // start loading
       loadingScreen('Loading…');
      _.dom.iframe.src = _.status.active ? url : '';

      isLoaded();
    },

    isSupported : function() {
      var testFrame = document.createElement('iframe');
      testFrame.id            = 'panelBarJStestFrame';
      testFrame.src           = siteURL + '/panel/';
      testFrame.style.display = 'none';
      document.body.appendChild(testFrame);
      
      testFrame.addEventListener("load", function() {
        var body = testFrame.contentDocument.querySelector('body.app');
        if(typeof body !== undefined) document.body.removeChild(testFrame);
      });

      setTimeout(function() {
        _.status.supported = document.getElementById('panelBarJStestFrame') === null;
      }, 1000);
    }
  };

  var _ = panelBar.iframe;

})(panelBar);

panelBar.iframe.isSupported();
