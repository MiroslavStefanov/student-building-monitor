function test(callback){
    fetch('http://localhost/student-building-monitor/endpoints/Cardholders.php', {
        method: 'get',
        // may be some code of fetching comes here
    }).then(function(response) {
            if (response.status >= 200 && response.status < 300) {
                return response.json()
            }
            throw new Error(response.statusText)
        }).then(callback)
}