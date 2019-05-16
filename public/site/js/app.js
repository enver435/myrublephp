function guid() {
    var nav = window.navigator;
    var screen = window.screen;
    var guid = nav.mimeTypes.length;
    guid += nav.userAgent.replace(/\D+/g, '');
    guid += nav.plugins.length;
    guid += screen.height || '';
    guid += screen.width || '';
    guid += screen.pixelDepth || '';

    return guid;
};

// function setCookie(cname, cvalue, exdays) {
//     var d = new Date();
//     d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
//     var expires = "expires=" + d.toUTCString();
//     document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
// }

// function getCookie(cname) {
//     var name = cname + "=";
//     var ca = document.cookie.split(';');
//     for (var i = 0; i < ca.length; i++) {
//         var c = ca[i];
//         while (c.charAt(0) == ' ') {
//             c = c.substring(1);
//         }
//         if (c.indexOf(name) == 0) {
//             return c.substring(name.length, c.length);
//         }
//     }
//     return "";
// }

if ($('.register').length > 0) {
    $('.register input[name=guid]').val(guid());

    function checkReg(e) {
        var guid = $('.register input[name=guid]').val();
        if (guid == guid()) {
            $('.register form').submit();
        } else {
            e.preventDefault();
        }
    }
}