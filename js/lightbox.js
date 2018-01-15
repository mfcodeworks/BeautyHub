// Get the modal
var modal = $('#myModal');
var modalImg = $("#myModalImg");

$('.product-modal').click( function(){
    modal.show();
    modalImg.attr('src',$(this).attr('src'));

    // When the user clicks on <span> (x), close the modal
    modalImg.click( function() {
      modal.hide();
    });
});
