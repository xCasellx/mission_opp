let hash;
$(document).on("load_data", function () {
    if (getCookie("jwt") === undefined) {
        $(location).attr('href',"../");
        return false;
    }
    hash = getUrlParameter("hash");
    $.ajax({
        url: "../api/user/confirm-email.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ hash: hash, id: user_id}),
        success : function(result) {
            setCookie('jwt',result.jwt,2);
        },
        error : function(result) {
        }
    })
});

