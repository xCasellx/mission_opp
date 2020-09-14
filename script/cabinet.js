$("html").ready(function () {
    if(getCookie("jwt") === undefined){
        $(location).attr('href',"../");
    }
});

$(document).ready(function () {
    let jwt=getCookie("jwt");
    if(jwt !== undefined) {
        $.ajax({
            url: "../api/user/validate.php",
            type : "POST",
            contentType : 'application/json',
            data : JSON.stringify({ jwt:jwt }),
            success : function(result){
                $("#user_first_name").text(result.jwt.first_name);
                $("#user_second_name").text(result.jwt.second_name);
                $("#user_number").text(result.jwt.number);
                $("#user_date").text(result.jwt.date);
                $("#user_town").text(result.jwt.town);
                $("#user_email").text(result.jwt.email);
            },
            error : function(result){
                console.log(result.responseJSON.message);
            }
        })

    }
});

$("#sign-out").on("click",function() {
    setCookie("jwt","",-1);
    $(location).attr('href',"../");
});