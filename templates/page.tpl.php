<?php
/**
 * @file
 * @see modules/system/page.tpl.php
 */
?>

<?php if ($page['site_top']): ?>
  <?php print render($page['site_top']); ?>
<?php endif ?>

<div id="container" class="<?php print $classes ?>">

    <?php if ($page['ad_top']): ?> <?php print render($page['ad_top']); ?> <?php endif ?>

    <?php if ($page['header']): ?> <?php print render($page['header']); ?> <?php endif ?>

    <?php if ($page['content_top']): ?>
        <?php print render($page['content_top']); ?>
    <?php endif ?>


    <div data-type="region" data-name="main">
      <div class="container">

        <div id="row-body" class="row">

          <?php if ($page['sidebar_first']): ?> <?php print render($page['sidebar_first']); ?> <?php endif ?>

          <div class="col-xs-12 col-sm-12 col-md-<?php print $content_col_span; ?> col-lg-<?php print $content_col_span; ?>">
            <?php if ($messages): ?>
              <div id="console" class="clearfix row-fluid"><?php print $messages; ?></div>
            <?php endif; ?>
            <?php if ($page['content']): ?> <?php print render($page['content']); ?> <?php endif ?>
          </div>

          <?php if ($page['sidebar_second']): ?> <?php print render($page['sidebar_second']); ?> <?php endif ?>

        </div>

      </div>
    </div>

    <?php if ($page['footer']): ?> <?php print render($page['footer']); ?> <?php endif ?>

</div> <!--/#container -->

<?php if ($page['site_bottom']): ?>
  <?php print render($page['site_bottom']); ?>
<?php endif ?>
