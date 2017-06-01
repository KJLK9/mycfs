/**
 * Created by KJ on 22-4-2017.
 */
function expanding(elem){
    var obj = document.getElementsByClassName(elem);
    if(obj[0].style.fontSize === "14px"){
        for (var i = 0; i < obj.length; i++) {
            var elemnt = obj[i];
            elemnt.style.padding = "0px";
            elemnt.style.height = "0px";
            elemnt.style.fontSize = "0px";
            elemnt.style.borderWidth = "0px";
            document.getElementById(elem).innerHTML="&#9654;";
        }
    }else{
        for (var x = 0; x < obj.length; x++) {
            var element = obj[x];
            element.style.padding = "5px";
            element.style.height = "auto";
            element.style.fontSize = "14px";
            element.style.borderWidth = "1px";
            document.getElementById(elem).innerHTML = "&#9660;";
        }
    }
}
function foc(el){
    document.getElementsByName(el)[0].focus();
}
function block(cat, zoom){
    window.location.href = "/"+cat+"/"+zoom;
}
function returnToHome(){
    window.location.href = "/";
}