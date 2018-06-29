<table>
  <tr>
    <th><?php _e('Profile', 'peepso-core'); ?></th>
    <td><a href="<?php echo $user->get_profileurl(); ?>"><?php echo $user->get_profileurl(); ?></a></td>
  </tr>
  <tr>
    <th><?php _e('Registration Date', 'peepso-core'); ?></th>
    <td><?php echo $user->get_date_registered(); ?></td>
  </tr>
  <tr>
    <th>Emails</th>
    <td><?php echo $user->get_email(); ?></td>
  </tr>

<?php

if( count($fields) ) {
    foreach ($fields as $key => $field) {
        ?>

  <tr>
    <th><?php _e($field->title, 'peepso-core'); ?></th>
    <td><?php $field->render(); ?></td>
  </tr>

        <?php
    }
}
?>
</table>