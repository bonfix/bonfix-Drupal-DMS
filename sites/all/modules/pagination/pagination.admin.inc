<?php

/**
 * Form handler for administrative settings.
 *
 */
function pagination_admin_settings($form_state) {
  $pg      = Pagination::instance();
  $options = node_type_get_types();

  // Drupal's theme_table runs values through check_plain, no need to escape HTML here
  // see: theme_pagination_admin_settings()
  $pagetypes = array(
    0    => t('No pagination'),
    1    => t('Manual break - custom'),
    2    => t('Manual break - <h!num>', array('!num' => variable_get('pagination_header', 3) ) ),
    150  => 150,
    300  => 300,
    450  => 450,
    600  => 600,
    750  => 750,
    1000 => 1000
  );

  $pagestyles = array(
    0 =>  t('Default paging'),
    1 =>  t('Default paging + Table of Contents'),
    2 =>  t('Table of Contents'),
  );

  $headers = array(1 => t('h1'), 2 => t('h2'), 3 => t('h3'), 4 => t('h4'), 5 => t('h5'), 6 => t('h6') );

  foreach($options as $type=>$node) {
    $form[$type]['pagination'] = array(
      '#type'          => 'select',
      '#name'          => "pagination[{$type}]",
      '#default_value' => $pg->getValue($type),
      '#options'       => $pagetypes,
    );
    $form[$type]['style'] = array(
      '#type'          => 'select',
      '#name'          => "style[{$type}]",
      '#default_value' => $pg->getStyle($type),
      '#options'       => $pagestyles,
    );
  }
  $form['pagination_ignore'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Disable pagination for a specific node'),
    '#description'   => t('Place the node ids of nodes you wish to avoid pagination. Separate multiple node ids. ex: "1, 5, 7" will ignore nodes 1, 5, and 7'),
    '#default_value' => variable_get('pagination_ignore', ''),
  );
  $form['pagination_header'] = array(
    '#type'          => 'select',
    '#title'         => t('Header tag'),
    '#description'   => t('Alter the header tag to paginate on under manual break (Default: !tag)', array('!tag' => '&lt;h3&gt;') ),
    '#options'       => $headers,
    '#default_value' => variable_get('pagination_header', 3),
  );
  $form['pagination_list_type'] = array(
    '#type'          => 'select',
    '#title'         => t('List type'),
    '#description'   => t('Alter the list type of the table of contents (Default: Unordered list)'),
    '#options'       => array('ul' => t('Unordered list'), 'ol' => t('Ordered list') ),
    '#default_value' => variable_get('pagination_list_type', 'ul'),
  );
  $form['pagination_showall'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Provide a "Show full page" link'),
    '#description'   => t('Enable a "Show full page" link below the content.'),
    '#default_value' => variable_get('pagination_showall', 1),
  );
  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => t('Save configuration'),
  );
  return $form;
}


/**
 * Submission handler for pagination_admin_settings().
 *
 */
function pagination_admin_settings_submit($form, &$form_state)	{

  $pagination = $form_state['values']['pagination'];
  $style      = $form_state['values']['style'];
  $showall    = $form_state['values']['pagination_showall'];
  $ignore     = $form_state['values']['pagination_ignore'];
  $header     = $form_state['values']['pagination_header'];
  $list_type  = $form_state['values']['pagination_list_type'];

  db_delete('pagination')->execute();

  variable_set('pagination_showall', (int) $showall);
  variable_set('pagination_ignore', $ignore);
  variable_set('pagination_header', (int) $header);
  variable_set('pagination_list_type', $list_type);

  foreach($pagination as $type => $value)	{
    if ($value > 0) {
      $fields = array(
        'type'     => $type,
        'paginate' => $value,
        'style'    => $style[$type],
      );
      db_insert('pagination')
        ->fields($fields)
        ->execute();
    }
  }

  drupal_set_message(t('Pagination settings have been updated.') );

}
