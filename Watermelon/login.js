    /* 
    * Toggle the style of an element, without double clicking it
    * http://jsfiddle.net/JjChY/1/
    * https://stackoverflow.com/questions/11212470/double-click-to-show-an-div
    */
   function newMemberToggle(event, divID) {
    var theForm = document.getElementById(divID);
    theForm.style.display = (theForm.style.display == "none" || theForm.style.display == "") ? "block" : "none";
    toggle.theForm = theForm; // save this to hide

    // stop the event here
    if(event.stopPropagation) event.stopPropagation();
    event.cancelBubble = true;
    return false;
}   // End of the Code Snippet