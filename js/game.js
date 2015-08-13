// game.js
// This script uses Ajax to dynamically handle the form submission.

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
	        var gameForm = U.$('gameForm');
	        gameForm.parentNode.insertBefore(errorDiv, gameForm);
	    } // End of messageDiv IF-ELSE
	}

    // Function called when the form is submitted.
    // Function validates the form data and performs an Ajax request.
	function validateForm(e) {

	    // Get the event object:
	    if (typeof e == 'undefined') e = window.event;

	    // Prevent the form's submission:
	    if (e.preventDefault) {
	        e.preventDefault();
	    } else {
	        e.returnValue = false;
	    }

                // Get references to the form elements:
		var result = U.$('result').value;
		var p2 = U.$('opponent').value;
                
		// Basic validation:
		if ( (result.length > 0) && (p2.length > 0) ) {
                        
                        // Perform an Ajax request:
			var ajax = U.getXMLHttpRequestObject();
			ajax.onreadystatechange = function() {

			    // Check the readyState and status code:
			    if (ajax.readyState == 4) {

			        // Check the status code:
			        if ( (ajax.status >= 200 && ajax.status < 300) 
			        || (ajax.status == 304) ) {

                                                // Check the response:
						if (ajax.responseText == 'VALID') {
                            
							// Hide the form:
							U.$('gameForm').style.visibility = 'hidden';

							// Clear the error DIV, if it exists:
							var errorDiv = U.$('errorDiv');
							if (errorDiv) {
							    errorDiv.parentNode.removeChild(errorDiv);
							}

							// Show a good message:
                                                        U.setText('message', 'Game has been logged!');
							U.$('message').className = 'good';
                                                        
                                                } else { // Bad response, show an error:
						    showErrorMessage('<h2>Error!</h2><p class="error">There was an error while adding this script.</p>');
						}

						// Clear the Ajax object:
						ajax = null;

			        } else { // Invalid status code, submit the form:
			            U.$('gameForm').submit();
			        }
  
			    } // End of readyState IF.

			}; // End of onreadystatechange anonymous function.
                        
                        
			// Perform the request:
			ajax.open('POST', 'ajax/game.php', true);
			ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			var data = 'result=' + encodeURIComponent(result) + '&opponent=' + encodeURIComponent(p2);
			ajax.send(data);    
    
			} else { // Didn't complete the form:

			    // Build up the error message:
			    var message = '<p>The following error(s) occurred:<ul>';
			    if (result.length == 0) {
			        message += '<li class="error">You forgot to select the result!</li>'
			    }
			    if (p2.length == 0) {
			        message += '<li class="error">You forgot to select the opponent!</li>'
			    }
			    message += '</ul></p>';
    
			    // Show the errors in a DIV:
			    showErrorMessage(message);

			} // End of validation IF-ELSE.
    
		    // Prevent the form's submission:
		    return false;

		} // End of validateForm() function.
                
        // Function called when the window has been loaded.
        // Function needs to add an event listener to the form.
	function init() {
	    U.addEvent(U.$('gameForm'), 'submit', validateForm);
	} // End of init() function.

	// Assign an event listener to the window's load event:
	U.addEvent(window, 'load', init);
})(); // End of immediately-invoked function.