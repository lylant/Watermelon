function validateForm(searchForm) {

    // initialize the variables
    var valid = true;   // determine the form is valid or not

    if (!searchForm.searchInput.value.length) {
        // no input
        valid = false;
    }

    return valid;   // return the validity
}