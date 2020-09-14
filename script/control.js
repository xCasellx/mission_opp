

function setCookie(cname, cvalue, days) {
    let d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let  co = decodeURIComponent(document.cookie).split("; ");
    for (let i = 0 ; i < co.length ; ++i) {
        if(co[i].indexOf(name)===0) {
            return co[i].substring(name.length, co[i].length);
        }
    }
}

$.fn.serializeObject = function(){
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
    let s=(d.getFullYear()-year)+"-12-01";
    return s;
}