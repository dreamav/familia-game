var ChartsFlotcharts = function() {

    return {
        //main function to initiate the module

        init: function() {

            App.addResizeHandler(function() {
                Charts.initPieCharts();
            });

        },

        initCharts: function() {

            if (!jQuery.plot) {
                return;
            }

            var data = [];
            var totalPoints = 250;

            // random data generator for plot charts

            function getRandomData() {
                if (data.length > 0) data = data.slice(1);
                // do a random walk
                while (data.length < totalPoints) {
                    var prev = data.length > 0 ? data[data.length - 1] : 50;
                    var y = prev + Math.random() * 10 - 5;
                    if (y < 0) y = 0;
                    if (y > 100) y = 100;
                    data.push(y);
                }
                // zip the generated y values with the x values
                var res = [];
                for (var i = 0; i < data.length; ++i) {
                    res.push([i, data[i]]);
                }

                return res;
            }



            //Tracking Curves

            function chart3() {
                if ($('#chart_3').size() != 1) {
                    return;
                }
                //tracking curves:

                var sin = [],
                    cos = [];
                for (var i = 0; i < 14; i += 0.1) {
                    sin.push([i, Math.sin(i)]);
                    cos.push([i, Math.cos(i)]);
                }

                plot = $.plot($("#chart_3"), [{
                    data: sin,
                    label: "sin(x) = -0.00",
                    lines: {
                        lineWidth: 1,
                    },
                    shadowSize: 0
                }, {
                    data: cos,
                    label: "cos(x) = -0.00",
                    lines: {
                        lineWidth: 1,
                    },
                    shadowSize: 0
                }], {
                    series: {
                        lines: {
                            show: true
                        }
                    },
                    crosshair: {
                        mode: "x"
                    },
                    grid: {
                        hoverable: true,
                        autoHighlight: false,
                        tickColor: "#eee",
                        borderColor: "#eee",
                        borderWidth: 1
                    },
                    yaxis: {
                        min: -1.2,
                        max: 1.2
                    }
                });

                var legends = $("#chart_3 .legendLabel");
                legends.each(function() {
                    // fix the widths so they don't jump around
                    $(this).css('width', $(this).width());
                });

                var updateLegendTimeout = null;
                var latestPosition = null;

                function updateLegend() {
                    updateLegendTimeout = null;

                    var pos = latestPosition;

                    var axes = plot.getAxes();
                    if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) return;

                    var i, j, dataset = plot.getData();
                    for (i = 0; i < dataset.length; ++i) {
                        var series = dataset[i];

                        // find the nearest points, x-wise
                        for (j = 0; j < series.data.length; ++j)
                            if (series.data[j][0] > pos.x) break;

                            // now interpolate
                        var y, p1 = series.data[j - 1],
                            p2 = series.data[j];

                        if (p1 == null) y = p2[1];
                        else if (p2 == null) y = p1[1];
                        else y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);

                        legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(2)));
                    }
                }

                $("#chart_3").bind("plothover", function(event, pos, item) {
                    latestPosition = pos;
                    if (!updateLegendTimeout) updateLegendTimeout = setTimeout(updateLegend, 50);
                });
            }



            //graph

            chart3();


        }
    };
}();

jQuery(document).ready(function() {    
    ChartsFlotcharts.init();
    ChartsFlotcharts.initCharts();
});