// Ed's old code in case we need to switch back

//GO TO THE LAST PAGE OF THE TABLE 
function lastPage() {
    if (filterResults.length != 0) {
        if (filterResults.length % resPerPage != 0) //i.e. the length of filterResults = resPerPage. Ths stops it from showing empty last page when showing all
        {
            resStart = filterResults.length - (filterResults.length % resPerPage);
            console.log(resStart, resPerPage, resStart / resPerPage);
            document.getElementById("pagesDropdown").options[resStart / resPerPage].setAttribute("selected", true);
        }
        else //filterResults is a multiple of resPerPage. Need this stop a blank last page.
        {
            resStart = filterResults.length - resPerPage;
        }
        displayTable(filterResults);
    }
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
