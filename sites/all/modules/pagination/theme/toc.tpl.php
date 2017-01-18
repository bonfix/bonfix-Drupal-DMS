<?php
/**
 *  Available variables
 *  -------------------
 *  $pages  -   A themed list (item-list) of page headers which defaults to
 *              Page x if no custom header is supplied.
 *  $title  -   The page title.
 *  $toc    -   The string 'Table of Contents:'.
 *
 */
?>
<div class="pagination-toc">
<?php if ($toc): ?>
  <h4><?php print $toc ?></h4>
<?php endif ?>
  <?php print $pages ?>
</div>