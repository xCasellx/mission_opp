let hash;
$("html").ready(function () {
    if (getCookie("jwt") !== undefined) {
        $(location).attr('href',"../pages/cabinet.php");
        return false;
    }
    hash = getUrlParameter("hash");
    $.ajax({
        url: "../api/recovery/check-hash.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ hash:hash }),
        success : function(result) {
            $(document).trigger('hash-success');
            console.log(result)
        },
        error : function(result) {
            $(location).attr('href',"../");
            console.log(result)
        }
    })
});

$(document).on("submit", "#recovery-form", function () {
    let form_data = $(this).serializeObject();
    form_data.hash = hash;
    form_data = JSON.stringify(form_data);
    console.log(form_data);
    $.ajax({
        url: "../api/recovery/recovery-password.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,
        success : function(result) {
            $(location).attr('href',"../");
        },
        error : function(result) {
            printMessage("error",result.responseJSON.message);
            console.log(result);
        }
    })
    return false;
});

$(document).on("hash-success", function () {
    deleteMessage();
    let html=`  <form action="/" id="recovery-form" method="post" class="form-check p-0 m-5">
                    <input required type="password" class="form-control" name="password" placeholder="Password">
                    <input required type="password" class="form-control" name="confirm_password" placeholder="Confirm password">
                    <button type="submit" name="submit" class="float-right btn mt-4 mb-3 btn-success"><strong>Sign in</strong></button>
                </form>`
    $("#form").html(html);
});


