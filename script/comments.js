$(document).ready(function () {
    if(getCookie("jwt") === undefined){
        $(location).attr('href',"../");
    }
    $.ajax({
        url: "../api/comment/load.php",
        type : "POST",
        contentType : 'application/json',
        success:function (result) {
            result.forEach(element => {
                PrintComment(element);
            })
        },
        error:function (result) {
            console.log(result);
        }
    });
});

$(document).on("submit","#form-comment",function () {
    console.log("test");
    return false;
})

function PrintComment(comment){
    let img = (comment.user.image !== null)? comment.user.image : "/api/image/nan.png"
    let html =`
                <div class='card p-0'>
                    <div class=' p-1 card-header bg-dark text-light row' >
                        <div class='col-1 p-0' style='max-width: 32px'>
                            <img class='rounded  img-fluid img' src='`+img+`' style='width: 32px;height: 32px;' alt=''>
                        </div>
                        <h6 class='col '>`+comment.user.first_name+` `+comment.user.second_name+`</h6>
                        <small class='col text-right'>`+comment.date +`</small>
                    </div>
                    <div class='p-1 card-body'>
                       `+comment.text+`
                    </div>
                    <div class='p-1 pr-2 m-0 card-footer bg-dark text-right'>
                        <a href='#' class='m-0 p-0 text-success comment_id'id='`+comment.id+`'data-toggle='modal' 
                        data-target='#myModal'><strong>response</strong></a>
                    </div>
                    <div class="ml-3" id="comments`+comment.id+`"></div>
                </div>`
    if(comment.parent_id === null) {
        $('#comments').append(html);
    }
    else {
        $(('#comments'+comment.parent_id)).append(html);
    }

}