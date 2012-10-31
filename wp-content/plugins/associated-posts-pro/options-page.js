jQuery(document).ready(function(){
// Start of the DOM ready sequence


jQuery('a.delete-link').click(function(){
  return confirm( $delete_confirm_message );
});


// End of the DOM ready sequence
});