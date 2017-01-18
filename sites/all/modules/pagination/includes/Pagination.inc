<?php

/**
 * Handles all the pagination logic
 *
 */
class Pagination  {

  /* holds the instance of the Pagination class */
  protected static $instance = NULL;

  /* holds the paginated sections */
  protected $page            = array();

  /* holds the headers for the pages */
  protected $headers         = array();

  /* holds the nodes paging style */
  protected $style           = NULL;

  /* holds the nodes paging value */
  protected $value           = NULL;

  /* regex to parse manual breaks */
  public $re_custom          = '/(<p>\s*)?\[\s*(pagebreak\s*\]|(header\s*=\s*([^\]]*)\]))(\s*<\/p>)?/mi';
  
  /* regex to parse <h3> breaks */
  public $re_tag             = '/(<p>\s*)?<h3>\s*(.*?)\s*<\/h3>(\s*<\/p>)?/mi';
  
  /**
   * Retrieve an instance of the Pagination class.
   *
   * @return an instance of the Pagination class.
   *
   */
  public static function instance()  {
    if (!self::$instance)  {
      self::$instance = new Pagination();
    }
    return self::$instance;
  }


  /**
   * Paginate text by character count.
   *
   * @param $text
   *   The text to paginate. 
   * @param $cutoff
   *   The length of characters to cut text into.
   *
   */
  public function paginate($text, $cutoff)    {
    $textsize = strlen($text);
    do {
      $section      = $this->parseText($text, $cutoff);
      $this->page[] = $section;
      $textsize     = strlen($text);
    }
    while ($textsize > 0);
  }


  /**
   * Return the pagination style currently being used.
   *
   * @param $type
   *   The node type.
   * @return
   *   The pagination style the node type is using.
   *
   */
  public function getStyle($type)  {
    $this->style = $this->getSettings('style', $type);
    return $this->style;
  }


  /**
   * Return the pagination value currently being used.
   *
   *  @param $type
   *    The node type.
   *  @return
   *    The pagination value the node type is using.
   *
   */
  public function getValue($type)  {
    $this->value = $this->getSettings('value', $type);
    return $this->value;
  }


  /**
   * Return the page headers for a specific node.
   *
   * @param $nid
   *   The node ID of the node to be queried.
   * @return
   *   An array of page headers.
   *
   */
  public function getHeaders($nid = 0) {
    if (!$nid)  {
      return array();
    }

    static $query;
    if (!$query && $this->value > PAGINATION_AUTO)  {
      $result = db_select('node_pagination', 'n')
        ->fields('n', array('headers') )
        ->condition('nid', $nid, '=')
        ->execute()
        ->fetchObject();
      $headers = isset($result->headers) ? unserialize($result->headers) : array();
      foreach($headers as $key=>$header)  {
      //  Page 1 is always the node title
        $this->headers[$key + 1] = $header;
      }
    }
    return $this->headers;
  }


  /**
   * Return the page count.
   *
   * @return
   *   An integer corresponding to the total number of pages.
   *
   */
  public function getPageCount() {
    return count($this->page);
  }


  /**
   * Return the content of the page by page number.
   *
   * @param $page
   *   The page number to return content for.
   * @return
   *   The content of the page.
   *
   */
  public function getPage($page) {
    $section = isset($this->page[$page]) ? $this->page[$page] : '';
    return $section;
  }


  /**
   * Returns the value of $_GET['page']
   *
   * @return
   *   An integer value corresponding to the current page number, or "show" if
   *   showing the full page.
   *
   */
  public function getPageVar() {
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $page = $page > 0 ? --$page : $page;
    return $page; 
  }


  /**
   * Return a themed pager (Drupal default).
   *
   * @return
   *   HTML corresponding to a themed Drupal pager.
   *
   */
  public function getPager() {
    global $pager_page_array, $pager_total;
    $pager_page_array     = explode(',', (int) $this->getPageVar() );
    $pager_total[0]       = $this->getPageCount();
    $pager_page_array[0]  = max(0, min( (int) $pager_page_array[0], (int) $pager_total[0]) );
    $pager                = theme('pager');

    // Drupal default pager is 0 based, we'll use 1 based for ours
    $regex = '#(\?|\&)page=(.+?)(\"|&|/)#se';
    $pager = preg_replace($regex, "'$1page='.($2 + 1).''.stripslashes('$3').''", $pager);
    return $pager;
  }


  /**
   * Return a themed table of contents.
   *
   * @param $nid
   *   The nid of the node.
   * @param $ignore_title
   *   Boolean value to indicate whether "Table of Contents" string should not
   *   be rendered.
   * @return
   *   HTML corresponding to a themed ToC.
   *
   */
  public function getToc($nid, $ignore_title = FALSE) {
    if (!$nid) {
      return;
    }

    drupal_add_css(drupal_get_path('module', 'pagination') . '/css/pagination.css');
    $toc        = array();
    $headers    = $this->getHeaders($nid);
    $headers[0] = menu_get_active_title();

    foreach($headers as $key => $header)  {
      $page   = $key + 1;
      $header = str_replace(array('<br />', '<br>'), '', $header);
      $options = array(
        'attributes' => array(
          'title' => t('Go to page !page', array('!page' => $page) ),
        ),
        'query' => array('page' => $page),
      );
      $toc[]  = array(
        'data'  =>  ($this->getPageVar() == $key) ? check_plain($header) : l($header, $_GET['q'], $options),
        'class' =>  array( ($this->getPageVar() == $key) ? 'pagination-toc-item current' : 'pagination-toc-item'),
      );
    }
    $items = theme('item_list', array(
      'items'      => $toc,
      'type'       => variable_get('pagination_list_type', 'ul'),
      'attributes' => array('class' => array('pagination-toc-list') ),
    ));
    // Avoid problems with special chars displaying, likely not an exhaustive regex
    $items = preg_replace('#&amp;(\S{2,7}[^;];)#e', "'&$1'", $items);
    $toc   = theme('pagination_toc', array(
      'toc'   => $ignore_title ? NULL : t('Table of Contents:'), 
      'title' => drupal_get_title(),
      'pages' => $items,
    ));
    return $toc;
  }


  /**
   * Return pagination settings by content type.
   *
   * @param $setting
   *   The pagination setting to return. [value | style].
   * @param $type
   *   The node type.
   * @return
   *   An array of pagination settings for a specific node type.
   *
   */
  protected function getSettings($setting, $type) {

    static $pagination = array();

    if (!isset($pagination[$type]) ) {
      $result = db_select('pagination', 'p')
        ->fields('p')
        ->condition('type', $type, '=')
        ->execute()
        ->fetchObject();
      $pagination[$type] = array(
        'style' =>  isset($result->style) ? $result->style : 0,
        'value' =>  isset($result->paginate) ? $result->paginate : 0,
      );

    }

    $result = isset($pagination[$type][$setting]) ? $pagination[$type][$setting] : 0;
    return $result;

  }


  /**
   * Set the page headers.
   *
   * @param $header
   *   The page header to add to the stack.
   *
   */
  protected function setHeader($header) {
    static $first;
    if (!$first and $this->value < PAGINATION_AUTO)  {
    //  Manual paging should have a default header set for page 1
      $first = TRUE;
      $this->headers[]  = t('Page 1');
    }
    $this->headers[] = $header;
  }


  /**
   * Handle pagination depending on pagination style
   *
   * @param $text
   *   The text to paginate.
   * @param $cutoff
   *   The length of text to create sections from.
   * @return
   *   A string representing a section of text.
   *
   */
  protected function parseText(&$text, $cutoff) {

    $page_count = empty($this->headers) ? count($this->headers) + 1 : count($this->headers);
    $section    = '';

    switch ($cutoff)  {
      case 1:
        // Manual breaks w/custom headers, for [ header = random ] we expect:
        // 0:  [ header = HEADER TEXT ]
        // 4:  HEADER TEXT
        // for [ pagebreak ] we expect:
        // 0:  [ pagebreak ]
        preg_match($this->re_custom, $text, $matches);
        if (isset($matches[0]) )  {
          $header  = isset($matches[4]) && $matches[4] ? $matches[4] : t('Page !num', array('!num' => $page_count + 1) );
          $section = $this->parseSection($text, $matches, 1, 5);
          $this->setHeader($header);
        }
        else {
          // No matches, send back full text
          $section = $text;
          $text    = '';
        }
        break;
      case 2:
        // Manual breaks w/h3 tags, we except:
        // 0:  <h3>HEADER TEXT</h3>
        // 2:  HEADER TEXT
        $tag = 'h' . variable_get('pagination_header', 3);
        if ('h3' !== $tag) {
          $this->re_tag = str_replace('h3', $tag, $this->re_tag);
        }
        preg_match($this->re_tag, $text, $matches);
        if (isset($matches[0]) )  {
          $header  = isset($matches[2]) ? $matches[2] : t('Page !num', array('!num' => $page_count + 1) );
          $section = $this->parseSection($text, $matches, 1, 3);
          $this->setHeader($header);
        }
        else {
          // No matches, send back full text
          $section = $text;
          $text    = '';
        }
        break;
      default:
        // Paging by words, we'll use the english approximate 5 chars per word (+ space)
        $header  = t('Page !num', array('!num' => $page_count + 1) );
        $section = $this->parseChunk($text, $cutoff * 6);
        $text    = substr($text, strlen($section) );
        $this->setHeader($header);
        break;
    }
    return $section;
  }


  /**
   * Handle splitting of sections in odd edge cases.
   *
   * @param $text
   *   The text to parse.
   * @param $matches
   *   An array of matches.
   * @param $start
   *   An integer corresponding to the start tag position within the text.
   * @param $end
   *   An integer corresponding to the end tag position within the text.
   *
   */
  protected function parseSection(&$text, $matches, $start, $end) {
    $open    = isset($matches[$start]) && !isset($matches[$end]) ? $matches[$start] : '';
    $close   = isset($matches[$end]) && $matches[$end] && isset($matches[$start]) && !$matches[$start] ? $matches[$end] : '';
    $split   = strpos($text, $matches[0]);
    $section = substr($text, 0, $split) . $close;
    $text    = $open . substr($text, $split + strlen($matches[0]) );
    return $section;
  }


  /**
   * Handle words per page chunking.
   *
   * @param $text
   *   The text to parse.
   * @param $size
   *   The "maximum" length to create sections from the text.
   * @return
   *   The chunk of text created.
   *
   */
  protected function parseChunk($text, $size) {
    return text_summary($text, NULL, $size);
  }

}
