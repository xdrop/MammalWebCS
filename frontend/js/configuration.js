var myInterval;
var scientistDataset = true;
var started = false;

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
    id_from = $("input[name='id_from'").val();
    id_to = $("input[name='id_to'").val();
    if(id_to == 0){
    	// -1 means run until end
    	id_to = -1;
    }
    _callback(json, id_from, id_to);
}

function updateProgress(){
    $.ajax({
        url:     "../backend/src/api/internal/algorithm.php?action=status",
        type:    "GET",
        success: function(json){
            var progress = parseInt(json.progress);
            var total = parseInt(json.total);
            if (progress == total) {
                $("#progress").hide();
                $("#run").removeClass("disabled");
            } else{
            	$("#progress").show();
            	$("#run").addClass("disabled");
            }
            var percentage = Math.floor((progress / total) * 100);
            $("#progress").progress({
                percent: percentage
            })
            }

        }
    });
}

$("#run").click(function(){ // To reclassify data:
    var fromID = 0;
    var toID = 0;

	getFields(function(json, from_id, to_id) { // Get -> Set new settings
        setSettings(json); // Rerun classify with new settings
        fromID = from_id;
        toID = to_id;
    });
    $.ajax({
        url:     "../backend/src/api/internal/algorithm.php",
        type:    "POST",
        data:     {
            "action": "run",
            "from_id" : fromID,
            "to_id" : toID,
            "scientist_dataset": scientistDataset
        },
        success: function(){
        	$("#run").addClass("disabled");
    		$("#progress").show();
    		myInterval = setInterval(updateProgress, 1000);
        },
        error:   function () {
            alert("Failed to set settings");
        }
    });

});

$("#scientistData").checkbox({onChange: function(){
		scientistDataset = !scientistDataset;
	}
});

// Get the current settings and populate the HTML form
$(document).ready(function () {
    getSettings(function(json) {
        updateFields(json);
    });
    $('#progress').progress();
    $("#progress").hide();
});
