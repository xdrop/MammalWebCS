/* TO DO:
 - Make filter buttons either not searchable (preferred), or make search work
 */
/*
 WHEN ADDING A NEW FILTER CATEGORY MUST ADD DATA AT MULTIPLE PLACES - THEY ARE NUMBERED LIKE THIS (1)
 */

//FILTER CRITERIA (1)
var filters = {}; //Stores the filters to be applied. The different filters are ANDed together i.e. badger AND forest. Within filter they are ORed e.g. badger OR fox
var species_include = []; //Stores the species to be included i.e. photos with any of these animals in
var species_exclude = []; //Stores the species to be exluded i.e. photos without any of these animals in
var habitats = []; //Stores the habitats to be included i.e. photos featuring any of these habitats
var sites = []; //Stores the sites to be included i.e. photos from any of these sites
var contains_human = false;
var is_flagged = false;
var includedUsers = [];
var excludedUsers = [];
var numSpecies = 0;
var minNumClassifications = 0;
var maxNumClassifications = 0;
var numClassifications = 0;
var taken_start = "1970-01-01 00:00:00"; //Must have both dates/time so these are the default values
var taken_end = "2100-01-01 00:00:00";
var scientist_dataset = true;

//RESULTS AND TABLE VARIABLES
var filterResults = []; //Holds the json of the most recent filter results
var resStart = 0; //The index of the first result to be shown
var resPerPage = 10; //How many results shown per page

//ORDERING VARIABLES
var isAscending = true; //Whether the column being sorted is ascending or descending
var currentSort = ""; //The attribute (table heading) currently being sorted
var iconIndex = 0; //The direction of the icon. 0 is down, 1 is up.
var icons = ['<i class="dropdown icon"></i>', '<i class="dropdown icon vertically flipped"></i>']; //The two icons

var csv_filename;

//UPDATE THE NUMBER OF ROWS OF THE RESULT TABLE TO BE SHOWN
//@param value - the number of results to be show. Options are 10, 25, 50, 100, 500 or All
function updateResPerPage(value) {
    if (filterResults.length == 0 && value == "All") //If want to have all. Has to give -1 as dont know size of next filter.  The size is updated in drawTable
    {
        resPerPage = -1; //Indicates not had any filters
    }
    else //Do only if a filter has been applied already
    {
        if (value == "All") {
            resPerPage = -1; //Don't know how many will have
        }
        else {
            resPerPage = parseInt(value);
        }
        resStart = 0; //Go back to first reault
        displayTable(sortJson(filterResults, isAscending, currentSort));
    }
}

/*SORT THE JSON RESULT
 @param json - the json object to be sorted
 @param isAsc - whether the list is to be sorted in ascending or descending order. True if ascending
 @param attrib - the attribute to be sorted by e.g. species name */
function sortJson(json, isAsc, attrib) //sorts json by attrib in ascending order (if isAsc == true, descending if not)
{
    var sorted = json.slice(); //Create deep copy of the list
    if (currentSort != "") {
        sorted = sorted.sort(function (a, b) {
            var thing1 = a[attrib].toLowerCase(); //Since it treats uppercase as before lowercase (e.g. B > a)
            var thing2 = b[attrib].toLowerCase();
            if (isAsc) {
                if (thing1 > thing2) {
                    return 1;
                }
                else if (thing1 < thing2) {
                    return -1;
                }
                else {
                    return 0;
                }
            }
            else {
                if (thing1 < thing2) {
                    return 1;
                }
                else if (thing1 > thing2) {
                    return -1;
                }
                else {
                    return 0;
                }
            }
        });
    }
    return sorted;
}

//DISPLAY THE RESULTS OF THE FILTER AS A TABLE
//@param json - the json object that contains the data
function displayTable(json) {
    if (json == "NO RESULT") //No photos for the given criteria
    {
        $("#resultsTable").html("<tr class='center aligned'><td colspan='11' class='align right'><b>No results</b></td></tr>");
    }
    else {
        $("#resultsTable").html(''); //Clear the table first
        if (resPerPage == -1) //Wants all shown
        {
            resPerPage = json.length;
        }
        var resLimit = 0; //The index of the final image to be shown
        if ((resStart + resPerPage) > json.length) //If the index of the final image is greater than the number of images...
        {
            resLimit = json.length; //...make the final image the number of the final image (e.g. 23 as opposed to 30)
        }
        else {
            resLimit = resStart + resPerPage; //Else make it the start plus the number of images shown
        }

        for (var i = resStart; i < resLimit; i++) //Go through each specified image in the json of each result and use the information to make arow in the table
        {
            var obj = json[i]; //The information in json form of the image. Included information can be seen here: https://github.com/xdrop/MammalWebCS/wiki/Filtering#filter
            //Create the link tag for the photograph. Will be in the form <a data-lightbox:"i" data-title:"species_name" href="http://www.mammalweb.org/..."> View </a>
            a = document.createElement('a'); //Create a temporary element of the a (link) type
            a.setAttribute("data-lightbox", "" + i); //Add the lightbox atribute (lets you view the image)
            a.setAttribute("data-title", obj.species_name); //The name that appears on the light box
            a.href = obj.url; // Insted of calling setAttribute, sets the link to the image
            a.innerHTML = "View"; // <a>INNER_TEXT</a>

            if (obj.flagged == 0) //If the object is flagged
            {
                data = "<tr class='center aligned'>"; //The start of the row
            }
            else {
                data = "<tr class='center aligned error'>"; //Alternate start of the row
            }

            data += "<td>" + a.outerHTML + "</td>"; //The first cell in the table is the link to the image

            if (obj.flagged == 0) //If not flagged
            {
                data += "<td></td>"; //Next cell shows nothing
            }
            else //If flagged
            {
                data += "<td class='centered'><i class='flag icon'></i></td>"; //Next cell shows a flag
            }

            data += "<td>" + obj.species_name + "</td>"; //Add species
            data += "<td>" + obj.contains_human + "</td>"; //Add if it contains human
            data += "<td>" + obj.time_classified + "</td>"; //Add time classified
            data += "<td>" + obj.taken + "</td>"; //Add time taken
            data += "<td>" + obj.person_id + "</td>"; //Add person ID
            data += "<td>" + obj.site_name + "</td>"; //Add site IDs
            data += "<td>" + obj.habitat_name + "</td>";
            data += "<td>" + obj.evenness_species + "</td>";
            data += "<td>" + obj.evenness_count + "</td>";
            $(data + "</tr>").appendTo("#resultsTable");
        }
    }
    updatePageNum();
    populatePagesDropdown(document.getElementById("pagesDropdown").value);
}

//CHANGE THE CURRENT VIEW PAGE FROM A VALUE CHOSEN FROM A DROPDOWN
//@param pageNum - the page to go to
function goToPage(pageNum) {
    resStart = (pageNum - 1) * resPerPage;
    displayTable(filterResults);
}

//GO TO THE FIRST PAGE OF THE TABLE
function firstPage() {
    if (filterResults.length != 0) //Only works if something in the table
    {
        resStart = 0;
        document.getElementById("pagesDropdown").options[0].setAttribute("selected", true);
        displayTable(filterResults);
    }
}

//GO TH THE NEXT PAGE IN THE TABLE
function nextPage() {
    if ((resStart + resPerPage) < filterResults.length) //If not on last page
    {
        resStart += resPerPage;
        document.getElementById("pagesDropdown").options[((resStart / resPerPage))].setAttribute("selected", true);
        displayTable(filterResults);
    }
}

//GO TO THE PREVIOUS PAGE
function prevPage() {
    if ((resStart - resPerPage) >= 0) //If not on first page
    {
        resStart -= resPerPage;
        document.getElementById("pagesDropdown").options[((resStart / resPerPage))].setAttribute("selected", true);
        displayTable(filterResults);
    }
}

//GO TO THE LAST PAGE OF THE TABLE
function lastPage() {
    if (filterResults.length != 0) {
        if (filterResults.length % resPerPage != 0) //i.e. the length of filterResults = resPerPage. Ths stops it from showing empty last page when showing all
        {
            resStart = filterResults.length - (filterResults.length % resPerPage);
            //console.log(resStart, resPerPage, resStart/resPerPage);
            document.getElementById("pagesDropdown").options[resStart / resPerPage].setAttribute("selected", true);
        }
        else //filterResults is a multiple of resPerPage. Need this stop a blank last page.
        {
            resStart = filterResults.length - resPerPage;
        }
        displayTable(filterResults);
    }
}

//UPDATE THE PAGE NUMBER
function updatePageNum() {
    if (filterResults.length != 0) {
        $("#pageNum").html("Showing page " + ((resStart / resPerPage) + 1) + " of " + Math.ceil((filterResults.length / resPerPage)));
    }
    else {
        $("#pageNum").html("Showing page 0 of 0");
    }
}

//POPULATE THE PAGE CHANGE DROPDOWN WITH THE POSSIBLE PAGE NUMBERS
function populatePagesDropdown(currentValue) {
    var extra;
    if (filterResults.length % resPerPage == 0) {
        extra = 1; //Otherwise 2 would make a blank last page
    }
    else {
        extra = 2;
    }
    $("#pagesDropdown").empty(); //Clear current options so don't keep adding them
    for (var i = 1; i < Math.floor((filterResults.length / resPerPage) + extra); i++) {
        var currentOption = document.getElementById("pagesDropdown"); //The current page the table is on
        currentOption.add(new Option(i)); //Add a new option
        if (i == currentValue) //If the option to be added is the current option
        {
            currentOption.options[i - 1].setAttribute("selected", true); //Make it the option shown in the dropdown
        }
    }
}

$("#moreOptions").click(function () {
    $('.ui.modal')
        .modal('show')
    ;
})

//APPLY THE FILTER (2)
function applyFilter(customFilter) //If the filter button is pressed
{
    if (arguments.length > 0) {
        filters = customFilter;
    } else {
        filters = {}; //reset filters
        if (species_include.length != 0) {
            filters.species_include = species_include; //Add the included species to filters
        }
        if (species_exclude.length != 0) {
            filters.species_exclude = species_exclude;
        }
        if (habitats.length != 0) {
            filters.habitat_id = habitats;
        }
        if (sites.length != 0) {
            filters.site_id = sites;
        }
        if (contains_human == true) {
            filters.contains_human = true;
        }
        if (is_flagged == true) {
            filters.flagged = true;
        }
        if (taken_start != "") {
            filters.taken_start = taken_start;
        }
        if (taken_end != "") {
            filters.taken_end = taken_end;
        }
        if (includedUsers.length != 0) {
            filters.users_include = includedUsers;
        }
        if (excludedUsers.length != 0) {
            filters.users_exclude = excludedUsers;
        }
        if (numSpecies != 0) {
            filters.no_of_species = numSpecies;
        }
        if (numClassifications != 0) {
            filters.no_of_classifications = numClassifications;
        }
        if (minNumClassifications != 0) {
            filters.no_of_classifications_from = minNumClassifications;
        }
        if (maxNumClassifications != 0) {
            filters.no_of_classifications_to = maxNumClassifications;
        }
        if (scientist_dataset != false) {
            filters.scientist_dataset = true;
        }
    }

    //alert(JSON.stringify(filters));
    $.ajax({
        url: "../backend/src/api/internal/filter.php",
        type: "POST",
        data: {"params": JSON.stringify(filters)},
        success: function (json) {
            $("#tableHeadings").attr("style", "visibilty:visible");
            $(".pageInfo").attr("style", "text-align:center; visibility:visible;");
            resStart = 0; //Start at the first result
            filterResults = json.results; //Store the result
            displayTable(filterResults); //Display the result
            updatePageNum(); //Update the page numbers
            var tableHeads = $(".tableHead");
            for (var i = 0; i < tableHeads.length; i++) //Remove dropdown arrows from other headings
            {
                tableHeads[i].innerHTML = tableHeads[i].innerHTML.split("<")[0];
            }
            // TODO: add a link to "filter.php?csv=" + csv_filename which will download the csv output
            csv_filename = json.csv; //Get and store the csv file name
            //console.log(csv_filename);
            document.getElementById("csvButton").disabled = false;
        },
        error: function () {
            //alert("It does not work...");
            filterResults = []; //Store the result as empty
            displayTable("NO RESULT");
            updatePageNum();
        }
    });

};

function downloadCSV() {
    $("#downloadCSVLink").attr("href", "../backend/src/storage/csv/" + csv_filename);
    return true; //return makes the href wait
}

$("#downloadCSVButton").click(function () {

});

function getRecentQueries() {
    $.getJSON("http://164.132.197.56/mammalwebcs/backend/src/api/internal/list.php?item=queries", function (data) {
        var resQueries = $("#recentQueries");
        var total = data.length;
        $.each(data, function (index, value) {
            var text = "Query #" + value.id + " - " + value.time;
            var $elem = $('<a class="item">' + text + '</a>');
            // On click get the json of the filter and run the filter ajax
            $elem.click(function () {
                applyFilter(JSON.parse(value.json));
            });
            resQueries.append($elem);
        });
    });
}

$(document).ready(function () {
    var $masterDrop = $("#masterDrop");
    var $speciesIncludeDrop = $("#speciesIncludeDrop");
    var $speciesExcludeDrop = $("#speciesExcludeDrop");
    var $habitatDrop = $("#habitatDrop");
    var $siteDrop = $("#siteDrop");

    getRecentQueries();


    $('.ui.checkbox').checkbox(); //Initialise checkbox

    $("#resultsTable").html("<tr class='center aligned'><td colspan='11' class='align right'>Results go here...</td></tr>"); //Show were results will go (5)

    populatePagesDropdown(0); //Make the dropdown have only the value of 0 in. Looks better than a dropdown with nothing

    //SORT A COLUMN OF THE TABLE
    $(".tableHead").click(function () { //If a column heading clicked
        var tableHeads = $(".tableHead"); //All the column headings
        for (var i = 0; i < tableHeads.length; i++) //Remove dropdown arrows from other headings
        {
            tableHeads[i].innerHTML = tableHeads[i].innerHTML.split("<")[0]; //Get the column heading name - ignore the rest of the html (the icon)
        }
        var newSort = $(this).attr("value"); //The column being sorted. Stored so can check if same as column already being sorted. 'this' is used because coud be any of the column headings - 'this' is the one that was clicked
        if (currentSort == newSort) //Change ordering
        {
            isAscending = !isAscending; //Make next click change direction
            iconIndex = (iconIndex + 1) % 2; //Make next click change arrow direction
        }
        else //If sort a different column, make it so ascending by default
        {
            isAscending = true;
            iconIndex = 0;
        }
        currentSort = newSort;
        filterResults = sortJson(filterResults, isAscending, $(this).attr("value")); //Update filterResults with the new, sorted results
        displayTable(filterResults); //Display results
        this.innerHTML = this.innerHTML.split("<")[0] + icons[iconIndex];
    });

    //Clear the master dropdown of all labels
    $("#clearMaster").click(function () {
        $masterDrop.dropdown('clear');
    });

    $('.ui.dropdown').dropdown(); //Initialise dropdown

    //WHEN THERE IS A CHANGE IN THE MAIN DROPDOWN (3)
    $masterDrop.dropdown({
        onChange: function (value, text) {
            species_include.length = 0; //Resets the array. arr = [] does not work.
            species_exclude.length = 0;
            habitats.length = 0;
            sites.length = 0;
            contains_human = false;
            is_flagged = false;
            includedUsers.length = 0;
            excludedUsers.length = 0;
            numSpecies = 0;
            minNumClassifications = 0;
            maxNumClassifications = 0;
            numClassifications = 0;
            taken_start = "1970-01-01 00:00:00"; //Set dated to default values
            taken_end = "2100-01-01 00:00:00";
            values = $masterDrop.dropdown("get values");
            for (var i = 0; i < values.length; i++) //Go through everything stored in the main dropdown
            {
                var val = values[i].split('=');
                var filterCategory = val[0]; //The name of the filter category
                var filterValue = val[1]; //The value of that specific filter
                //Add the desired filters to their arrays
                if (filterCategory == "animal") {
                    species_include.push(parseInt(filterValue));
                }
                else if (filterCategory == "no_animal") {
                    species_exclude.push(parseInt(filterValue));
                }
                else if (filterCategory == "habitat") {
                    habitats.push(parseInt(filterValue));
                }
                else if (filterCategory == "site") {
                    sites.push(parseInt(filterValue));
                }
                else if (filterCategory == "contains_human") {
                    contains_human = true;
                }
                else if (filterCategory == "is_flagged") {
                    is_flagged = true;
                }
                else if (filterCategory == "datetimeFrom") {
                    taken_start = filterValue;
                }
                else if (filterCategory == "datetimeTo") {
                    taken_end = filterValue;
                }
                else if (filterCategory == "numSpecies") {
                    numSpecies = filterValue;
                }
                else if (filterCategory == "numClassifications") {
                    numClassifications = filterValue;
                }
                else if (filterCategory == "minNumClassifications") {
                    minNumClassifications = filterValue;
                }
                else if (filterCategory == "maxNumClassifications") {
                    maxNumClassifications = filterValue;
                }
                else if (filterCategory == "includeUser") {
                    includedUsers.push(parseInt(filterValue));
                }
                else if (filterCategory == "excludeUser") {
                    excludedUsers.push(parseInt(filterValue));
                }
            }
            applyFilter();
        }
    });

    var usesApi = ["speciesIncludeDrop", "speciesExcludeDrop", "habitatDrop", "siteDrop"]; //The filters that need to be populaed by an api

    //The information needed for each dropdown (4)
    var info = {
        "newSpeciesDrop": ["animal", "Include species", "green", "species"],
        "speciesIncludeDrop": ["animal", "Include species", "green", "species"],
        "speciesExcludeDrop": ["no_animal", "Exclude species", "red", "species"],
        "habitatDrop": ["habitat", "Habitat", "teal", "habitats"],
        "siteDrop": ["site", "Site", "yellow", "sites"],
        "humanCheck": ["contains_human", "Contains human", "olive"],
        "flaggedCheck": ["is_flagged", "Flagged", "orange"],
        "numSpecies": ["numSpecies", "Number of species", "black"],
        "numClassifications": ["numClassifications", "Number of classifications", "violet"],
        "minNumClassifications": ["minNumClassifications", "Minimum number of classifications", "brown"],
        "maxNumClassifications": ["maxNumClassifications", "Maximum number of classifications", "yellow"],
        "includeUser": ["includeUser", "Included user", "green"],
        "excludeUser": ["excludeUser", "Excluded user", "red"]
    };

    //Add dropdown selection to main selection, and populate dropdown with api
    $(".filterOpt").dropdown({
        action: function (text, value) {	//When an option from the dropdown is chosen
            var chosenDropdown = event.target.parentElement.parentElement.id //The dropdown that has been chosen. Got by looking through parents of the item chosen from the dropdown
            console.log(chosenDropdown);
            var filterType = info[chosenDropdown][0] + "=";
            var labelName = info[chosenDropdown][1];
            var colour = info[chosenDropdown][2];
            $masterDrop.dropdown("add value", filterType + value)//, labelName + ": " + text); //Add the value
            if (chosenDropdown == "habitatDrop") //Only show the first word of the habitat
            {
                $masterDrop.dropdown("add label", filterType + value, labelName + ": " + text.substr(0, text.indexOf(' ')), colour);
            }
            else {
                $masterDrop.dropdown("add label", filterType + value, labelName + ": " + text, colour); //Add the label
            }
            //$speciesIncludeDrop.dropdown("action hide");
        },
        fields: {name: "name", value: "id"},
        apiSettings: {
            beforeSend: function (settings) {
                if (usesApi.indexOf(event.target.parentNode.id) != -1) {
                    settings.url = '../backend/src/api/internal/list.php?item=' + info[event.target.parentNode.id][3]; //used instead of the url parameter because cannot seem to reference DOM element to get necessary api query (species, habitats etc.)
                    return settings;
                }
                else {
                    return false;
                }

            },
            //url: "../backend/src/api/internal/list.php?",
            onResponse: function (response) {
                return {success: true, results: response};
            }
        }
    });

    var prevDateTimeFrom = ""; //Stores the most recent datetime. Used so can replace datetime if a new one decided.
    var prevDateTimeTo = "";

    //DATE FROM
    $('#dateFrom').daterangepicker(
        {
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            timePicker12Hour: false,
            timePickerSeconds: true,
            timePickerIncrement: 1
        }
    );

    $('#dateFrom').on('apply.daterangepicker', function (ev, picker) { //Executed when the apply button is clicked
        var datetime = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        //Remove previous datetime since only allowed one
        $masterDrop.dropdown("remove label", "datetimeFrom=" + prevDateTimeFrom);
        $masterDrop.dropdown("remove value", "datetimeFrom=" + prevDateTimeFrom);
        //Add new datetime
        $masterDrop.dropdown("add value", "datetimeFrom=" + datetime, "Date from: " + datetime);
        $masterDrop.dropdown("add label", "datetimeFrom=" + datetime, "Date from: " + datetime, "pink");
        prevDateTimeFrom = datetime;
    });

    //DATE TO
    $('#dateTo').daterangepicker(
        {
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            timePicker12Hour: false,
            timePickerSeconds: true,
            timePickerIncrement: 1
        }
    );

    //Executed when the apply button is clicked
    $('#dateTo').on('apply.daterangepicker', function (ev, picker) {
        var datetime = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        $masterDrop.dropdown("remove label", "datetimeTo=" + prevDateTimeTo);
        $masterDrop.dropdown("remove value", "datetimeTo=" + prevDateTimeTo);
        $masterDrop.dropdown("add value", "datetimeTo=" + datetime, "Date to: " + datetime);
        $masterDrop.dropdown("add label", "datetimeTo=" + datetime, "Date to: " + datetime, "purple");
        prevDateTimeTo = datetime;
    });


    var prevValues = {
        "numSpecies": "",
        "numClassifications": "",
        "minNumClassifications": "",
        "maxNumClassifications": ""
    };

    $(".filterForm").submit(function (event) {
        event.preventDefault();
        //console.log(event.target.children[0].value);
        var chosenCheck = event.target.id //The dropdown that has been chosen. Got by looking through parents of the item chosen from the dropdown
        var enteredValue = event.target.children[0].value;
        var filterType = info[chosenCheck][0] + "=";
        var labelName = info[chosenCheck][1];
        var colour = info[chosenCheck][2];
        if (prevValues.hasOwnProperty(chosenCheck) == true) //Remve any labels already there
        {
            if (prevValues[chosenCheck] != "") {
                $masterDrop.dropdown("remove label", prevValues[chosenCheck]);
                $masterDrop.dropdown("remove value", prevValues[chosenCheck]);
            }
        }
        $masterDrop.dropdown("add value", filterType + enteredValue); //Add the value
        $masterDrop.dropdown("add label", filterType + enteredValue, labelName + ": " + enteredValue, colour); //Add the label
        if (prevValues.hasOwnProperty(chosenCheck) == true) //Store the values so any values already there are known so dows not put more than one label for categories that are not allowed
        {
            prevValues[chosenCheck] = filterType + enteredValue;
        }
        event.target.children[0].value = ""; //Reset the form
    });


    $(".check").checkbox({
        onChecked: function (text, value) {
            var chosenCheck = event.target.id //The dropdown that has been chosen. Got by looking through parents of the item chosen from the dropdown
            var filterType = info[chosenCheck][0] + "=";
            var labelName = info[chosenCheck][1];
            var colour = info[chosenCheck][2];
            $masterDrop.dropdown("add value", filterType + "1"); //Add the value
            $masterDrop.dropdown("add label", filterType + "1", labelName + ": " + "yes", colour); //Add the label
        },
        onUnchecked: function (text, value) {
            var chosenCheck = event.target.id
            var filterType = info[chosenCheck][0] + "=";
            var labelName = info[chosenCheck][1];
            var colour = info[chosenCheck][2];
            $masterDrop.dropdown("remove value", filterType + "1"); //Rremove the value
            $masterDrop.dropdown("remove label", filterType + "1", labelName + ": " + "yes", colour); //Remove the label
        }
    });
    //$('.test.checkbox').checkbox('attach events', '.toggle.button');
    $("#scientistCheck").checkbox({
        onChange: function (text, value) {
            scientist_dataset = !scientist_dataset;
        }
    })

});
