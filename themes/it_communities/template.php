<?php

/**
 * @file
 * Template overrides, pre- process and alter hooks for IT Communities theme.
 */

/**
 * Implements template_preprocess_page().
 */

function it_communities_user_picture($user){
 $ck=module_exists('rb_ba')&&module_exists('rb_api')?TRUE:FALSE; 
 if($ck){$RB=new RBStarter();return $RB->user_picture($user);}
 else{return theme('user_picture',array('account'=>$user));}  
}
function it_communities_preprocess_comment(&$vars){
 unset($vars['permalink']);
 $vars['picture']=it_communities_user_picture(user_load($vars['elements']['#comment']->uid));
 
}
function it_communities_js_alter(&$javascript){
 /*// Remove the Drupal core version on Admin Area
 if(arg(0)!='admin'||!(arg(1)=='add'&&arg(2)=='edit')||arg(0)!='panels'||arg(0)!='ctools'){
  $i=cp.'/js/lib/jquery/jquery-1.7.2.min.js';
  $javascript[$i]['version']='1.7.2';
  $javascript[$i]['data']=$i;
  $javascript[$i]['scope']='header';
  $javascript[$i]['weight']=-50;
  $javascript[$i]['group']=JS_LIBRARY;
  $javascript[$i]['every_page']=TRUE;
  $javascript[$i]['type']='file';
  $javascript[$i]['preprocess']=TRUE;
  $javascript[$i]['cache']=TRUE;
  $javascript[$i]['defer']=FALSE;
  unset($javascript['misc/jquery.js']);
 }*/
}
function it_communities_preprocess_page(&$vars) {
	

  // Classes template.
  $vars['class_sidebar_first'] = "";
  $vars['class_sidebar_second'] = "";
  $vars['class_content'] = "";

  if (!empty($vars['page']['sidebar_first'])) {
    $vars['class_sidebar_first'] = "pure-u-md-1-2 pure-u-lg-6-24";
    $vars['class_content'] = "pure-u-md-1-2 pure-u-lg-18-24";
  }
  elseif (!empty($vars['page']['sidebar_second'])) {
    $vars['class_sidebar_second'] = "pure-u-md-1-2 pure-u-lg-8-24";
    $vars['class_content'] = "pure-u-md-1-2 pure-u-lg-16-24";
  }

  if (isset($vars['node'])) {
    // Remove title page tpl for these content types.
    // @todo refactor into *.module files and use unset().
    $type = array('country_office', 'regional_bureau', 'service',
      'service_general', 'solution', 'solution_general', 'corporate',
      'learning', 'divisional_services', 'shared_post', 'answers_question');
    if (in_array($vars['node']->type, $type)) {
      $vars['title'] = '';
    }

    // Custom breadcrumbs.
    $breadcrumb = array();
    switch ($vars['node']->type) {
      case 'regional_bureau':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Regions'), 'regions');
        break;

      case 'country_office':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Regions'), 'regions');
        $wrapper = entity_metadata_wrapper('node', $vars['node']);
        $node_parent_id = $wrapper->field_regional_bureau->value()->nid;
        $node_parent = node_load($node_parent_id);
        $breadcrumb[] = l($node_parent->title, 'node/' . $node_parent_id);
        break;

      case 'service_general':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Services'), 'services');
        break;

      case 'service':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Services'), 'services');
        $wrapper = entity_metadata_wrapper('node', $vars['node']);
        $node_parent_id = $wrapper->field_service_general->value()->nid;
        $node_parent = node_load($node_parent_id);
        $breadcrumb[] = l($node_parent->title, 'node/' . $node_parent_id);
        break;

      case 'solution_general':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Solutions'), 'solutions');
        break;

      case 'solution':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Solutions'), 'solutions');
        $wrapper = entity_metadata_wrapper('node', $vars['node']);
        $node_parent_id = $wrapper->field_solution_general->value()->nid;
        $node_parent = node_load($node_parent_id);
        $breadcrumb[] = l($node_parent->title, 'node/' . $node_parent_id);
        break;

      case 'divisional_services':
      case 'corporate':
      case 'learning':
        $breadcrumb[] = l(t('Home'), '<front>');
        break;

      case 'document':
      case 'update':
      case 'faq':
        $breadcrumb[] = l(t('Home'), '<front>');
        // Load the 1st parent of current node.
        $node_wrapper = entity_metadata_wrapper('node', $vars['node']);
        // The $og_ref_node_id variable is set in 3 line to avoid an issue of
        // old php versions.
        $og_ref_node_id = $node_wrapper->og_group_ref->value();
        $og_ref_node_id = $og_ref_node_id[0];
        $og_ref_node_id = $og_ref_node_id->nid;
        $og_ref_node = node_load($og_ref_node_id);
        // Load the 2nd parent of current node.
        $og_ref_node_type = $og_ref_node->type;
        $og_ref_list = array('country_office', 'service', 'solution');
        if (in_array($og_ref_node_type, $og_ref_list)) {
          $og_ref_wrapper = entity_metadata_wrapper('node', $og_ref_node);
          switch ($og_ref_node_type) {
            case 'country_office':
              $parent_og_ref_id = $og_ref_wrapper->field_regional_bureau->value()->nid;
              $breadcrumb[] = l(t('Regions'), 'regions');
              break;

            case 'service':
              $parent_og_ref_id = $og_ref_wrapper->field_service_general->value()->nid;
              $breadcrumb[] = l(t('Services'), 'services');
              break;

            case 'solution':
              $parent_og_ref_id = $og_ref_wrapper->field_solution_general->value()->nid;
              $breadcrumb[] = l(t('Solutions'), 'solutions');
              break;
          }

          if (isset($parent_og_ref_id)) {
            $parent_og_ref = node_load($parent_og_ref_id);
            $breadcrumb[] = l(t('@title', array('@title'=>$parent_og_ref->title)), 'node/' . $parent_og_ref_id);
          }
        }

        $breadcrumb[] = l(t('@title', array('@title'=>$og_ref_node->title)), 'node/' . $og_ref_node_id);
        break;

      // Breadcrumbs.
      case 'answers_question':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Questions'), 'search-content', array('query'=>array('f[0]'=>'type:answers_question')));
        break;

      case 'shared_post':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Posts'), 'search-content', array('query'=>array('f[0]'=>'type:shared_post')));
        break;

      case 'learning_path':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Learning and Development'), 'learning-and-development');
        $breadcrumb[] = l(t('Learning Paths'), 'learning-and-development/paths');
        break;
    }

    drupal_set_breadcrumb($breadcrumb);
  }

  if (isset($vars['theme_hook_suggestions'][0])) {
    switch ($vars['theme_hook_suggestions'][0]) {
      case 'page__user':
        /* Remove title page tpl for user */
        $vars['title'] = '';
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('People'), 'people');
        drupal_set_breadcrumb($breadcrumb);
        break;

      case 'page__learning_and_development':
        $breadcrumb[] = l(t('Home'), '<front>');
        $breadcrumb[] = l(t('Learning and Development'), 'learning-and-development');
        drupal_set_breadcrumb($breadcrumb);
        break;
    }
  }

  // Content var for search api block.
  $vars['search_form'] = "";
  if (user_is_logged_in()) {
    $vars['search_form'] = module_invoke('views', 'block_view', 'a24aa58af91f35505445bde0eaa7752a');
  }

  // Set breadcrumbs for Questions and posts views pages.
  if (isset($vars['theme_hook_suggestions'][1])) {
    if ($vars['theme_hook_suggestions'][1] === 'page__questions_posts__%') {
      $breadcrumb[] = l(t('Home'), '<front>');
      $breadcrumb[] = l(t('Questions'), 'search-content', array('query'=>array('f[0]'=>'type:answers_question')));
      drupal_set_breadcrumb($breadcrumb);
    }
  }
}

/**
 * Implements template_preprocess_block().
 */
function it_communities_preprocess_block(&$variables) {
  if ($variables['block']->module == 'views') {
    // First, try to get info about the view from contextual links
    // - The contextual links module does not have to be on,
    // but 'Hide contextual links' must be set to 'no' (the default).
    // We try this method first because its usually
    // available and may save us some performance overhead if the view object
    // is already present.
    if (isset($variables['elements']['#views_contextual_links_info']['views_ui']['view'])) {
      $view = $variables['elements']['#views_contextual_links_info']['views_ui']['view'];
      $display_id = $variables['elements']['#views_contextual_links_info']['views_ui']['view_display_id'];
    }
    // Next, try to extrapolate the view and display name from the delta (this
    // is how views module does it) and load the view.
    else {
      // If the delta doesn't contain valid data return nothing.
      $explode = explode('-', $variables['block']->delta);
      if (count($explode) != 2) {
        return;
      }

      list($name, $display_id) = $explode;
      $view = views_get_view($name);
    }

    // Add class with view display id name.
    $variables['classes_array'][] = $display_id;
    // If we got a view and a display name, we can get the classes from it and
    // put them on our block.
    if (!empty($view) && !empty($display_id)) {
      // Get the css string as defined by the user for this display.
      if (!empty($view->display[$display_id]->display_options['css_class'])) {
        $view_css_string = $view->display[$display_id]->display_options['css_class'];
      }
      // If there are no classes set for this display, check if this display is
      // using the default (all displays) settings.
      elseif (isset($view->display[$display_id]->display_options['defaults']) && !empty($view->display[$display_id]->display_options['defaults']['css_class'])) {
        $view_css_string = $view->display['default']->display_options['css_class'];
      }
      else {
        // There's no CSS class, we can't do anything.
        return;
      }

      // There may be more than one class separated by a space.
      $view_classes = explode(' ', $view_css_string);

      if (!empty($view_classes)) {
        // Add each class to the blocks top level container with the string
        // '-container' concatenated.
        foreach ($view_classes as $view_class) {
          // Strip whitespace and add the class if we have anything left.
          $view_class = trim($view_class);
          if (!empty($view_class)) {
            $variables['classes_array'][] = $view_class;
          }
        }
      }
    }
  }
}

/**
 * Implements template_preprocess_node().
 */
function it_communities_preprocess_node(&$variables) {
  // Add variables for content types.
  $types_nodetemplate = array('country_office', 'regional_bureau', 'service',
    'service_general', 'solution', 'solution_general', 'learning', 'corporate', 'divisional_services');
  $nodetype = $variables['node']->type;
  if (in_array($nodetype, $types_nodetemplate)) {
    // Questions_discussions view, count items, view all logic.
    // @deprecated use GroupOptions::renderedMembersList() instead.
    $variables['questions_discussions'] = block_questions_discussions();
    $num_members = count(views_get_view_result('wfp_it_users_og_members', 'block_1'));
    $variables['num_members'] = $num_members;
    $variables['list_members'] = views_embed_view('wfp_it_users_og_members', 'block_group_members');
    if ($num_members > 6) {
      $variables['list_members'] .= l(t('View all team members') . ' »', 'members/' . $variables['node']->nid, array('attributes'=>array('class'=>array('show-all'))));
    }

    // Toolbar shortcut add content.
    $block_add_news = module_invoke('itc_group_content', 'block_view', 'wfp_add_news');
    $block_add_document = module_invoke('itc_group_content', 'block_view', 'wfp_add_document');

    $toolbar = $block_add_news['content'] . $block_add_document['content'];

    if (($nodetype == 'country_office') || $nodetype == 'regional_bureau') {
      $block_add_local_service = module_invoke('itc_services', 'block_view', 'wfp_add_local_service');
      $toolbar .= $block_add_local_service['content'];
      $block_add_local_solution = module_invoke('itc_solutions', 'block_view', 'wfp_add_local_solution');
      $toolbar .= $block_add_local_solution['content'];
    }

    if (($nodetype == 'solution_general') || $nodetype == 'service_general' || $nodetype == 'learning') {
      $block_add_faq = module_invoke('itc_faq', 'block_view', 'wfp_add_faq');
      $toolbar .= $block_add_faq['content'];
    }

    $variables['toolbar_add_related_content'] = '<div id="content-toolbar" class="clearfix">' . $toolbar . '</div>';
  }

  switch ($nodetype) {
    case 'solution':
      // Render fields for inside of tabs.
      $visible = array('field_users', 'field_programming_language');
      render_tab_fields($variables, $visible);
      break;

    case 'solution_general':
      // Render fields for inside of tabs.
      $visible = array('field_application_hosting', 'field_programming_language');
      render_tab_fields($variables, $visible);
      break;

    case 'shared_post':
    case 'answers_question':
    case 'faq':
    case 'update':
      // Content types with custom 2/3 responsive width.
      $variables['classes_array'][] = 'pure-g';
      $variables['classes_array'][] = 'pure-u-md-2-3';
      break;

    case 'learning_path':
      $variables['theme_hook_suggestions'][] = 'node__group';
      // Removing additional useless follow button.
      unset($variables['content']['links']);
      break;
  }
}

/**
 * Implements hook_preprocess_field().
 */
function it_communities_preprocess_field(&$variables) {
  switch ($variables['element']['#field_name']) {
    // Attach a custom template to specific fields.
    case 'field_learning_courses':
    case 'field_learning_interactive':
    case 'field_learning_on_the_job':
      $variables['theme_hook_suggestions'][] = 'field__field_group';
      break;
  }

}

/**
 * Render the fields inside of the Service and Solution tabs.
 */
function it_communities_render_tab_fields(&$variables, $visible) {
  $fields_collapsed = NULL;
  $fields_visible = NULL;

  foreach ($variables['content'] as $key=>$field) {
    // Render all entity fields.
    if (substr($key, 0, 6) === 'field_') {
      if (in_array($key, $visible)) {
        // Populate rendered visible fields.
        $fields_visible .= render($variables['content'][$key]);
      }
      else {
        // Populate rendered collapsed fields.
        $fields_collapsed .= render($variables['content'][$key]);
      }
    }
  }

  // Wrap the rendered output with custom markup.
  $fields_visible = '<div class="item-fields">' . $fields_visible . '</div>';
  $fields_collapsed = '<div class="item-fields">' .
      '<a id="toggle-link" href="#">View all specifications</a><div id="field-togglable" style="display:none;">' . $fields_collapsed . '</div></div>';
  // Add pre-rendered markup to the theme variable.
  $variables['content_specifications'] = $fields_visible . $fields_collapsed;
}

/**
 * Manage the block_questions_discussions title with html containers.
 *
 * @deprecated use GroupOptions::renderAskPost() instead.
 *
 * @return string
 *   Block questions and posts Title with containers.
 */
function block_questions_discussions() {
  // Theme - print ask and post elements near view Questions and Discussions
  // title.
  $view = views_get_view('it_questions_and_posts');
  // @todo it is unnecessary to have this button as a block, it can be loaded
  // directly from a call to _itc_global_add_content_block().
  $block_add_question = module_invoke('itc_questions_answers', 'block_view', 'wfp_add_question');
  $block_add_post = module_invoke('itc_posts', 'block_view', 'wfp_add_post');

  $text = '<div class="block-views block_questions_answers block-questions-posts">
      <div class="title-container">
      <h2>' . $view->display['block_questions_answers']->display_options['title'] . '</h2>'
      . $block_add_post['content']
      . $block_add_question['content']
      . '</div>'
      . views_embed_view('it_questions_and_posts', 'block_questions_answers')
      . '</div>';
  return $text;
}

/**
 * Implements template_html_head_alter().
 */
function it_communities_html_head_alter(&$head_elements) {
  // X-UA-Compatible meta tag.
  $head_elements['system_meta_ie_browser'] = array(
    '#type'=>'html_tag',
    '#tag'=>'meta',
    '#attributes'=>array(
      'http-equiv'=>'X-UA-Compatible',
      'content'=>'IE=edge,chrome=1',
    ),
    '#weight'=>-99999,
  );
  drupal_add_html_head($head_elements, 'system_meta_ie_browser');
}

/**
 * Implements template_preprocess_html().
 */
function it_communities_preprocess_html(&$variables) {
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $is_ie8 = (bool) preg_match('/msie 8./i', $u_agent);
  if(isset($_GET['reg'])){$variables['classes_array'][]='reg_'.strtolower($_GET['reg']);}
  if ($is_ie8) {
    drupal_add_js(drupal_get_path('theme', 'it_communities') . '/libraries/html5shiv/html5shiv.min.js', array(
      'scope'=>'header',
      'weight'=>'15',
    ));
    drupal_add_css(drupal_get_path('theme', 'it_communities') . '/css/ie8.css', array('weight'=>'10000'));
  }

  // Set theme viewport.
  $viewport = array(
    '#tag'=>'meta',
    '#attributes'=>array(
      'name'=>'viewport',
      'content'=>'width=device-width, initial-scale=1',
    ),
  );
  drupal_add_html_head($viewport, 'viewport');
}

/**
 * Implements template_preprocess_user_profile().
 */
function it_communities_preprocess_user_profile(&$variables){
 if(user_is_logged_in()){
 $user=$variables['user']; 
 if(isset($user)){
  foreach($user->data['gtd'] as $i=>$x){
   $o=array(); 
   $m='gtd_'.$i;
   $n=NULL; 
   $u=NULL;
   switch($i){
    case "picture_url":$n=user_picture($user); $n=$n?$n:variable_get('user_picture_default');  break;
	case "roles":$n='<span>'.implode(' | ',$x).'</span>'; break;
	case "phones":$n='<span>'.
	 implode(' | ',array_map(function($i){$i=(object)$i;return $i->type.' : '.$i->phone;},$x)).'</span>'; break;
	default: $n='<span>'.($x?$x:'N/A').'</span>'; break;
   };
   switch($i){
    case "picture_url":$u=NULL; break;
	case "first_name":$u=NULL; break;
	case "last_name":$u=NULL; break;
	default:$u=ucfirst(str_replace(array('-','_'),' ',$i)); break;
   };
   $o['#markup']=$u.($u?' : ':NULL).$n;
   $variables['user_profile'][$m]=$o;
  }	
 }
 }
}

/**
 * Implements template_breadcrumb().
 */
function it_communities_breadcrumb($variables) {
  $variables['breadcrumb']=empty($variables['breadcrumb'])?array('Home'):$variables['breadcrumb'];
  if (!empty($variables['breadcrumb'])) {
    // Set the breadcrumbs front page.
    if (!drupal_is_front_page()) {
      $variables['breadcrumb'][] = '<span class="active">' . drupal_get_title() . '</span>';
    }

    $breadcrumb = $variables['breadcrumb'];
    // Set link homepage and button class.
    $vars = array(
      'path'=>path_to_theme() . '/images/icons/home_dark.png',
      'width'=>10,
      'height'=>10,
      'alt'=>'Home',
    );
    $home = theme('image', $vars);
    $breadcrumb[0] = l($home, '<front>', array('html'=>TRUE));
    if (drupal_is_front_page()) {
      $breadcrumb[1] = l(t('Home'), '<front>');
    }

    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $output .= theme('item_list', array(
      'items'=>$breadcrumb,
      'type'=>'ul',
      'attributes'=>array(
        'id'=>'breadcrumbs',
        'class'=>array('breadcrumbs'),
      ),
    ));
    return $output;
  }
}

function it_communities_mail_format($body){
 $class = 'btn-primary email-link';
 $data = '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@';
 
 if($body == strip_tags($body)){
  $body = htmlspecialchars($body);	
  $body = nl2br($body);
 }
 
 $body = preg_replace($data, '<a class="'.$class.'" href="$1">$1</a>', $body);
 return $body;
}
