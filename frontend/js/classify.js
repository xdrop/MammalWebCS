function getSettings(_callback) {
    $.ajax({
        url:     "../backend/src/api/internal/settings.php?action=get",
        type:    "GET",
        success: function (json) {
            _callback(json);
        },
        error:   function () {
        }
    });
}

function updateFields(json) {

    $("input[name='consecutive'").val(json.consecutive_expected);
}

$(document).ready(function () {
    getSettings(function(json) {
        updateFields(json);
    });
});
