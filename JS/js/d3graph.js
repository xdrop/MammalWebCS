/**
 * Created by xdrop on 03/02/2016.
 */

var dataset = [
    {label: "Giraffe", count: 4},
    {label: "Tiger", count: 8},
    {label: "Goose", count: 6}
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



var path = svg.selectAll('path')
    .data(pie(dataset))
    .enter()
    .append('path')
    .attr('d',arc)
    .attr('fill',function(dataItem, index) {
        return colors(dataItem.data.label);
    });

path.on('mouseover',function(d){
    var total = d3.sum(dataset.map(function(d){
        return d.count;
    }));

    var percent = Math.round(1000 * d.data.count / total) / 10;
    tooltip.select('.g-label').html(d.data.label);
    tooltip.select('.g-count').html(d.data.count);
    tooltip.select('.g-percent').html(percent + '%');
    // set from none to block
    tooltip.style('display', 'block');

});

path.on('mouseout', function(d){
    tooltip.style('display', 'none');
});

path.append('text')
    .attr("transform", function(d){
        return "translate(" + arc.centroid(d) + ")";
    })
    .attr("dy",".35em")
    .style("text-anchor","middle")
    .text("test");

var legend = svg.selectAll('.legend')
    .data(colors.domain())
    .enter()
    .append('g')
    .attr('class','legend')
    .attr('transform', function(dataItem, i) {
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



