function test(callback, inputValue){
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