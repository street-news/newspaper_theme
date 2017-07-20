<?php
/**
 * @file
 * Theme functions for the newspaper_theme theme.
 */

/**
 * Implements hook_preprocess_html().
 */
function newspaper_theme_preprocess_html(&$variables) {
  // Add body class based on taxonomy vocab.
  $term = menu_get_object('taxonomy_term', 2);
  if ($term) {
    $variables['classes_array'][] = 'vocabulary-' . $term->vocabulary_machine_name;
  }
}

/**
 * Implements hook_preprocess_page().
 */
function newspaper_theme_preprocess_page(&$variables) {
  $variables['classes_array'][] = 'container-fluid';
  if ($variables['page']['sidebar_second']) {
    $variables['content_col_span'] = 8;
  }
  else {
    $variables['content_col_span'] = 12;
  }
}

/**
 * Implements hook_preprocess_region().
 */
function newspaper_theme_preprocess_region(&$variables) {
  $ca = &$variables['classes_array'];
  if ($variables['region'] == 'sidebar_second') {
    $ca[] = 'col-xs-12';
    $ca[] = 'col-sm-12';
    $ca[] = 'col-md-4';
    $ca[] = 'col-lg-4';
  }
}

/**
 * Implements hook_preprocess_block().
 */
function newspaper_theme_preprocess_block(&$variables) {
  $block = &$variables['block'];

  if ($block->module === 'menu_block') {
    $block->subject = '';
  }

  // Remove span* from social links block in header.
  if ($block->module === 'bean' && $block->delta === 'ombubeans_sociallinks_1') {
    foreach ($variables['classes_array'] as $i => $class) {
      if (stristr($class, 'span') !== FALSE) {
        unset($variables['classes_array'][$i]);
      }
    }
  }

  // Remove span* from header menu block.
  if ($block->module === 'menu_block' && $block->delta === 'newspaper-base-3') {
    foreach ($variables['classes_array'] as $i => $class) {
      if (stristr($class, 'span') !== FALSE) {
        unset($variables['classes_array'][$i]);
      }
    }
  }


  $variables['title_attributes_array']['class'][] = 'title';
}

/**
 * Implements hook_preprocess_node().
 */
function newspaper_theme_preprocess_node(&$variables) {
  $node = $variables['node'];
  if ($node->type === 'page') {
    $variables['submitted'] = '';
  }
  else {
    $variables['submitted'] = t('by !username <span class="separator">|</span> !datetime', array(
      '!username' => $variables['name'],
      '!datetime' => format_date($variables['created'], 'newspaper_default'),
    ));
  }
}

/**
 * Implements hook_preprocess_feed_list().
 */
function newspaper_theme_preprocess_feed_list(&$variables) {
  foreach ($variables['items'] as $i => $item) {
    $timestamp = DateTime::createFromFormat('d/m/Y', $item['pubDate'])->format('U');
    $variables['items'][$i]['pubDate'] = format_date($timestamp, 'newspaper_default');
  }
}

/**
 * Implements hook_preprocess_search_results().
 */
function newspaper_theme_preprocess_search_result(&$variables) {

  // No search snippet.
  if (trim($variables['snippet']) === '...') {
    $variables['snippet'] = FALSE;
  }

  // Disqus comment counts.
  if (isset($variables['info_split']['comments'])) {
    $comments = newspaper_theme_disqus_comments_link($variables['result']['node']->entity_id);
    $variables['info_split']['comments'] = drupal_render($comments);
  }

  if (isset($variables['info_split']['user'])) {
    $variables['info_split']['author'] = $variables['info_split']['user'];
    unset($variables['info_split']['user']);
  }

  if (isset($variables['info_split']['date'])) {
    $timestamp = $variables['result']['node']->created;
    $variables['info_split']['date'] = format_date($timestamp, 'newspaper_default');
  }

  // Rebuild info.
  $variables['info'] = implode(' - ', $variables['info_split']);
}

/**
 * Utility function to return a render array to show the number of Disqus
 * comments for a given nid.
 *
 * @see disqus_node_view()
 */
function newspaper_theme_disqus_comments_link($nid) {
  $build = array();

  // Display the Disqus link.
  $link = array(
    '#type' => 'link',
    '#title' => t('Comments'),
    '#href' => 'node/' . $nid,
    '#options' => array(
      'fragment' => 'disqus_thread',
      'attributes' => array(
        // Identify the node for Disqus with the unique identifier:
        // http://docs.disqus.com/developers/universal/#comment-count
        'data-disqus-identifier' => 'node/' . $nid,
      ),
    ),
  );

  drupal_add_js(drupal_get_path('module', 'disqus') . '/disqus.js');
  drupal_add_js(array('disqusComments' => variable_get('disqus_domain', '')), 'setting');

  return $link;
}

/**
 * Implements theme_pager().
 */
function newspaper_theme_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('≪ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next »')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('active'),
            'data' => '<a href="#">' . $i . '</a>',
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array('pager-next'),
        'data' => $li_next,
      );
    }
    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
      'items' => $items,
      'attributes' => array(
        'class' => array('pagination'),
      ),
    ));
  }
}


/**
 * Implements theme_pager_link
 */
function newspaper_theme_pager_link($variables) {
  $text = $variables['text'];
  $page_new = $variables['page_new'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $attributes = $variables['attributes'];

  $fragment = '';
  if (isset($parameters['bean_element'])) {
    $fragment = $parameters['bean_element'];
    unset($parameters['bean_element']);
  }

  $page = isset($_GET['page']) ? $_GET['page'] : '';
  if ($new_page = implode(',', pager_load_array($page_new[$element], $element, explode(',', $page)))) {
    $parameters['page'] = $new_page;
  }

  $query = array();
  if (count($parameters)) {
    $query = drupal_get_query_parameters($parameters, array());
  }
  if ($query_pager = pager_get_query_parameters()) {
    $query = array_merge($query, $query_pager);
  }

  // Set each pager link title
  if (!isset($attributes['title'])) {
    static $titles = NULL;
    if (!isset($titles)) {
      $titles = array(
        t('« first') => t('Go to first page'),
        t('‹ previous') => t('Go to previous page'),
        t('next ›') => t('Go to next page'),
        t('last »') => t('Go to last page'),
      );
    }
    if (isset($titles[$text])) {
      $attributes['title'] = $titles[$text];
    }
    elseif (is_numeric($text)) {
      $attributes['title'] = t('Go to page @number', array('@number' => $text));
    }
  }

  $attributes['href'] = url($_GET['q'], array('query' => $query, 'fragment' => $fragment));
  return '<a' . drupal_attributes($attributes) . '>' . check_plain($text) . '</a>';
}

/**
 * Shows a groups of blocks for starting a search from a filter.
 */
function newspaper_theme_apachesolr_search_browse_blocks($vars) {
  $result = '';
  if ($vars['content']['#children']) {
    $result .= "<div class='apachesolr-browse-blocks'>\n<h3>" . t('Or Start a Search By:') . "</h3>\n";
    $result .= '<div class="blocks row">';
    $result .= $vars['content']['#children'] . "\n</div>\n";
    $result .= '</div>';
  }

  return $result;
}

/**
 * Implements hook_preprocess_user_profile().
 */
function newspaper_theme_preprocess_user_profile(&$variables) {
  if (isset($variables['user_profile']['summary'])) {
    unset($variables['user_profile']['summary']);
  }
}

/**
 * Implements hook_page_alter().
 *
 * Adds mobile-targeting meta tags
 */
function newspaper_theme_page_alter(&$page) {
  $meta_viewport = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' => 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0.0',
    ),
  );

  drupal_add_html_head($meta_viewport, 'meta_viewport');
}

/**
 * Implements hook_block_view_alter().
 */
function newspaper_theme_block_view_alter(&$data, $block) {
  // Remove visible-xs class from select menu, since it interferes with select2.
  if (in_array($block->module, array('menu_block'))) {
    $classes =& $data['content']['#content']['select']['#attributes']['class'];
    if ($classes) {
      unset($classes[array_search('visible-xs', $classes)]);
    }
  }
}
