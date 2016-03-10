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
    $("input[name='consensus'").val(json.votes_before_consensus);
    $("input[name='unreasonable'").val(json.unreasonable_number_of_species_in_image);
    $("input[name='evenness_species'").val(json.evenness_threshold_species);
    $("input[name='evenness_count'").val(json.evenness_threshold_count);
}

$(document).ready(function () {
    getSettings(function(json) {
        updateFields(json);
    });
});
