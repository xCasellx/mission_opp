$(document).on("load_data", function () {
    let jwt = getCookie("jwt");
    $.ajax( {
        url: "../api/comment/load.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ jwt:jwt } ),
        success:function (result) {
            result.forEach(element => {
                PrintComment(element);
                $(".comment-img").error(function() {
                    $(this).attr('src', '/api/image/nan.png?' + Math.random());
                });
                });
        },
        error : function(data) {
            console.log(data.responseJSON.message);
            }
        });
});




function PrintComment(comment) {
    let edit = (comment.edit_check === "1") ? "(edited) " : "";
    let config_p = ( comment.user_id === user_id ) ? `
        <a class="text-warning edit-comment" href="#" id="edit-comment-` + comment.id + `" data-toggle='modal' data-target='#myModal'><strong>edit</strong></a>
        <a class="text-danger delete-comment" href="#" id="delete-comment-` + comment.id + `" data-toggle='modal' data-target='#myModal'><strong>delete</strong></a>` : ``;
    let img = (comment.user.image !== null)? comment.user.image : "/api/image/nan.png"
    let html =`
                <div class = 'card p-0 ' id="comments-` +comment.id + `">
                    <div class =' p-1 card-header bg-dark text-light row' >
                        <div class ='col-1 p-0' style="max-width: 32px">
                            <img class ='comment-img rounded m-0 img-fluid img' src='`+img+`' style='width: 32px;height: 32px;' alt=''>
                        </div>
                        <h6 class='col '>`+ comment.user.first_name +` `+comment.user.second_name+`</h6>
                        <small class='col text-right date-comment'><lable>`+ edit +`</lable> `+ comment.date +`</small>
                    </div>
                    <div class='p-1 card-body' id="comments-text-` + comment.id + `">
                       `+comment.text+`
                    </div>
                    <div class='p-1 pr-2 m-0 card-footer bg-dark text-right'>
                        <a href='#' class='off comment_id m-0 p-0 text-success' id='`+comment.id+`' data-toggle='modal' data-target='#myModal'><strong>response</strong></a>`
                            + config_p + `
                    </div>
                    <div class="ml-3 border-left border-dark" id="comments`+comment.id+`"></div>
                </div>`
    if(comment.parent_id === null) {

        $('#comments').append(html);
    }
    else {
        $(('#comments'+comment.parent_id)).append(html);
    }

}
let parent_id = null;
let edit_id;
let delete_id;

$(document).on("click", ".edit-comment", function () {
    edit_id = $(this).attr('id');
    edit_id = edit_id.replace("edit-comment-", "");
    let comment_text = $("#comments-text-"+edit_id).text().trim();
     let html =`<form action="#" id="edit-comment" method="post">
        <textarea required class="form-control border-dark border" maxlength="500" id="modal_comment" name="text" rows="10" cols="70">`+ comment_text +`</textarea>
        <div class="mt-2 float-right">
            <button type="submit"   id="edit_button" class="off btn btn-success" ><strong>Save </strong></button>
            <button type="button" class=" btn btn-danger" data-dismiss="modal"><strong>Close</strong></button>
        </div>
     </form>`
    $(".modal-title").text("Edit comment");
    $(".modal-body").html(html);
});

$(document).on("click", ".delete-comment", function () {
    delete_id = $(this).attr('id').replace("delete-comment-", "");
    let html =`
            <button type="button"   id="delete-button" class="m-auto off btn btn-danger " ><strong>Delete</strong></button>
            <button type="button" class="m-auto btn btn-success" data-dismiss="modal"><strong>Close</strong></button>`
    $(".modal-title").text("DELETE");
    $(".modal-body").html(html);
    return false;
});

$(document).on("click", ".comment_id", function () {
    parent_id = $(this).attr('id');
    let html =`<form action="#" class="form-comment">
        <textarea required class="form-control border-dark border" maxlength="500" id="modal_comment" name="text" rows="10" cols="70"></textarea>
        <div class="mt-2 float-right">
            <button type="submit"   id="modal_button" class="off btn btn-success" ><strong>Send</strong></button>
            <button type="button" class=" btn btn-danger" data-dismiss="modal"><strong>Close</strong></button>
        </div>
     </form>`
    $(".modal-title").text("Leave a comment");
    $(".modal-body").html(html);
});

$(document).on("submit", ".form-comment", function () {
    $(".off").attr('disabled', true);
    let form = $(this);
    let form_obj = form.serializeObject();
    form_obj.jwt = getCookie("jwt");
    form_obj.parent_id = parent_id;
    let form_data = JSON.stringify(form_obj);
    $(this).find("textarea").val("");
    $.ajax({
        url: "../api/comment/creat.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,
        success : function(result) {
            PrintComment(result);
            $(".comment-img").error(function() {
                $(this).attr('src', '/api/image/nan.png?' + Math.random());
            });
            $(".off").attr('disabled', false);
            $('#myModal').modal('hide');
        },
        error : function(result) {
            console.log(result);
            $(".off").attr('disabled', false);
        }
    })
    return false;
})

$(document).on("submit", "#edit-comment", function () {
    $(".off").attr('disabled', true);
    let form = $(this);
    let form_obj = form.serializeObject();
    form_obj.jwt = getCookie("jwt");
    form_obj.id = edit_id;
    let form_data = JSON.stringify(form_obj);
        $.ajax({
            url: "../api/comment/update.php",
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function () {
                let text = $("#edit-comment").find('textarea').val();
                $("#comments-"+edit_id).find(".date-comment").find("lable").text("(edited)")
                $("#comments-text-"+edit_id).html(text);
                $('#myModal').modal('hide');
                $(".off").attr('disabled', false);
            },
            error: function (result) {
                console.log(result);
                $(".off").attr('disabled', false);
            }
        })
    return false;
})


$('#myModal').on('hide.bs.modal', function() {
    parent_id = null;
    edit_id = "";
    delete_id = "";
})

$(document).on("click", "#delete-button", function () {
    $(".off").attr('disabled', true);
    let jwt=getCookie("jwt");
    if(jwt !== undefined) {
        $.ajax({
            url: "../api/comment/delete.php",
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify({jwt: jwt, id: delete_id}),
            success: function (result) {
                $("#comments-"+delete_id).remove();
                $('#myModal').modal('hide');
                $(".off").attr('disabled', false);
            },
            error: function (result) {
                console.log(result);
                $(".off").attr('disabled', false);
            }
        })
    }
    return false;
})


