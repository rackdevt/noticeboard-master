var $container = $('#gnb-advert-cards');

$( document ).ready(function() {


// initialize
$container.masonry({
  columnWidth: 100,
  itemSelector: '.gnb-item',
  isFitWidth: true,
  isAnimated: true 
});

});
