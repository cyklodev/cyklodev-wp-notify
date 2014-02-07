<?php

defined('ABSPATH') or die("Cannot access pages directly.");  

/*
 * Get roles 
 */
    
global $wp_roles;
$roles = $wp_roles->get_names();

/*
 * Test submit button
 */
foreach($_POST as $key => $value){
    foreach ($roles as $k => $v) {
        if($key == $k){
            $get_role_by_post = $key;
        }
    }   
}

/*
 * Get post ID 
 */

if(is_numeric($_GET['update_id'])){
    $post_data = get_post($_GET['update_id']);
    
    echo "<hr /><h3>".__("Titre de l'article",'cyklodev')."</h3><center>$post_data->post_title</center><hr />";
    
} else {
    echo '<br />';
    _e("Notifiez vos utilisateurs par role directement sur vos <a href='edit.php'>posts</a> ou directement dans le post.",'cyklodev');
    return false;
}


foreach ($roles as $k => $v) {
    if ($get_role_by_post == $k){
        echo __('Notification envoyée aux ','cyklodev').$get_role_by_post;
        $blogusers = get_users('blog_id=1&orderby=nicename&role='.$k);
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $headers[] = "From:  $blogname admin <".get_option('admin_email').">"; 
        
        foreach ($blogusers as $user) {
            echo '<li>' . $user->user_email . '</li>';
            
            if($_POST['cyklodev_notify_form'] == ''){
                $message = __('Bonjour', 'cyklodev').' '.$user->user_login." \r\n\r\n";
                $message .= __("Des nouveautés sur le site ont été publiées, l'article ", 'cyklodev').get_permalink($_GET['update_id']).__(' pourrait vous intéresser.','cyklodev')." \r\n\r\n";
                $message .= __("N'hésitez pas à le commenter ! ", 'cyklodev')."\r\n\r\n";
                $message .= __('A bientôt sur ', 'cyklodev').get_site_url()."\r\n";
                $subject = __('Un nouvel article ', 'cyklodev').$post_data->post_title.__(' sur ','cyklodev').$blogname.__(' pourrait vous intéresser.','cyklodev');
            } else {
                $message = $_POST['cyklodev_notify_form'];
                $message = preg_replace( "/USER_NAME/", $user->user_login, $message );
                $message = preg_replace( "/POST_TITLE/", $post_data->post_title, $message );
                $message = preg_replace( "/POST_URL/", get_permalink($_GET['update_id']), $message );
                $message = preg_replace( "/BLOG_URL/", get_site_url(), $message );
                $subject = $_POST['cyklodev_notify_subject'];
                $subject = preg_replace( "/POST_TITLE/", $post_data->post_title, $subject );
                $subject = preg_replace( "/BLOG_NAME/", $blogname, $subject );
                
            }

            wp_mail($user->user_email, $subject, stripslashes($message),$headers); 
        }
        return false;
    }
}


/*
 *  Function count by role
 */

function get_count_of_users($role = '') {
    $result = count_users();
    if ($role == '') {
        return $result['total_users'];
    } else {
        foreach ($result['avail_roles'] as $roles => $count)
            if ($roles == $role) {
                return $count;
            }
    }
    return 0;
}

if(get_bloginfo('language') == 'fr_FR'){
    $default_notify_subject = "Un nouvel article est disponible sur BLOG_NAME : POST_TITLE";
    $default_notify_message = "
Bonjour USER_NAME,
Des nouveautés sur le site ont été publiées, l'article POST_TITLE pourrait vous intéresser.
POST_URL
N'hésitez pas à le commenter !
A bientôt sur BLOG_URL
    ";
 } else {
    $default_notify_subject = "A new article is available on BLOG_NAME : POST_TITLE";
    $default_notify_message = "
Hello USER_NAME,
A new article is out, POST_TITLE might interest you.
POST_URL
Please leave a comment !
See you soon on BLOG_URL
    ";
 }

/*
 * Create table
 */

echo '<form action="" method="post">';
echo '  <h3>'.__("Customisez le sujet",'cyklodev').'</h3>
        <center>
            <input type="text" name="cyklodev_notify_subject" id="cyklodev_notify_subject" size="80" value="'.$default_notify_subject.'">
        <br /><b>Tips</b> : Metawords are POST_TITLE , BLOG_NAME </center>
        <hr/>';

echo '  <h3>'.__("Customisez le texte",'cyklodev').'</h3>
        <center><textarea name="cyklodev_notify_form" id="cyklodev_notify_form" rows="10" cols="80">';
        echo preg_replace( "/<br \/><br \/>/", "\n", $default_notify_message );  

        echo '</textarea>
        <br /><b>Tips</b> : Metawords are USER_NAME , POST_TITLE , POST_URL , BLOG_URL </center>
        <hr />
        <h3>'.__("Choissez le role",'cyklodev').'</h3>
        <table class="form-table" width="300px">';

foreach ($roles as $k => $v) {
    echo '
        <tr>
           <td>'.$v.' ( '.  get_count_of_users($k).' )</td>
           <td>';
    if (get_count_of_users($k) > 0){
        echo '<input type="submit" name="'.$k.'" value="'.__('Notifier','cyklodev').'" class="button"';
    }
    echo '</td> 
       </tr>

       ';
}
echo '</table></form>';
?>
<div style="position:fixed;right: 10px;bottom:25px;z-index: 0;"><img src="<?php echo plugins_url( 'images/cyklodev.png' , dirname(__FILE__) );?>" width="100px" heigth="100px"/></div>
