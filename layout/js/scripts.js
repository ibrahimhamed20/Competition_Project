/* Global $*/

$(function () {
    "use strict";
    var ourCountDown = setInterval(function () {
        var hr = parseInt($('.hr').html());
        var min = parseInt($('.min').html());
        var sec = parseInt($('.sec').html());
        if (hr !== 0) {
            if (min !== 0) {
                if (sec !== 0) {
                    $('.sec').html(sec - 1);
                } else{
                    $('.sec').html('59');
                    $('.min').html(min - 1)
                }
            } else {
                $('.hr').html(hr - 1);
                $('.min').html('59');
            }
        }else if (hr === 0) {
            if (min !== 0){
                if (sec !== 0) {
                    $('.sec').html(sec - 1);
                } else{
                    $('.sec').html('59');
                    $('.min').html(min - 1);
                }
            }else if (min === 0) {
                if (sec !== 0) {
                    $('.sec').html(sec - 1);
                } else{
                    $('.sec').html('00');
                    clearInterval(ourCountDown);
                    $('.countdown').html('<h3 class="text-danger">الوقت إنتهي الآن !</h3>');
                }
            }else {
                $('.min').html('00');
            }
        }
    }, 1000);
});

function openQuiz(evt, quiz_Q) {
    var i, x;
    x = document.getElementsByClassName("quiz");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(quiz_Q).style.display = "block";
}