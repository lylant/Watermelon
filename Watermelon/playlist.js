    /* 
    * Toggle the style of an element, without double clicking it
    * http://jsfiddle.net/JjChY/1/
    * https://stackoverflow.com/questions/11212470/double-click-to-show-an-div
    */
function newPlaylistToggle(event, divID) {
    var theForm = document.getElementById(divID);
    theForm.style.display = (theForm.style.display == "none" || theForm.style.display == "") ? "block" : "none";
    toggle.theForm = theForm; // save this to hide

    // stop the event here
    if(event.stopPropagation) event.stopPropagation();
    event.cancelBubble = true;
    return false;
}   // End of the Code Snippet

    


function validateForm(newPlaylistForm) {

    /* initialize the variables
    * regEx1 to allow alphanumeric + space
    * regEx2 checks the input is starting with whitespace to prevent naming as empty
    * regEx3 checks the input is exceeded 30 characters, as DB allows varchar(30) */
    var regEx1 = /^[a-zA-Z0-9\s]+$/;
    var regEx2 = /^[a-zA-Z0-9][a-zA-Z0-9\s]+$/;
    var regEx3 = /^[a-zA-Z0-9][a-zA-Z0-9\s]{0,29}$/;
    var valid = true;   // determine the form is valid or not

    if (!regEx1.test(newPlaylistForm.newPlaylistField.value)) {
        // input do not satisfy regEx1: special character found
        valid = false;

        document.getElementById('newPlaylistError').style.display = "block";
        document.getElementById('newPlaylistError').innerHTML = "Only alphanumeric characters are allowed";
    }

    else if (!regEx2.test(newPlaylistForm.newPlaylistField.value)) {
        // input do not satisfy regEx2: the input is starting with space
        valid = false;

        document.getElementById('newPlaylistError').style.display = "block";
        document.getElementById('newPlaylistError').innerHTML = "Start with an alphanumeric character";
    }

    else if (!regEx3.test(newPlaylistForm.newPlaylistField.value)) {
        // input do not satisfy regEx3: the input is exceeded 30 characters
        valid = false;

        document.getElementById('newPlaylistError').style.display = "block";
        document.getElementById('newPlaylistError').innerHTML = "Maximum 30 letters are allowed";
    }

    return valid;   // return the validity
}