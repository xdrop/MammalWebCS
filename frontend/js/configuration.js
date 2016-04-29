var myInterval;
// Gets the current settings
function getSettings(_callback) {
    $.ajax({
        url:     "../backend/src/api/internal/settings.php?action=get",
        type:    "GET",
        success: function (json) {
            _callback(json);
        },
        error:   function (msg) {
            alert(JSON.stringify(msg));
        }
    });
}

// Sets the settings to the user specified
function setSettings(json_settings) {
    $.ajax({
        url:     "../backend/src/api/internal/settings.php",
        type:    "POST",
        data:     {
            "action": "store",
            "settings": JSON.stringify(json_settings)
        },
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

function updateProgress(){
    $.ajax({
        url:     "../backend/src/api/internal/algorithm.php?action=status",
        type:    "GET",
        success: function(json){
            var progress = parseInt(json.progress);
            var total = parseInt(json.total);
            if (progress == total) {
                clearInterval(myInterval);
                $("#progress").hide();
                $("#run").removeClass("disabled");

            }
            $("#progress").progress({
                percent: Math.floor((progress / total) * 100)
            })
        }
    });
}

// To reclassify data:
// Get -> Set new settings
// Rerun classify with new settings
$("#reclassify").click(function() {
    getFields(function(json) {
        setSettings(json);
    });
});

$("#run").click(function(){
    $.ajax({
        url:     "../backend/src/api/internal/algorithm.php",
        type:    "POST",
        data:     {
            "action": "run",
            "scientist_dataset": true
        },
        error:   function () {
            alert("Failed to set settings");
        }
    });
    $("#run").addClass("disabled");
    $("#progress").show();
    myInterval = setInterval(updateProgress, 4000);
});

// Get the current settings and populate the HTML form
$(document).ready(function () {
    getSettings(function(json) {
        updateFields(json);
    });
    $('#progress').progress();
    $("#progress").hide();
});
