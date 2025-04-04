function checkAnswer() {
        var chkBox = document.getElementById('needAnswer');
        if (chkBox.checked) {
            $("#dateAnswer").removeAttr("hidden");
            $("#nameAnswer").removeAttr("hidden");
            $("#filesAnswer").removeAttr("hidden");
        } else {
            $("#dateAnswer").attr("hidden", "true");
            $("#nameAnswer").attr("hidden", "true");
            $("#filesAnswer").attr("hidden", "true");
        }
    }

document.addEventListener('DOMContentLoaded', function () {
    $("#corr").change(function() {
        if (this.value != '') {
            $("#corr_div1").attr("hidden", "true");
            $("#corr_div2").attr("hidden", "true");
        }
        else
        {
            $("#corr_div1").removeAttr("hidden");
            $("#corr_div2").removeAttr("hidden");
        }
    });
});