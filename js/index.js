// index.js
// This script uses Ajax to dynamically display the table.

// Wrap it all in an immediately-invoked function:
(function() {
    'use strict';

    // Function used for showing errors messages:
    function showErrorMessage(message) {
        var errorDiv = U.$('errorDiv');
        if (errorDiv) { // Already exists; update.
            errorDiv.innerHTML = message;
        } else { // Create and add to the page:
            errorDiv = document.createElement('div');
            errorDiv.id = 'errorDiv';
            errorDiv.innerHTML = message;
            var leaderForm = U.$('leaderForm');
            leaderForm.parentNode.insertBefore(errorDiv, leaderForm);
        } // End of messageDiv IF-ELSE
    }

    // Function called when the form is submitted.
    // Function validates the form data and performs an Ajax request.
    function updateBoard() {
        // Get references to the form elements:
        var leaderboard = U.$('leaderboard').value;
        var weekStart, weekEnd;
        
        // Basic validation:
        if ( leaderboard.length > 0 ) {
            if (leaderboard == 2){
                var weekEnd = ((Date.today().toString("yyyy-MM-dd")) + ' 23:59:59');
                if (Date.getDayNumberFromName(Date.today().toString("ddd")) == 1) {
                    var weekStart = ((Date.today().toString("yyyy-MM-dd")) + ' 00:00:00');
                } else {
                    var weekStart = ((Date.today().previous().monday().toString("yyyy-MM-dd")) + ' 00:00:00');
                }
            }
            else if (leaderboard == 3) {
                weekStart = ((Date.today().add(-7).days().monday().toString("yyyy-MM-dd")) + ' 00:00:00');
                weekEnd = ((Date.today().previous().sunday().toString("yyyy-MM-dd")) + ' 23:59:59');
            }
                // Perform an Ajax request:
            var ajax = U.getXMLHttpRequestObject();
            ajax.onreadystatechange = function() {

                // Check the readyState and status code:
                if (ajax.readyState == 4) {

                    // Check the status code:
                    if ( (ajax.status >= 200 && ajax.status < 300) 
                    || (ajax.status == 304) ) {

                        // Check the response:
                        if (ajax.responseText != 'INVALID') {
                            
                            var data = JSON.parse(ajax.responseText);
                            
                            // Clear the error DIV, if it exists:
                            var errorDiv = U.$('errorDiv');
                            if (errorDiv) {
                                errorDiv.parentNode.removeChild(errorDiv);
                            }
                           
                            // If data was returned, update the table:
                            if (data.length > 0) {
                                $("#noData").remove();
                                $("#leaderTable tbody tr").remove();
                                // Show a good message:
                                U.setText('message', 'Players are ordered by their win ratio.');
                                
                                // Loop through the data:
                                for (var i = 0, count = data.length; i < count; i++) {
                                    var r = Number(data[i].ratio);
                                    r = r.toFixed(4);
                                    $('<tr><td><a href=\"view.php?userId=' + data[i].userId + '\">' + data[i].name + '</a></td><td>' + r + '</td><td>' + data[i].wins + '</td><td>' + data[i].losses + '</td></tr>\n').appendTo('table tbody');
                                }
                            } else {
                                $("#noData").remove();
                                $("#leaderTable tbody tr").remove();
                                $('<h2 id="noData">No Data</h2>').appendTo('table');
                            }
                            

                        } else { // Bad response, show an error:
                            showErrorMessage('<h2>Error!</h2><p class="error">The submitted values do not match those on file!</p>');
                        }

                        // Clear the Ajax object:
                        ajax = null;

                    } else { // Invalid status code, submit the form:
                        //U.$('leaderForm').submit();
                    }

                } // End of readyState IF.

            }; // End of onreadystatechange anonymous function.

            // Perform the request:
            ajax.open('POST', 'ajax/index.php', true);
            ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            if(leaderboard == 2 || leaderboard == 3){
                var data = 'leaderboard=' + encodeURIComponent(leaderboard) + '&weekstart=' + encodeURIComponent(weekStart) + '&weekend=' + encodeURIComponent(weekEnd);
            }
            else { var data = 'leaderboard=' + encodeURIComponent(leaderboard); }
            ajax.send(data);

        } else { // Didn't complete the form:

            // Build up the error message:
            var message = '<p>The following error(s) occurred:<ul>';
            if (leaderboard.length == 0) {
                message += '<li class="error">Please select an item from the leaderboard dropdown!</li>'
            }
            message += '</ul></p>';

            // Show the errors in a DIV:
            showErrorMessage(message);

        } // End of validation IF-ELSE.

    } // End of updateBoard() function.

    // Function called when the window has been loaded.
    // Function needs to add an event listener to the form.
    function init() {
        U.addEvent(U.$('leaderboard'), 'change', updateBoard);
        updateBoard();
    } // End of init() function.
    
    U.addEvent(window, 'load', init);

})(); // End of immediately-invoked function.