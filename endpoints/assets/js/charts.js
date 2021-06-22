function requestChangeFilters(callback, inputValue){
    var url = new URL('http://localhost/student-building-monitor/endpoints/Cardholders.php');
    var params = {sortBy : inputValue};
    url.search = new URLSearchParams(params).toString();
    fetch(url, {
        method: 'get',
        // may be some code of fetching comes here
    }).then(function(response) {
            if (response.status >= 200 && response.status < 300) {
                return response.json()
            }
            throw new Error(response.statusText)
        }).then(callback)
}

class ChartController {
    constructor(elementId) {
        this.elementId = elementId;
        this.chart = null
    }

    updateData (data) {
        // console.log(this);
        if(this.chart) {
            this.chart.destroy();
        }
        this.createChart('bar', data);
    }

    createChart (type, data) {
        let chartData = this.prepareBarData(data);
        const config = {
            type: 'bar',
            data: chartData,
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }}
        };
        this.chart = new Chart(
            document.getElementById(this.elementId),
            config
        );
        console.log(this.chart.chartArea);
    }

    prepareBarData(input) {
        const labels = input.map(x => x["LABEL"]);
        const counts = input.map(x => parseInt(x["COUNT"]));

        for (var random_colors=[],i=0;i<counts.length;++i) random_colors[i]='#'+(Math.random()*0xFFFFFF<<0).toString(16);

        return {
            labels: labels,
            datasets: [{
                backgroundColor: random_colors,
                data: counts,
            }]
        };
    }
}

const chartController = new ChartController('graphCanvas');
