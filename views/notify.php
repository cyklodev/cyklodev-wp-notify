<?php

defined('ABSPATH') or die("Cannot access pages directly.");  

/*
 * Get roles  and remove 
 */
    
global $wp_roles;
$roles = $wp_roles->get_names();
//unset ($roles['administrator']);


/*
 * Get post ID 
 */
if(is_numeric($_GET['update_id'])){
    $post_data = get_post($_GET['update_id']);
    
    echo "<hr />".__("Titre de l'article",'cyklodev')." : <br /><h3>$post_data->post_title</h3><hr />";
    
} else {
    echo '<br />';
    _e("Notifiez vos utilisateurs par role directement sur vos <a href='edit.php'>posts</a> ou directement dans le post.",'cyklodev');
    return false;
}


foreach ($roles as $k => $v) {
    if ($_GET['role'] == $k){
        echo __('Notification envoyée aux ','cyklodev').$_GET['role'];
        $blogusers = get_users('blog_id=1&orderby=nicename&role='.$k);
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $headers[] = "From:  $blogname admin <".get_option('admin_email').">";
        
        foreach ($blogusers as $user) {
            echo '<li>' . $user->user_email . '</li>';
            
            $message = __('Bonjour', 'cyklodev').' '.$user->user_login." \r\n\r\n";
            $message .= __("Des nouveautés sur le site ont été publiées, l'article ", 'cyklodev').get_permalink($_GET['update_id']).__(' pourrait vous intéresser.','cyklodev')." \r\n\r\n";
            $message .= __("N'hésitez pas à le commenter ! ", 'cyklodev')."\r\n\r\n";
            $message .= __('A bientôt sur ', 'cyklodev').get_site_url()."\r\n";


            //error_log('>>>>>>'.$message.' & '.$user_email.' & '.$blogname);
            wp_mail($user->user_email, __('Un nouvel article ', 'cyklodev').$post_data->post_title.__(' sur ','cyklodev').$blogname.__(' pourrait vous intéresser.','cyklodev'), $message,$headers);
            
            
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


/*
 * Create table
 */

echo '<form action="" method="post">
        <table class="form-table" width="300px">';

foreach ($roles as $k => $v) {
    echo '
        <tr>
           <td>'.$v.' ( '.  get_count_of_users($k).' )</td>
           <td>';
    if (get_count_of_users($k) > 0){
        echo '<a href="admin.php?'.$_SERVER['QUERY_STRING'].'&role='.$k.'" class="button '.$k.'">'.__('Notifier','cyklodev').'</a>';
    }
    echo '</td> 
       </tr>

       ';
}
echo '</table>
    </form>';
?>
<div style="position:fixed;right: 10px;bottom:25px;z-index: 0;"><img src="<?php echo plugins_url( 'images/cyklodev.png' , dirname(__FILE__) );?>" width="100px" heigth="100px"/></div>
