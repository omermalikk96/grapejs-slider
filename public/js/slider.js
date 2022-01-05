for (var i = 0; i < 1; i++) {
  $('iframe').ready(function () {
    var frame = $("iframe").contents();
    console.log($('html', frame).html());
    $('.slider', frame).slider();
    $('select', frame).material_select();
  });
}
