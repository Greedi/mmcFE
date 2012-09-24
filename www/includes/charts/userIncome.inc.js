?>
<script type="text/javascript" src="js/highstock.js"></script>
<script type="text/javascript" src="js/themes/mmcfe.js"></script>

<script type="text/javascript">

$(document).ready(function() {
      var chart1,
      //chart2; // globally available

      chart1 = new Highcharts.StockChart({
         chart: {
            renderTo: 'financestats',
	    height: 450,
	    zoomType: 'x',
	    spacingRight: 35,
            type: 'column'
         },
		 tooltip: {
			formatter: function() {
				var s = '<b>'+ Highcharts.dateFormat('%H:%M, %A, %b %e, %Y', this.x) +'</b>';

                $.each(this.points, function(i, point) {
				color = point.series.color;
				if (this.series.name == 'Workers') {
					s += '<br /><span style="font-weight: bold; color: '+color+'">'+this.series.name + ':' + '</span>'+ Math.round(point.y);
				}
				else if (this.series.name == 'Users') {
					s += '<br /><span style="font-weight: bold; color: '+color+'">'+this.series.name + ':' + '</span>'+ Math.round(point.y);
				}
				else {
                    s += '<br /><span style="font-weight: bold; color: '+color+'">'+this.series.name + ':' + '</span>'+ Math.round(point.y*1000)/1000;
				}
                });

                return s;
			}
		},

         title: {
            text: 'My Income',
	    x: 27.5
         },
	 subtitle: { text: 'Click & Drag over a time period to zoom', x: 27.5},
         legend: {
            enabled: false,
            verticalAlign: 'top',
            x: 27.5,
	    y: 40
         },
         rangeSelector: {
			enabled: 1,
			selected: 3,
			buttons: [{
				type: 'day',
				count: 1,
				text: '1d'
			}, {
				type: 'day',
				count: 3,
				text: '3d'
			},{
				type: 'week',
				count: 1,
				text: '1w'
			}, {
				type: 'month',
				count: 1,
				text: '1m'
			}, {
				type: 'year',
				count: 1,
				text: '1y'
			}],
		},
		plotOptions: {
                                column: {
                                        stacking: '',
                                        dataLabels: {
                                        enabled: false,
                                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                }
                },
            series: {
                marker: {
                    enabled: false
                }
            }
		},
		xAxis: {
			type: 'datetime',
			maxZoom: 1 * 24 * 3600000, // fourteen days
			title: {
				text: null
			}
		},
         yAxis: {
		 title: {
               		text: 'BTC'
            	},
		startOnTick: false,
		showFirstLabel: false
         },
         series: [{
		    //type: 'column',
        	    name: 'Credits',
        	    data: [
				<?php
				while ($row = mysql_fetch_array($userCreditsQ, MYSQL_ASSOC)) {
				//while ($row = mysql_fetch_array($userCreditsDetailQ, MYSQL_ASSOC)) {
					echo "[Date.UTC(" .date('Y, m-1, d, G, i', $row["time"]). "), " .$row["earnings"]. " ],\n\r";
				}
				?>
		    ]
		}, {
		    //type: 'column',
        	    name: 'detail',
		    data: [
				<?php
				while ($row = mysql_fetch_array($userCreditsDetailQ, MYSQL_ASSOC)) {
				//	echo "[Date.UTC(" .date('Y, m-1, d, G, i', $row["time"]). "), " .$row["earnings"]. " ],\n\r";
				}
				?>
		    ]
	}]

      });
  });
</script>

<?php
