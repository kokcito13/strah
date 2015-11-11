$(document).ready(function(){

    alignMenu();

});

$(window).resize(function() {
    $(".city-menu").append($(".city-menu li.hideshow ul").html());
    $(".city-menu li.hideshow").remove();
    alignMenu();
});

function alignMenu() {
    var w = 0;
    var mw = $(".city-menu").width() - 150;
    var i = -1;
    var menuhtml = '';
    jQuery.each($(".city-menu").children(), function() {
        i++;
        w += $(this).outerWidth(true);
        if (mw < w) {
            menuhtml += $('<div>').append($(this).clone()).html();
            $(this).remove();
        }
    });
    $(".city-menu").append(
        '<li  style="position:relative;" href="#" class="hideshow">'
        + '<a class="show-a">А также '
        + '<span class="arrow-down"></span>'
        + '</a><ul>' + menuhtml + '</ul></li>');
    $(".city-menu li.hideshow ul").css("top",
        $(".city-menu li.hideshow").outerHeight(true) + "px");
    $(".city-menu li.hideshow").click(function() {
        $(this).children("ul").toggle();
        $('.show-a').toggleClass('active');
    });
}