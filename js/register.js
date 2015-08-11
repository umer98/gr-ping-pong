// register.js
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
	        var registerForm = U.$('registerForm');
	        registerForm.parentNode.insertBefore(errorDiv, registerForm);
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
                var firstName = U.$('firstName').value;
		var lastName = U.$('lastName').value;
		var username = U.$('username').value;
		var userpass = U.$('userpass').value;
                var userpass2 = U.$('userpass2').value;

		// Basic validation:
		if ( (firstName.length > 0) && (lastName.length > 0) && (username.length > 0) && (userpass.length > 0) && (userpass2.length > 0) ) {
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
							U.$('registerForm').style.visibility = 'hidden';

							// Clear the error DIV, if it exists:
							var errorDiv = U.$('errorDiv');
							if (errorDiv) {
							    errorDiv.parentNode.removeChild(errorDiv);
							}

							// Show a good message:
							U.setText('message', 'Your registeration was successful!');
							U.$('message').className = 'good';
                                                        
                                                } else if (ajax.responseText == 'EXISTS') {
                                                    showErrorMessage('<h2>Error!</h2><p class="error">That username already exists! Please enter a different username.</p>');
                                                    
						} else { // Bad response, show an error:
						    showErrorMessage('<h2>Error!</h2><p class="error">The submitted values do not match those on file!</p>');
						}

						// Clear the Ajax object:
						ajax = null;

			        } else { // Invalid status code, submit the form:
			            U.$('registerForm').submit();
			        }
  
			    } // End of readyState IF.

			}; // End of onreadystatechange anonymous function.
        
			// Perform the request:
			ajax.open('POST', 'ajax/register.php', true);
			ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			var data = 'firstname=' + encodeURIComponent(firstName) + '&lastname=' + encodeURIComponent(lastName) + '&username=' + encodeURIComponent(username) + '&userpass=' + encodeURIComponent(userpass) + '&userpass2=' + encodeURIComponent(userpass2);
			ajax.send(data);    
    
			} else { // Didn't complete the form:

			    // Build up the error message:
			    var message = '<h2>Error!</h2><p>The following error(s) occurred:<ul>';
			    if (firstName.length == 0) {
			        message += '<li class="error">You forgot to enter your first name!</li>'
			    }
                            if (lastName.length == 0) {
			        message += '<li class="error">You forgot to enter your last name!</li>'
			    }
                            if (username.length == 0) {
			        message += '<li class="error">You forgot to enter your username!</li>'
			    }
			    if (userpass.length == 0) {
			        message += '<li class="error">You forgot to enter your password!</li>'
			    }
                            else {
                                if (userpass2.length == 0) {
                                    message += '<li class="error">You forgot to confirm your password!</li>'
                                }
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
	    U.addEvent(U.$('registerForm'), 'submit', validateForm);
	} // End of init() function.

	// Assign an event listener to the window's load event:
	U.addEvent(window, 'load', init);

})(); // End of immediately-invoked function.