
let first_name,
    second_name,
    email,
    town,
    date,
    image,
    number,
    user_id;

$("html").ready(function () {
    let jwt=getCookie("jwt");
    if(jwt !== undefined) {
        $.ajax({
            url: "../api/user/validate.php",
            type : "POST",
            contentType : 'application/json',
            data : JSON.stringify({ jwt: jwt }),
            success : function(result){
                first_name = result.jwt.first_name;
                second_name = result.jwt.second_name;
                number = result.jwt.number;
                date = result.jwt.date;
                town = result.jwt.town;
                email = result.jwt.email;
                image  =result.jwt.image;
                user_id = result.jwt.id;
                $(document).trigger('load_data');
            },
            error : function(result){
                console.log(result.responseJSON.message);
            }
        })

    }
    else {
        $(location).attr('href',"../");
    }
});