<?php

defined('ABSPATH') or die("Cannot access pages directly."); 




$options_list = array (
    'cyklodev_notify_twitter_consumer_secret'          => 'Twitter consumer secret',
    'cyklodev_notify_twitter_consumer_key'             => 'Twitter consumer key',
    'cyklodev_notify_twitter_access_token'             => 'Twitter access token',
    'cyklodev_notify_twitter_access_token_secret'      => 'Twitter access token secret'
);

foreach ($options_list as $k => $v) {
    if (isset($_POST[$k])){  
        if(is_string($_POST[$k])){
            update_option($k,$_POST[$k]);
            echo '<div style="background-color:#00ff00;" align="center">Updated !</div>';
            @header(Location);
        } else {
            echo '<div style="background-color:#ff0000;" align="center">Nice try but data not allowed !</div>';
        } 
    }
}
?>

<table class="form-table" width="300px">
<?php foreach ($options_list as $k => $v) {  ?> 
<tbody>
	<tr valign="top">
		<th scope="row"><label for="<?php echo $k;?>"><?php echo $v;?></label></th>
		<td>
                    <form action="" method="post">
                        <input type="text" name="<?php echo $k;?>" value="<?php echo get_option($k) ?>" size="60"/>
                        <input type="submit" value="Update"/>
                    </form>
                </td>
	</tr>
</tbody>
<?php } ?>
</table>
<div style="position:fixed;right: 10px;bottom:25px;z-index: 0;"><img src="<?php echo plugins_url( 'images/cyklodev.png' , dirname(__FILE__) );?>" width="100px" heigth="100px"/></div>
