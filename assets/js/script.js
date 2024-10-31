jQuery(document).ready(function ($) {
    $(".uc-open-sidenav").on('click', function (e) {
        e.preventDefault();
        $(".uc-sidenav").addClass("open");
        $(".uc-black-window").show();
    });
    $(".uc-close-sidenav").on('click', function (e) {
        e.preventDefault();
        $(".uc-sidenav").removeClass("open");
        $(".uc-black-window").hide();
    });
});