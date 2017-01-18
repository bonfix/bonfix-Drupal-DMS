<?php
/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */
?>
<div id="page-wrapper">
  <div id="page">
    <!-- Masthead -->
    <header class="masthead">
      <div class="pure-g wrapper">
        <div class="pure-u-2-3 pure-u-sm-1-3">
          <?php if ($logo): ?>
            <h1 class="wfp-logo">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
                <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
              </a>
              <?php if ($site_name): ?>
                <div id="site-name">
                  <strong>
                    <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home">
                      <span><?php print $site_name; ?></span>
                    </a>
                  </strong>
                </div>
              <?php endif; ?>

            </h1>
          <?php endif; ?>

        </div>
        <div class="pure-u-1-3 pure-u-sm-2-3">
          <?php if (user_is_logged_in()): ?>
            <div id="user-menu">
             <?php /*?> <a href="<?php print url('node/add/answers-question') ?>" id="btn-ask">
                <div><?php print t('Ask') ?></div>
              </a>
              <a href="<?php print url('node/add/shared-post') ?>" id="btn-post">
                <div><?php print t('Post') ?></div>
              </a><?php */?>
              <div class="user-picture">
      <?php print it_communities_user_picture($user);?>
     </div>
              <a class="btn-logout" href="<?php print url('user/logout'); ?>">
                Logout
              </a>
            </div>
          <?php endif; ?>

          <div class="main-menu-trigger">
            <button type="button" class="pure-button small"><?php print t('Menu'); ?></button>
          </div>
          <div class="main-menu">
            <?php print render($main_menu_expanded); ?>
          </div>
        </div>


      </div>

    </header> <!-- /.section, /#header -->
    <section class="band clearfix">
      <div class="pure-g wrapper">
        <div class="pure-u-md-1-2 pure-u-lg-18-24">
          <?php if ($breadcrumb): ?>
            <?php print $breadcrumb; ?>
          <?php endif; ?>
        </div>
        <div class="search-bar pure-u-md-1-2 pure-u-lg-6-24">
          <?php
          /* print search block content - see preprocess page */
          print render($search_form);
          ?>
        </div>
      </div>
    </section>
    <section id="main-wrapper" class="main-wrapper">
      <div id="main" class="page-content pure-g wrapper clearfix">

        <?php print render($title_prefix); ?>
        <?php if ($title): ?><h1 class="title pure-u-lg-16-24" id="page-title"><?php print $title; ?></h1><?php endif; ?>
        <?php print render($title_suffix); ?>

        <?php if ($page['highlighted']): ?><div id="highlighted" class="pure-u-1"><?php print render($page['highlighted']); ?></div><?php endif; ?>
        <?php if ($messages != ""): ?>
          <div class="pure-u-1">
            <?php print $messages; ?>
          </div>
        <?php endif; ?>

        <?php if ($page['sidebar_first']): ?>
          <div id="sidebar-first" class="<?php print $class_sidebar_first; ?>">
            <div class="section">
              <?php print render($page['sidebar_first']); ?>
            </div>
          </div> <!-- /.section, /#sidebar-first -->
        <?php endif; ?>

        <div id="content" class="page-body <?php print $class_content; ?>"><div class="section">
            <a id="main-content"></a>
            <?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
            <?php print render($page['help']); ?>
            <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
            <?php print render($page['content']); ?>
            <?php print render($page['content_bottom']); ?>
            <?php print $feed_icons; ?>
          </div></div> <!-- /.section, /#content -->


        <?php if ($page['sidebar_second']): ?>
          <div id="sidebar-second" class="column sidebar <?php print $class_sidebar_second; ?>">
            <div class="section">
              <?php print render($page['sidebar_second']); ?>
            </div>
          </div> <!-- /.section, /#sidebar-second -->
        <?php endif; ?>
      </div>
    </section> <!-- /#main, /#main-wrapper -->

    <footer id="footer" class="footer">
      <div class="pure-g wrapper footer-top">
        <div class="pure-u-1 pure-u-sm-3-4">
          <?php print render($page['footer']); ?>
        </div>
      </div>
    </footer> <!-- /.section, /#footer -->
  </div>
</div>
  <!-- /#page, /#page-wrapper -->
