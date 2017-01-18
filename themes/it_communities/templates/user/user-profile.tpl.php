<?php
/**
 * @file
 * Default theme implementation to present all user profile data.
 *
 * This template is used when viewing a registered member's profile page,
 * e.g., example.com/user/123. 123 being the users ID.
 *
 * Use render($user_profile) to print all profile items, or print a subset
 * such as render($user_profile['user_picture']). Always call
 * render($user_profile) at the end in order to print all remaining items. If
 * the item is a category, it will contain all its profile items. By default,
 * $user_profile['summary'] is provided, which contains data on the user's
 * history. Other data can be included by modules. $user_profile['user_picture']
 * is available for showing the account picture.
 *
 * Available variables:
 *   - $user_profile: An array of profile items. Use render() to print them.
 *   - Field variables: for each field instance attached to the user a
 *     corresponding variable is defined; e.g., $account->field_example has a
 *     variable $field_example defined. When needing to access a field's raw
 *     values, developers/themers are strongly encouraged to use these
 *     variables. Otherwise they will have to explicitly specify the desired
 *     field language, e.g. $account->field_example['en'], thus overriding any
 *     language negotiation rule that was previously applied.
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-item.tpl.php
 *   Where the html is handled for each item in the group.
 * @see template_preprocess_user_profile()
 *
 * @ingroup themeable
 *
 * Custom variables. See template_preprocess_node()
 * - $flag_button: button to follow user.
 */
?>
<div class="profile clearfix"<?php print $attributes; ?>>
  <div class="title-page">
    <div class="title-container">
      <h1 class="main-title">
	  <?php print render($user_profile['gtd_first_name']); ?> 
	  <?php print render($user_profile['gtd_last_name']); ?></h1>
      <?php print $flag_button; ?>
      <?php //print render($user_profile['field_api_position']); ?>
    </div>
  </div>

  <div id="profile-info" class="clearfix">
    <div id="picture"><?php print render($user_profile['gtd_picture_url']); ?></div>
    <div class="information">
      <div><?php print render($user_profile['gtd_region']); ?></div>
      <div><?php print render($user_profile['gtd_country']); ?></div>
      <div><?php print render($user_profile['gtd_duty_station']); ?></div>
      <div><?php print render($user_profile['gtd_job_title']); ?></div>
      <div><?php print render($user_profile['gtd_org_unit_name']); ?></div>
      <div><?php print render($user_profile['gtd_phones']); ?></div>
    </div>
  </div>

  <div id="profile-role">
    <?php print render($user_profile['gtd_roles']); ?>
  </div>
</div>
