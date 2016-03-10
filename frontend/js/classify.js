$(document).ready(function () {
    alert("hello");
    $.ajax({
        url:     "../backend/src/api/internal/settings.php?action=get",
        type:    "GET",
        success: function (json) {
            $("#test").text(JSON.stringify(json));
            alert("yay");
        },
        error:   function () {
            alert("It does not work...");
        }
    });
}
