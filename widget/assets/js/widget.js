
var panelBarList = $('.panelBar-widget__list');

// =============================================
//  Sortable
// =============================================
var sortable = Sortable.create(panelBarList[0], {
  forceFallback: true,
  filter:        ".panelBar-widget--undraggable",
  chosenClass:   "panelBar-widget__chosen",
  ghostClass:    "panelBar-widget__ghost",
  onUpdate:      setPanelBarElements,

});

// =============================================
//  On checking/unchecking
// =============================================
panelBarList.find('input').change(function(e) {
  var input    = $(this);
  var checkbox = input.parent();
  var item     = checkbox.parent();

  item.toggleClass('panelBar-widget--undraggable');

  var actives  = panelBarList.find(':checked').not(input);
  var insert   = actives.last().parent().parent();

  var float = checkbox.siblings('.controls').find('.float');
  float.attr('data-float', input.is(':checked') ? 'left' : '');

  item.insertAfter(insert);

  if(!input.is(':checked')) sortPanelBarElements();

  setPanelBarElements();
});

function sortPanelBarElements() {
  $(".panelBar-widget--undraggable").sort(function(a, b) {
    return ($(b).find('.name').text()) < ($(a).find('.name').text()) ? 1 : -1;
  }).appendTo('.panelBar-widget__list');
}

// =============================================
//  On float setting
// =============================================
panelBarList.find('.float > a').click(function(e) {
  e.preventDefault();
  var el = $(this);
  el.parent().attr('data-float', el.index() === 0 ? 'left' : 'right');
  setPanelBarElements();
  return false;
});


// =============================================
//  Set API call
// =============================================
function setPanelBarElements() {
  var data = generatePanelBarConfig();
  $.post(setPanelBarURL, data);
}

function generatePanelBarConfig() {
  var data = { 'elements' : []};

  panelBarList.find(":checked").each(function() {
    var el    = $(this);
    var float = el.parent().siblings('.controls').find('.float');

    data.elements.push({
      element: el.val(),
      float:   float.attr('data-float') == 'right' ? 'right' : null
    });
  });

  return data;
}

// =============================================
//  Reset button
// =============================================
panelBarList.prev('h2').find('a').click(function(e) {
  e.preventDefault();
  $.post($(this).attr("href"));
  location.reload();
  return false;
});
