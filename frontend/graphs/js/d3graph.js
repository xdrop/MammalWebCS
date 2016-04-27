/**
 * Created by xdrop on 03/02/2016.
 */

var preinit = [
    {label: "Giraffe", count: 4},
    {label: "Tiger", count: 8},
    {label: "Goose", count: 6},
    {label: "Antelope", count: 22},
    {label: "Tiger", count: 1}
];

// set height and width of canvas
var width = 560,
    height = 500,
    radius = Math.min(width, height) / 2;

var donutWidth = 75;

var colors = d3.scale.category20c();

var legendSize = 18;
var legendSpacing = 4;



var svg = d3.select("#chart")
            .append('svg')
            .attr('width',width)
            .attr('height',height)
            .append('g')
            .attr('transform','translate(' + (width/2) + ',' + (height/2) + ')');

var arc = d3.svg.arc()
    .innerRadius(radius - donutWidth) // radius of inner donut
    .outerRadius(radius);


var pie = d3.layout.pie()
            .value(function(dataItem) {
                return dataItem.count;
            })
            .sort(null);

var tooltip = d3.select("#chart")
    .append('div')
    .attr('class','g-tooltip');

tooltip.append('div')
    .attr('class','g-label');

tooltip.append('div')
    .attr('class','g-count');

tooltip.append('div')
    .attr('class','g-percent');

function render(data){
    renderPie(data);
    renderLegend(data);

}

function renderPie(data){

    // bind
    var arcs = svg.selectAll('g.arc').data(pie(data));

    // enter
    arcs.enter()
        .append('g')
        .attr('class','arc');

    // update

    // draw the arc
    arcs.append('path')
        .attr('d',arc)
        .attr('fill', function(datum,index){
            // colour appropriately according to label
            return colors(datum.data.label);
    });

    
    //draw the percentage
    arcs.append('text')
        .attr('transform', function (datum){
            return "translate(" + arc.centroid(datum) +  ")";
        })
        .attr("dy",".35em")
        .attr('style','fill:white;')
        .attr('class','g-percentage')
        .style("text-anchor","middle")
        .text(function(datum,index){
            var total = d3.sum(dataset.map(function(d){
                return d.count;
            }));

            var percent = Math.round(1000 * datum.data.count / total) / 10;
            return percent + '%';
        });
    

    // exit
    arcs.exit().remove();

    // show tooltip on mouseover
    arcs.on('mouseover',function(d){
        var total = d3.sum(dataset.map(function(d){
            return d.count;
        }));

        var percent = Math.round(1000 * d.data.count / total) / 10;
        tooltip.select('.g-label').html(d.data.label);
        tooltip.select('.g-count').html(d.data.count);
        tooltip.select('.g-percent').html(percent + '%');
        // set from none to block
        tooltip.style('display', 'block')
                .style("left", width / 2  - 40 + "px")
                .style("top", height /2  + 50 + "px");

    });

    // hide tooltip on mouseout
    arcs.on('mouseout', function(d){
        tooltip.style('display', 'none');
    });

}

function renderLegend(data){
    // bind
    var legend = svg.selectAll('.legend')
        .data(colors.domain());

    // enter
    legend.enter().append('g').attr('class','legend');

    // update
    legend.attr('transform', function(datum,i){
        var height = legendSize + legendSpacing;
        var offset = height * colors.domain().length / 2;
        var horz = -2 * legendSize;
        var vert = i * height - offset;
        return 'translate(' + horz + ',' + vert + ')';
    });

    legend.append('rect')
        .attr('width', legendSize)
        .attr('height', legendSize)
        .style('fill', colors)
        .style('stroke', colors);

    legend.append('text')
        .attr('x',legendSize + legendSpacing)
        .attr('y',legendSize - legendSpacing)
        .text(function(item) {
            return item.toUpperCase();
        });

    // exit
    legend.exit().remove();
}


$(document).ready(function() {
    url = '../../backend/src/api/internal/list.php?item=counts';

        $.getJSON(url, function(data){
            var reg = /([\w\s]+).*/;
            dataset = data.map(function(currentArrayEntry){
                return { label: reg.exec(currentArrayEntry.name)[1], count: Math.log(currentArrayEntry.count) }
            }).filter(function(d) {
                return d.count > 0;
            })
            .sort(function(a,b) {
                return d3.ascending(a.label, b.label);
            });
            render(dataset);
        });
});





