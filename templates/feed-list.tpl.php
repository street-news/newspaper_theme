<?php
/**
 * @file
 * Template for a feed list
 *
 * Variables:
 *   $items array, an array of item data
 *     Each item has the keys:
 *       - title
 *       - description
 *       - link (url)
 *       - pubDate
 */
?>
<ul>
  <?php foreach ($items as $i => $item): ?>
    <li>
      <div>
        <div class="row-fluid">
          <span class="date col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php print $item['pubDate']; ?></span>
          <span class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <?php print l($item['title'], $item['link'], array('attributes' => array('rel' => "noreferrer"))); ?>
          </span>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
