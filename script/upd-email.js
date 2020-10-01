let hash;
$(document).on("load_data", function () {
    if (getCookie("jwt") === undefined) {
        $(location).attr('href',"../");
        return false;
    }
    hash = getUrlParameter("hash");
    $.ajax({
        url: "../api/user/upd-email.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ hash: hash, id: user_id}),
        success : function(result) {
            setCookie('jwt',result.jwt,2);
            $("#status-message").html("Email updete");
            $("#message-div").addClass("alert-success");
            console.log(result);
        },
        error : function(result) {
            $("#status-message").html("Email not updetet");
            $("#message-div").addClass("alert-danger");
            console.log(result);
        }
    })
});

