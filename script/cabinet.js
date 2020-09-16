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
                if(result.jwt.image !== null){
                    $.ajax(result.jwt.image, {
                        success: function() {
                            $("#user-image").attr("src",result.jwt.image);
                        },
                        method: "HEAD"
                    });
                }
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


$(document).on("click","#open-edit-data",function () {
    $(".edit-data").toggleClass("d-none");
    $("#edit-image").toggleClass("d-none");
    if($("#open-edit-data").text()=="Edit") {
        $("#open-edit-data").text("Cancel");
    }
    else {
        $("#open-edit-data").text("Edit");
    }

});
let edit_component;



$(".edit-data").on("click",function (){
    let html = `  <div class="container" align="center">
                            <div class="status-message d-none text-center mt-2 p-2"></div>
                            <form action="" id="edit-form" method="post">
                                <div id="form-content" class="m-4"></div>
                                <button type="submit"  id="save_button" class="btn btn-outline-success" ><strong>Save</strong></button>
                                <button type="button"  class="btn btn-outline-danger" data-dismiss="modal"><strong>Close</strong></button>
                            </form>
                        </div>`
    $(".modal-body").html(html);
})

$("#edit-second_name").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="text" class="form-control border-dark border input-text input-edit" 
              id="input-second_name" name="edit_text" placeholder='`+content+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit second name");
});
$("#edit-first_name").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="text" class="form-control border-dark border input-text input-edit" 
              id="input-first_name" name="edit_text" placeholder='`+content+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit first name");
});
$("#edit-number").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="text" name="edit_text"  class="form-control border-dark border input-text input-edit" 
              id="input-number" placeholder='`+content+`' pattern="[0-9]{10,15}">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit number");
});


$("#edit-date").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="date" name="edit_text"  class="form-control border-dark border input-text input-edit" 
             value="`+content+`" id="input-date" max='`+maxDate(8)+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit date");
});


$("#edit-town").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="text" name="edit_text" class="form-control border-dark border input-text input-edit" 
              id="input-town" placeholder='`+content+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit town");
});


$("#edit-email").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="email" name="edit_text"  class="form-control border-dark border input-text input-edit" 
              id="input-email" placeholder='`+content+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit email");
});

$("#edit-image").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let html = `  <div class="container" align="center">
                            <div class="status-message d-none text-center mt-2 p-2"></div>
                            <form action="" id="edit-form-image" method="post">
                                <div id="form-content" class="m-4">
                                    <input required name='image' type="file" class="input-edit input-text" id="input-image" accept="image/jpeg,image/png,image/gif">
                                </div>
                                <button type="submit"  id="save_button" class="btn btn-outline-success" ><strong>Save</strong></button>
                                <button type="button"  class="btn btn-outline-danger" data-dismiss="modal"><strong>Close</strong></button>
                            </form>
                        </div>`
    $(".modal-body").html(html);
    $(".modal-title").text("Edit image");

});

$("#edit-password").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let content=$("#user_"+edit_component).text();
    let html=`<input required type="password" name="password" class="mt-2 input-text border-dark border form-control" placeholder="Password"  id="input-password">
              <input required type="password" name="edit_text" class="mt-2 input-text border-dark border form-control" placeholder="New password"  id="input-new-password">
              <input required type="password" name="confirm_password" class="mt-2 input-text border-dark border form-control" placeholder="Confirm password" id="input-confirm-password">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit password");
});

$(document).on("submit","#edit-form-image",function () {
    let form_data = new FormData($(this)[0]);
    form_data.append("jwt",getCookie("jwt"));
    $.ajax({
        url: "../api/user/image.php",
        type: "POST",
        dataType : "json",
        cache: false,
        contentType: false,
        processData: false,
        data:form_data,
        success:function (result){
            console.log(result);
            setCookie("jwt",result.jwt,"2");
            $("#user-image").attr("src",result.image);
            $('#myModal').modal('hide');
        },
        error:function (result){
            console.log(result.responseJSON.message);
            printMessage("error",result.responseJSON.message);
        }
    });
    return false;
});

$(document).on("submit","#edit-form",function () {
    let form = $(this);
    let form_obj=form.serializeObject();
    form_obj.jwt=getCookie("jwt");
    form_obj.edit_name= edit_component;
    let form_data=JSON.stringify(form_obj);
    $.ajax({
        url: "../api/user/update.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,
        success : function(result){
            console.log(result);
            setCookie("jwt",result.jwt,"2");
            $("#user_"+edit_component).text(form_obj.edit_text);
            $('#myModal').modal('hide');
        },
        error : function(result){
            console.log(result.responseJSON.message);
            printMessage("error",result.responseJSON.message);
        }
    })
    return false;
});


$('#myModal').on('hide.bs.modal', function() {
    deleteMessage();
});

