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
var scientist_dataset = false;

//RESULTS AND TABLE VARIABLES
var filterResults = []; //Holds the json of the most recent filter results
var resStart = 0; //The index of the first result to be shown
var resPerPage = 10; //How many results shown per page

//ORDERING VARIABLES
var isAscending = true; //Whether the column being sorted is ascending or descending
var currentSort = ""; //The attribute (table heading) currently being sorted
var iconIndex = 0; //The direction of the icon. 0 is down, 1 is up.
var icons = ['<i class="dropdown icon"></i>', '<i class="dropdown icon vertically flipped"></i>']; //The two icons

//Dropdown population
var usesApi = ["newSpeciesDrop", "speciesExcludeDrop", "habitatDrop", "siteDrop"]; //The filters that need to be populaed by an api
//The information needed for each dropdown (4)
var info = {
	"newSpeciesDrop": ["animal", "Include species", "green", "species"],
	"speciesExcludeDrop": ["no_animal", "Exclude species", "red", "species"],
	"habitatDrop": ["habitat", "Habitat", "teal", "habitats"],
	"siteDrop": ["site", "Site", "yellow", "sites"],
	"humanCheck": ["contains_human", "Contains human", "olive"],
	"flaggedCheck": ["is_flagged", "Flagged", "orange"],
	"scientistCheck":["scientist_dataset", "Scientist dataset", "blue"],
	"numSpecies": ["numSpecies", "Number of species", "black"],
	"numClassifications": ["numClassifications", "Number of classifications", "violet"],
	"minNumClassifications": ["minNumClassifications", "Minimum number of classifications", "brown"],
	"maxNumClassifications": ["maxNumClassifications", "Maximum number of classifications", "yellow"],
	"includeUser": ["includeUser", "Include user", "green"],
	"excludeUser": ["excludeUser", "Exclude user", "red"]
};

//Date/time variables
var prevDateTimeFrom = ""; //Stores the most recent datetime. Used so can replace datetime if a new one decided.
var prevDateTimeTo = "";

//CSV
var csv_filename;

var usesApi = ["speciesIncludeDrop", "speciesExcludeDrop", "habitatDrop", "siteDrop"]; //The filters that need to be populaed by an api

//The information needed for each dropdown (4)
var info = {
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
//Page changing
var currentPage = 1;
var $paginationMenu = $("#paginationMenu");

//DROPDOWNS
var $masterDrop = $("#masterDrop");
var $speciesIncludeDrop = $("#speciesIncludeDrop");
var $speciesExcludeDrop = $("#speciesExcludeDrop");
var $habitatDrop = $("#habitatDrop");
var $siteDrop = $("#siteDrop");

$("#slideshowButton").click(function(){
    slideshow();
})

function slideshow(){
    $("#resultsTable:eq(0) tr").find('a').each(function() {
        $('#slide').append("<li><img src=\'" + $(this).attr('href') +"\' /></li>");
        console.log($(this).attr('href'));
});
}

function dashboard(id, fData){
    var barColor = '#c10c0c';
    function segColor(c){ return {forest:"#e31f1f", woodland:"#e74242",scrubland:"#ec6464", grassland:"#f08787", swamp:"#f4aaaa", riverbank:"#f9cdcd", garden:"#fdf0f0"}[c]; }

    // compute total for each species.
    fData.forEach(function(d){d.total=d.freq.forest+d.freq.woodland+d.freq.scrubland+d.freq.grassland+d.freq.swamp+d.freq.riverbank+d.freq.garden;});

    // function to handle histogram.
    function histoGram(fD){
        var hG={},    hGDim = {t: 60, r: 0, b: 30, l: 0};
        hGDim.w = 500 - hGDim.l - hGDim.r,
        hGDim.h = 300 - hGDim.t - hGDim.b;

        //create svg for histogram.
        var hGsvg = d3.select(id).append("svg")
            .attr("width", hGDim.w + hGDim.l + hGDim.r)
            .attr("height", hGDim.h + hGDim.t + hGDim.b).append("g")
            .attr("transform", "translate(" + hGDim.l + "," + hGDim.t + ")");

        // create function for x-axis mapping.
        var x = d3.scale.ordinal().rangeRoundBands([0, hGDim.w], 0.1)
                .domain(fD.map(function(d) { return d[0]; }));

        // Add x-axis to the histogram svg.
        hGsvg.append("g").attr("class", "x axis")
            .attr("transform", "translate(0," + hGDim.h + ")")
            .call(d3.svg.axis().scale(x).orient("bottom"));

        // Create function for y-axis map.
        var y = d3.scale.linear().range([hGDim.h, 0])
                .domain([0, d3.max(fD, function(d) { return d[1]; })]);

        // Create bars for histogram to contain rectangles and freq labels.
        var bars = hGsvg.selectAll(".bar").data(fD).enter()
                .append("g").attr("class", "bar");

        //create the rectangles.
        bars.append("rect")
            .attr("x", function(d) { return x(d[0]); })
            .attr("y", function(d) { return y(d[1]); })
            .attr("width", x.rangeBand())
            .attr("height", function(d) { return hGDim.h - y(d[1]); })
            .attr('fill',barColor)
            .on("mouseover",mouseover)// mouseover is defined below.
            .on("mouseout",mouseout);// mouseout is defined below.

        //Create the frequency labels above the rectangles.
        bars.append("text").text(function(d){ return d3.format(",")(d[1])})
            .attr("x", function(d) { return x(d[0])+x.rangeBand()/2; })
            .attr("y", function(d) { return y(d[1])-5; })
            .attr("text-anchor", "middle");

        function mouseover(d){  // utility function to be called on mouseover.
            // filter for selected species.
            var st = fData.filter(function(s){ return s.species == d[0];})[0],
                nD = d3.keys(st.freq).map(function(s){ return {type:s, freq:st.freq[s]};});

            // call update functions of pie-chart and legend.
            pC.update(nD);
            leg.update(nD);
        }

        function mouseout(d){    // utility function to be called on mouseout.
            // reset the pie-chart and legend.
            pC.update(tF);
            leg.update(tF);
        }

        // create function to update the bars. This will be used by pie-chart.
        hG.update = function(nD, color){
            // update the domain of the y-axis map to reflect change in frequencies.
            y.domain([0, d3.max(nD, function(d) { return d[1]; })]);

            // Attach the new data to the bars.
            var bars = hGsvg.selectAll(".bar").data(nD);

            // transition the height and color of rectangles.
            bars.select("rect").transition().duration(500)
                .attr("y", function(d) {return y(d[1]); })
                .attr("height", function(d) { return hGDim.h - y(d[1]); })
                .attr("fill", color);

            // transition the frequency labels location and change value.
            bars.select("text").transition().duration(500)
                .text(function(d){ return d3.format(",")(d[1])})
                .attr("y", function(d) {return y(d[1])-5; });
        }
        return hG;
    }

    // function to handle pieChart.
    function pieChart(pD){
        var pC ={},    pieDim ={w:250, h: 250};
        pieDim.r = Math.min(pieDim.w, pieDim.h) / 2;

        // create svg for pie chart.
        var piesvg = d3.select(id).append("svg")
            .attr("width", pieDim.w).attr("height", pieDim.h).append("g")
            .attr("transform", "translate("+pieDim.w/2+","+pieDim.h/2+")");

        // create function to draw the arcs of the pie slices.
        var arc = d3.svg.arc().outerRadius(pieDim.r - 10).innerRadius(0);

        // create a function to compute the pie slice angles.
        var pie = d3.layout.pie().sort(null).value(function(d) { return d.freq; });

        // Draw the pie slices.
        piesvg.selectAll("path").data(pie(pD)).enter().append("path").attr("d", arc)
            .each(function(d) { this._current = d; })
            .style("fill", function(d) { return segColor(d.data.type); })
            .on("mouseover",mouseover).on("mouseout",mouseout);

        // create function to update pie-chart. This will be used by histogram.
        pC.update = function(nD){
            piesvg.selectAll("path").data(pie(nD)).transition().duration(500)
                .attrTween("d", arcTween);
        }
        // Utility function to be called on mouseover a pie slice.
        function mouseover(d){
            // call the update function of histogram with new data.
            hG.update(fData.map(function(v){
                return [v.species,v.freq[d.data.type]];}),segColor(d.data.type));
        }
        //Utility function to be called on mouseout a pie slice.
        function mouseout(d){
            // call the update function of histogram with all data.
            hG.update(fData.map(function(v){
                return [v.species,v.total];}), barColor);
        }
        // Animating the pie-slice requiring a custom function which specifies
        // how the intermediate paths should be drawn.
        function arcTween(a) {
            var i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function(t) { return arc(i(t));    };
        }
        return pC;
    }

    // function to handle legend.
    function legend(lD){
        var leg = {};

        // create table for legend.
        var legend = d3.select(id).append("table").attr('class','legend');

        // create one row per segment.
        var tr = legend.append("tbody").selectAll("tr").data(lD).enter().append("tr");

        // create the first column for each segment.
        tr.append("td").append("svg").attr("width", '16').attr("height", '16').append("rect")
            .attr("width", '16').attr("height", '16')
            .attr("fill",function(d){ return segColor(d.type); });

        // create the second column for each segment.
        tr.append("td").text(function(d){ return d.type;});

        // create the third column for each segment.
        tr.append("td").attr("class",'legendFreq')
            .text(function(d){ return d3.format(",")(d.freq);});

        // create the fourth column for each segment.
        tr.append("td").attr("class",'legendPerc')
            .text(function(d){ return getLegend(d,lD);});

        // Utility function to be used to update the legend.
        leg.update = function(nD){
            // update the data attached to the row elements.
            var l = legend.select("tbody").selectAll("tr").data(nD);

            // update the frequencies.
            l.select(".legendFreq").text(function(d){ return d3.format(",")(d.freq);});

            // update the percentage column.
            l.select(".legendPerc").text(function(d){ return getLegend(d,nD);});
        }

        function getLegend(d,aD){ // Utility function to compute percentage.
            return d3.format("%")(d.freq/d3.sum(aD.map(function(v){ return v.freq; })));
        }

        return leg;
    }

    // calculate total frequency by segment for all species.
    var tF = ['forest','woodland','scrubland','grassland','swamp','riverbank','garden'].map(function(d){
        return {type:d, freq: d3.sum(fData.map(function(t){ return t.freq[d];}))};
    });

    // calculate total frequency by species for all segment.
    var sF = fData.map(function(d){return [d.species,d.total];});

    var hG = histoGram(sF), // create the histogram.
        pC = pieChart(tF), // create the pie-chart.
        leg= legend(tF);  // create the legend.
}

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
            var strA = a[attrib].toLowerCase(); //Since it treats uppercase as before lowercase (e.g. B > a)
            var strB = b[attrib].toLowerCase();
            return isAsc ? strA.localeCompare(strB) : strB.localeCompare(strA);
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

			if (obj.flagged == 0) //If not flagged
            {
                data = "<tr class='center aligned'>";
            }
            else //If flagged
            {
                data = "<tr class='center aligned negative'>";
            }
            data += "<td>" + a.outerHTML + "</td>"; //The first cell in the table is the link to the image
            data += "<td>" + obj.species_name + "</td>"; //Add species
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
    //populatePagesDropdown(document.getElementById("pagesDropdown").value);
}

function numberOfPages(){
    return Math.ceil((filterResults.length / resPerPage));
}

function truncate(string, len){
    if (string.length > len)
        return string.substring(0,len)+'...';
    else
        return string;
}



//CHANGE THE CURRENT VIEW PAGE FROM A VALUE CHOSEN FROM A DROPDOWN
//@param pageNum - the page to go to
function goToPage(pageNum) {
    resStart = (pageNum - 1) * resPerPage;
    displayTable(filterResults);
}

function lastPage() {
    if (filterResults.length != 0) {
        displayTable(filterResults);
        currentPage = numberOfPages();
        goToPage(currentPage);
        updatePaginationMenu($paginationMenu);
    }
}


function firstPage() {
    if (filterResults.length != 0) {
        displayTable(filterResults);
        currentPage = 1;
        goToPage(currentPage);
        updatePaginationMenu($paginationMenu);
    }
}

//GO TH THE NEXT PAGE IN THE TABLE
function nextPage() {
    if(currentPage < numberOfPages()){
        currentPage = currentPage + 1;
        goToPage(currentPage);
        updatePaginationMenu($paginationMenu);
    }
}

//GO TO THE PREVIOUS PAGE
function prevPage() {
    if(currentPage > 1){
        currentPage = currentPage - 1;
        goToPage(currentPage);
        updatePaginationMenu($paginationMenu);
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

/**
 *
 * @param menu selector
 */
function updatePaginationMenu(menu){
    /* unhide the menu */
    $("#tableFooter").removeClass('hidden');
    /* remove any page buttons already there */
    menu.find(".deletable").remove();
    var maxPages = numberOfPages();
    /* Display 2 pages before and 2 pages after the current page */
    for (var i = 2; i >= -2; i--){
        if(currentPage + i >= 1 && currentPage + i <= maxPages){
            var pageLink;
            if(i == 0){
                /* mark the current page as active */
                pageLink = $('<a class="item deletable active">' + (currentPage + i) + '</a>');
            } else{
                pageLink = $('<a class="item deletable">' + (currentPage + i) + '</a>');
            }
            pageLink.click( function() {
                var page = parseInt($(this).text());
                currentPage = page;
                goToPage(page);
                updatePaginationMenu(menu);
            });
            $("#prevPageBtn").after(pageLink);
        }
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
	//alert("OK");
    if (arguments.length > 0) {
        filters = customFilter;
        filters.query = false;
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
	//filters.query = true;
    //alert(JSON.stringify(filters));
    $.ajax({
        url: "../backend/src/api/internal/filter.php",
        type: "POST",
        data: {"params": JSON.stringify(filters)},
        success: function (json) {
            $("#tableHeadings").attr("style", "visibilty:visible");
            $("#pageInfo").attr("style", "text-align:center; visibility:visible;");
            resStart = 0; //Start at the first result
            filterResults = json.results; //Store the result
            displayTable(filterResults); //Display the result
            updatePageNum(); //Update the page numbers
            var tableHeads = $(".tableHead");
            for (var i = 0; i < tableHeads.length; i++) //Remove dropdown arrows from other headings
            {
                tableHeads[i].innerHTML = tableHeads[i].innerHTML.split("<")[0];
            }
			//console.log(JSON.stringify(filters));
            //$("#downloadCSVLink").attr("href", "../backend/src/api/internal/csv.php?id=" + json);
            document.getElementById("csvButton").disabled = false;
            updatePaginationMenu($paginationMenu);
        },
        error: function () {
            //alert("It does not work...");
            filterResults = []; //Store the result as empty
            displayTable("NO RESULT");
            updatePageNum();
        }
    });

};

$(".tabMenu").click(function(event) {
	$(".tabMenu").removeClass('active');
	$(".tab").removeClass("active");
	$(this).addClass('active');
	if ($(this).attr('id') == "resMenu") {
		$("#tableHeadings").addClass('active');
	} else if($(this).attr('id') == "chartMenu") {
		$("#chartTab").addClass('active');
	} else if ($(this).attr('id') == "statMenu"){
		$("#statTab").addClass('active');
	}
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
    this.innerHTML = this.innerHTML.split("<")[0] + "<br/>" + icons[iconIndex];
});

function populateDropdowns(){
	$.each(usesApi, function( index, dropName ) {
	  $.getJSON('../backend/src/api/internal/list.php?item=' + info[dropName][3], function(data) {
		for(var i = 0 ; i < data.length ; i++)
		{
			$("#" + dropName).find(".menu").append("<div class='item' data-value=" + data[i].id + ">" + data[i].name + "</div>");
		}
	  })
	});
}

//WHEN THERE IS A CHANGE IN THE MAIN DROPDOWN (3)
$("#masterDrop").dropdown({
    onChange: function (value, text) {
    		var usesApi = ["speciesIncludeDrop", "speciesExcludeDrop", "habitatDrop", "siteDrop"]; //The filters that need to be populaed by an api

	//The information needed for each dropdown (4)
	var info = {
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
}
});
$("#filterStore").change(function(){
		$('.filterOpt').dropdown('hide');
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
        taken_start = "1970-01-01 00:00:00"; //Set dates to default values
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
    });

//Clear the master dropdown of all labels
$("#clearMaster").click(function () {
    $("#masterDrop").dropdown('clear');
});

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


//Executed when the apply button is clicked
$('#dateTo').on('apply.daterangepicker', function (ev, picker) {
    var datetime = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
    $masterDrop.dropdown("remove label", "datetimeTo=" + prevDateTimeTo);
    $masterDrop.dropdown("remove value", "datetimeTo=" + prevDateTimeTo);
    $masterDrop.dropdown("add value", "datetimeTo=" + datetime, "Date to: " + datetime);
    $masterDrop.dropdown("add label", "datetimeTo=" + datetime, "Date to: " + datetime, "purple");
    prevDateTimeTo = datetime;
});

$(document).ready(function () {
    getRecentQueries();

    $('.ui.checkbox').checkbox(); //Initialise checkbox

    $("#resultsTable").html("<tr class='center aligned'><td colspan='11' class='align right'>Results go here...</td></tr>"); //Show were results will go (5)

    populatePagesDropdown(0); //Make the dropdown have only the value of 0 in. Looks better than a dropdown with nothing

    $('.ui.dropdown').dropdown(); //Initialise dropdown

    //Add dropdown selection to main selection, and populate dropdown with api
    $(".filterOpt").dropdown({
        action: function (text, value) {
	//When an option from the dropdown is chosen
            var chosenDropdown = event.target.parentElement.parentElement.id //The dropdown that has been chosen. Got by looking through parents of the item chosen from the dropdown
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
        }
    });
    populateDropdowns();

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

    var prevValues = { //Categories that can only have one value at a time.
        "numSpecies": "",
        "numClassifications": "",
        "minNumClassifications": "",
        "maxNumClassifications": ""
    };

    $(".filterForm").submit(function (event) {
		event.preventDefault();
		for(var e = 0 ; e < 9 ; e++) //Go through all the forms
		{
			var filterOption = event.target[e];
			var enteredValue = "";
			if(filterOption.classList[0] == "filterField") //If an input field
			{
				enteredValue = filterOption.value;
			}
			else if(filterOption.classList[0] == "filterCheck") //If a checkbox
			{
				if(filterOption.checked)
				{
					enteredValue = "true";
				}
				else
				{
					enteredValue = "false";
				}

			}
			var chosenCheck = filterOption.id; //The filter that has been chosen.
			var filterType = info[chosenCheck][0] + "="; //Name of the filter
			var labelName = info[chosenCheck][1]; //What goes on the label
			var colour = info[chosenCheck][2]; //Colour of the label
			if(enteredValue != "false" && enteredValue != "") //If a value has been chosen
			{
				if (prevValues.hasOwnProperty(chosenCheck) == true) //If it is a filter that can only be added once
				{
					if (prevValues[chosenCheck] != "") //Remove any labels already there
					{
						$masterDrop.dropdown("remove value", prevValues[chosenCheck]); //Remove the value
						$masterDrop.dropdown("remove label", prevValues[chosenCheck]); //Remove the label
					}
				}
				$masterDrop.dropdown("add value", filterType + enteredValue); //Add the value
				if(filterOption.classList[0] == "filterCheck") //If a checkbox
				{
					$masterDrop.dropdown("add label", filterType + enteredValue, labelName + ": Yes", colour); //Add the label for a checkbox
				}
				else //If a form that has a number entered
				{
					$masterDrop.dropdown("add label", filterType + enteredValue, labelName + ": " + enteredValue, colour); //Add the label
				}
				if (prevValues.hasOwnProperty(chosenCheck) == true) //Store the values so any values already there are known so does not put more than one label for categories that are not allowed
				{
					prevValues[chosenCheck] = filterType + enteredValue;
				}
			}
			else if(filterOption.classList[0] == "filterCheck")
			{
				$masterDrop.dropdown("remove label", filterType + "true");
				$masterDrop.dropdown("remove value", filterType + "true");
			}
		}
		$("#filterModal").modal("hide");
    });
    applyFilter();

    var freqData=[
    {species:'Badger',freq:{forest:4786, woodland:1319, scrubland:249, grassland:457, swamp:324, riverbank:478, garden:1234}}
    ,{species:'Blackbird',freq:{forest:1101, woodland:412, scrubland:674, grassland:412, swamp:522, riverbank:1569, garden:135}}
    ,{species:'Grey squirrel',freq:{forest:932, woodland:2149, scrubland:418, grassland:96, swamp:45, riverbank:2458, garden:89}}
    ,{species:'Rabbit',freq:{forest:832, woodland:1152, scrubland:1862, grassland:1022, swamp:458, riverbank:854, garden:125}}
    ,{species:'Red fox',freq:{forest:4481, woodland:3304, scrubland:948, grassland:1742, swamp:1234, riverbank:225, garden:1543}}
    // ,{species:'Roe Deer',freq:{forest:1619, woodland:167, scrubland:1063, grassland:62, swamp:1852, riverbank:2304, garden:1234}}
    // ,{species:'Woodpigeon',freq:{forest:1819, woodland:247, scrubland:1203, grassland:741, swamp:23, riverbank:421, garden:0}}
    // ,{species:'Small rodent',freq:{forest:4498, woodland:3852, scrubland:942, grassland:236, swamp:71, riverbank:98, garden:152}}
    ];

    dashboard('#chartTab',freqData);

});
