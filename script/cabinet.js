$(document).on("load_data", function () {
    $("#cabinet-page").addClass("active font-weight-bold");
    $("#user_first_name").text(first_name);
    $("#user_second_name").text(second_name);
    $("#user_number").text(number);
    $("#user_date").text(date);
    $("#user_town").text(town);
    $("#user_email").text(email);
    $("#user-image").attr("src", image+"?"+ Math.random());
    if(email_confirm === "0") {
        let html = `
          <div class="alert-danger p-2" id="main-message">
            <a href="#" class="text-decoration-none text-danger" id="email-confirm">Click to confirm mail</a>  
          </div>
        `;
    $("main").prepend(html);
    }
});

$(document).on("click", "#email-confirm", function () {
    $( document ).off( "click", "#email-confirm" );
    $.ajax({
        url: "../api/user/hash-confirm-email.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ email: email}),
        success : function(result) {
            $("#main-message").html("A message has been sent to the mail");
        },
        error : function(result) {
            $("#main-message").html("A message has been sent to the mail,failed to send message");
        }
    })
});


$("#user-image").error(function() {
    $(this).attr('src', '/api/image/nan.png?' + Math.random());
});

$(document).on("click","#open-edit-data",function () {
    $(".edit-data").toggleClass("d-none");
    $("#edit-image").toggleClass("d-none");
    if ($("#open-edit-data").text()=="Edit") {
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

$("#edit-second_name").on("click", function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let html=`<input required type="text" class="form-control border-dark border input-text input-edit" 
              id="input-second_name" name="edit_text" placeholder='` +second_name+ `'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit second name");
});
$("#edit-first_name").on("click", function () {
    edit_component = $(this).attr('id').replace("edit-", "");
    let html=`<input required type="text" class="form-control border-dark border input-text input-edit" 
              id="input-first_name" name="edit_text" placeholder='` +first_name+ `'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit first name");
});
$("#edit-number").on("click", function () {
    edit_component = $(this).attr('id').replace("edit-", "");
    let html=`<input required type="text" name="edit_text"  class="form-control border-dark border input-text input-edit" 
              id="input-number" placeholder='` +number+ `' pattern="[0-9]{10,15}">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit number");
});

$("#edit-date").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-", "");
    let html=`<input required type="date" name="edit_text"  class="form-control border-dark border input-text input-edit" 
             value="`+date+`" id="input-date" max='` +maxDate(8)+ `'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit date");
});

$("#edit-town").on("click",function () {
    loadListCountry();
    edit_component = $(this).attr('id').replace("edit-","");
    let html=`<select required id="country" class="border border-dark col mt-1 custom-select"></select>
            <select required id="region" class="border border-dark col mt-1 custom-select"></select>
            <select required id="city" class="border border-dark col mt-1 custom-select"></select>`
    $("#form-content").html(html);
    $(".modal-title").text("Edit town");
});
$(document).on("change","#country", function () {
    $("#city").empty();
    loadListRegion();
});

$(document).on("change","#region", function () {
    loadListCity();
});

$("#edit-email").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-","");
    let html=`<input required type="password" name="password" class="mt-2 input-text border-dark border form-control" placeholder="Password"  id="input-password">
              <input required type="email" name="edit_text"  class="mt-2 form-control border-dark border input-text input-edit" 
              id="input-email" placeholder='`+email+`'">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit email");
});

$("#edit-image").on("click",function () {
    edit_component = $(this).attr('id').replace("edit-", "");
    let html = `  <div class="container" align="center">
                            <div class="status-message d-none text-center mt-2 p-2"></div>
                            <form enctype="multipart/form-data" action="" id="edit-form-image" method="post">
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
    edit_component = $(this).attr('id').replace("edit-", "");
    let html=`<input required type="password" name="password" class="mt-2 input-text border-dark border form-control" placeholder="Password"  id="input-password">
              <input required type="password" name="edit_text" class="mt-2 input-text border-dark border form-control" placeholder="New password"  id="input-new-password">
              <input required type="password" name="confirm_password" class="mt-2 input-text border-dark border form-control" placeholder="Confirm password" id="input-confirm-password">`
    $("#form-content").html(html);
    $(".modal-title").text("Edit password");
});

$(document).on("submit","#edit-form-image",function () {
    let form_data = new FormData($(this)[0]);
    form_data.append("jwt", getCookie("jwt"));
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
            setCookie("jwt", result.jwt, "2");
            image = result.image;
            $("#user-image").attr("src", image+"?"+Math.random());
            $('#myModal').modal('hide');
        },
        error:function (result){
            console.log(result.responseJSON.message);
            printMessage("error", result.responseJSON.message);
        }
    });
    return false;
});

$(document).on("submit","#edit-form",function () {
    let form = $(this);
    let form_obj = form.serializeObject();
    form_obj.jwt = getCookie("jwt");
    if (edit_component === "town") {
        form_obj.edit_text = $('#city option:selected').attr("id").replace("city-","");
    }
    form_obj.edit_name = edit_component;
    let form_data = JSON.stringify(form_obj);
    $.ajax({
        url: "../api/user/update.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,
        success : function(result){
            console.log(result);
            setCookie("jwt",result.jwt,"2");
            if (edit_component === "town") {
                $("#user_"+edit_component).text($("#city").val()+","+$("#region").val()+","+$("#country").val());
            }
            else if(edit_component === "email") {
                let html = `
                  <div class="alert-success p-2" id="message">
                       A message has been sent to the mail 
                  </div>
                `;
                $("main").prepend(html);
            }
            else  {
                $("#user_"+edit_component).text(form_obj.edit_text);
                first_name = $("#user_first_name").text();
                second_name = $("#user_second_name").text();
                number = $("#user_number").text();
                date = $("#user_date").text();
                town = $("#user_town").text();
                email = $("#user_email").text();
            }

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

