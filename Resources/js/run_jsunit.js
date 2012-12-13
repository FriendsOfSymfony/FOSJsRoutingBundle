function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3001, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    console.log("'waitFor()' timeout");
                    phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 100); //< repeat check every 250ms
}

if (phantom.args.length === 0) {
    console.log('Usage: phantomjs run_jsunit.js <filepath>');
    phantom.exit();
} else {
    var page = new WebPage();

    page.onConsoleMessage = function(msg) {
        console.log(msg);
    };

    page.open(phantom.args[0], function(status) {
        if (status === 'success') {
            waitFor(function() {
                return page.evaluate(function() {
                    return G_testRunner.isFinished();
                });
            }, function() {
                var exitCode = page.evaluate(function() {
                    return G_testRunner.isSuccess() ? 0 : 1;
                });
                phantom.exit(exitCode);
            });
        } else {
            console.log("phantomjs: Unable to load page. [" + address + ']');
            phantom.exit();
        }
    });
}
