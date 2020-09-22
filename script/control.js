$("#sign-out").on("click", function() {
    setCookie("jwt","",-1);
    $(location).attr('href',"../");
});

function  loadListCountry() {
    $.ajax({
        url: "../api/location/country.php",
        type : "POST",
        contentType : 'application/json',
        success : function(result){
            $("#country").html(`<option></option>`);

            result.forEach(element => {
                $("#country").append(`<option id='country-`+element.id+`'>`+element.name+`</option>`);
            });
        }
    })
}
function  loadListRegion(){
    let id =$('#country option:selected').attr("id");
    id=id.replace("country-","");
    $.ajax({
        url: "../api/location/region.php",
        type : "POST",
        data: JSON.stringify({ country_id: id }),
        contentType: 'application/json',
        success: function(result){
            $("#region").html("<option></option>");
            result.forEach(element => {
                $("#region").append(`<option id='region-`+element.id+`'>`+element.name+`</option>`);
            });
        },
        error: function (result) {
            console.log(result)
        }
    })

}
function  loadListCity(){
    let id =$('#region option:selected').attr("id");
    id=id.replace("region-","");
    $.ajax({
        url: "../api/location/city.php",
        type : "POST",
        data: JSON.stringify({ region_id: id }),
        contentType: 'application/json',
        success: function(result){
            $("#city").html(`<option></option>`);
            result.forEach(element => {
                $("#city").append(`<option id='city-`+element.id+`'>`+element.name+`</option>`);
            });
        },
        error: function (result) {
            console.log(result)
        }
    })
}

function setCookie(cname, cvalue, days) {
    let d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let  co = decodeURIComponent(document.cookie).split("; ");
    for (let i = 0 ; i < co.length ; ++i) {
        if(co[i].indexOf(name) === 0) {
            return co[i].substring(name.length, co[i].length);
        }
    }
}

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function maxDate (year) {
    let d = new Date();
    d.setTime(d.getTime());
    let s = (d.getFullYear()-year)+"-12-01";
    return s;
}

function  printMessage(type, text) {
    $(".status-message").removeClass( "d-none" );
    if("success" === type) {
        $(".status-message").addClass("alert-success");
    }
    else if("error" === type) {
        $(".status-message").addClass("alert-danger");
    }
    $(".status-message").text(text);
    setTimeout(deleteMessage, 5000);
}

function  deleteMessage() {
    $(".status-message").addClass( "d-none" );
    $(".status-message").text("");
    if($(".status-message").hasClass( "alert-danger" )) {
        $(".status-message").removeClass( "alert-danger" );
        return ;
    }
    if($(".status-message").hasClass( "alert-success" )) {
        $(".status-message").removeClass( "alert-success" );
        return ;
    }

}
