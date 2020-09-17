<?php
    require_once "../block/header.php";
    require_once "../block/nav.php";?>
    <main class="container mt-5  p-5 bg-light">
        <div class="container" >
            <form action="#" class="form-comment">
                <textarea required class="border-dark border form-control" maxlength="500" name="text" rows="10" cols="120"></textarea>
                <button type="submit"  class="off float-right p-2 mt-2 btn btn-success"><strong>Send</strong></button>
            </form>
        </div>
        <div class="container border-success mt-5 " id="comments">

        </div>
    </main>
    <div class="modal fade" id="myModal">
        <div class=" modal-dialog" >
            <div class="bg-dark modal-content p-0" >
                <div class="text-center text-light modal-header">
                    <div class="container">
                        <h4 class="modal-title">Leave a comment</h4>
                    </div>
                </div>
                <div class="bg-light modal-body" >
                    <form action="#" class="form-comment">
                        <textarea required class="form-control border-dark border" maxlength="500" id="modal_comment" name="text" rows="10" cols="70"></textarea>
                        <div class="mt-2 float-right">
                            <button type="submit"   id="modal_button" class=" btn btn-success" ><strong>Send</strong></button>
                            <button type="button" class=" btn btn-danger" data-dismiss="modal"><strong>Close</strong></button>
                        </div>
                    </form>
                </div>
                <div class="p-1 bg-dark modal-footer"></div>
            </div>
        </div>
    </div>
    <script src="../script/comments.js"></script>
<?php require_once "../block/footer.php"?>