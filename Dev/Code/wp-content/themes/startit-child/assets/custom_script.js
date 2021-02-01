$(document).ready(function () {

$('.ays_score_percent').each(function(){
    $(this).html($(this).html().split("%").join(""));
});

});