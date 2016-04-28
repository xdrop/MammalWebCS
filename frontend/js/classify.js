// Gets the current settings
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

// Sets the settings to the user specified
function setSettings(json_settings) {
    json = {};
    json.action = "store";
    json.settings = json_settings;
    $.ajax({
        url:     "../backend/src/api/internal/settings.php",
        type:    "POST",
        data:     {
            "action": "store",
            "settings": JSON.stringify(json)
        }
        error:   function () {
            alert("Failed to set settings");
        }
    });
}

// Update HTML form
function updateFields(json) {
    $("input[name='consecutive'").val(json.consecutive_expected);
    $("input[name='consensus'").val(json.votes_before_consensus);
    $("input[name='unreasonable'").val(json.unreasonable_number_of_species_in_image);
    $("input[name='evenness_species'").val(json.evenness_threshold_species);
    $("input[name='evenness_count'").val(json.evenness_threshold_count);
}

// Get new settings from HTML form
function getFields(_callback) {
    json = {};
    json.consecutive_expected = $("input[name='consecutive'").val();
    json.votes_before_consensus = $("input[name='consensus'").val();
    json.unreasonable_number_of_species_in_image = $("input[name='unreasonable'").val();
    json.evenness_threshold_species = $("input[name='evenness_species'").val();
    json.evenness_threshold_count = $("input[name='evenness_count'").val();
    _callback(json);
}

// To reclassify data:
// Get -> Set new settings
// Rerun classify with new settings
$("#reclassify").click(function() {
    getFields(function(json) {
        setSettings(json);
    });
});

// Get the current settings and populate the HTML form
$(document).ready(function () {
    getSettings(function(json) {
        updateFields(json);
    });
});
