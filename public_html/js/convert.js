
function form2jsonRE(str) {
    
    var keyValuePairs = str.split('&');
    var json = {};
    for(var i=0,len = keyValuePairs.length,tmp,key,value;i <len;i++) {
        tmp = keyValuePairs[i].split('=');
        key = decodeURIComponent(tmp[0]);
        value = decodeURIComponent(tmp[1]);
        if(key.search(/\[\]$/) != -1) {
            tmp = key.replace(/\[\]$/,'');
            json[tmp] = json[tmp] || [];
            json[tmp].push(value);
        }
        else {
            json[key] = value;
        }
    }
    
    return json;
}

function form2json(str) {
    var arr = str.split('&');
    var obj = {};
    for(var i = 0; i < arr.length; i++) {
        var bits = arr[i].split('=');
        obj[bits[0]] = bits[1];
    }
    
    return obj;
}

function json2form(obj) {
    str = '';
    for(key in obj) {
        str += key + '=' + obj[key] + '&';
    }
    str = str.slice(0, str.length - 1);
    
    return str;
}