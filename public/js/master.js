$(function(){
  console.log('here');
  $("p.desc").text(function(index, currentText) {
    if($.trim(currentText).length > 0) {
      return currentText.substr(0, 175) + '...';
    }
  });
});