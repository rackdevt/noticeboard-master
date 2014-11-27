<?php
//Blank message created
$message = "";

//The @ suppresses an error if post[action] is not set
if (@$_POST['action'] == 'update')  
{  
    //todo: make this a method and save an array
    //Set the option to the form variable                
    updateOption('date_format');//gforms_date_limiter_date_format
    
    //Send a message to the user to let them know it was updated
    $message = '<div id="message" class="updated fade"><p><strong>' . __('Options saved','gravityforms') . '</strong></p></div>';  
}

//Get our options
$options['gforms_date_limiter_date_format'] = get_option('gforms_date_limiter_date_format');        


//Display options form
echo '<div class="wrap">' . $message;                     
echo '<div class="icon32"><br /></div>';
echo '<h2>Gravity Forms Date Limiter</h2>';
echo '<form method="post" action="">';
echo '<input type="hidden" name="action" value="update" />';

//Other options
echo '<h3>Other options</h3>
        <p>These options are site wide and WILL override settings on individual lists.</p>
        <table class="form-table">
        <tbody>      
        <tr valign="top">
        <th scope="row">Date format</th>
        <td><fieldset><legend class="screen-reader-text"><span>Date format</span></legend>
        <label for="date_format"><input type="text" value="' . ($options['gforms_date_limiter_date_format'] == "" ? 'dd/mm/yy' : $options['gforms_date_limiter_date_format']) . '" name="date_format" id="date_format"/></label>            
        </fieldset>
        <span>This should be in the format dd/mm/yy.</span></td>
        </tr>         
</tbody></table>';
//Save button
echo '<p><input type="submit" class="button-primary" value="Save Changes" /></p>';

function updateOption($optionName){
    if(@$_POST[$optionName]){
        update_option('gforms_date_limiter_' . $optionName, $_POST[$optionName]);
    }
    else{
        update_option('gforms_date_limiter_' . $optionName, '');
    }
    
}