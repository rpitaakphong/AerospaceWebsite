<?php
// Lightweight regression checks without a live WordPress database.
define('ABSPATH', __DIR__);
define('OBJECT', 'OBJECT');
$options = array('show_on_front'=>'page', 'page_on_front'=>73, 'page_for_posts'=>0);
$meta = array(); $inserted = array(); $existing = null; $category = (object) array('term_id'=>14);
class WP_Post { public $ID = 99; }
function register_activation_hook($file, $fn) {}
function add_filter($hook, $fn, $priority=10) {}
function add_action($hook, $fn, $priority=10) {}
function get_page_by_path($slug, $type, $post_type) { global $existing; return $existing; }
function get_option($key, $default=false) { global $options; return $options[$key] ?? $default; }
function update_option($key, $value, $autoload=false) { global $options; $options[$key]=$value; }
function add_option($key, $value, $unused='', $autoload=false) { update_option($key,$value); }
function wp_insert_post($data, $error=false) { global $inserted; $inserted[]=$data; return 1536; }
function is_wp_error($value) { return false; }
function update_post_meta($id, $key, $value) { global $meta; $meta[$id][$key]=$value; }
function get_post_meta($id, $key, $single=true) { global $meta; return $meta[$id][$key] ?? ''; }
function get_term_by($field,$value,$taxonomy) { global $category; return $category; }
function wp_reset_postdata() {}
function home_url($path) { return 'https://example.test'.$path; }
function is_front_page() { return true; }
class WP_Query {
    public $post_count=0;
    public function __construct($args) { $GLOBALS['query_args']=$args; }
    public function have_posts() { return false; }
}
function ensure($ok,$message) { if (!$ok) { throw new Exception($message); } }
require dirname(__DIR__).'/aero-homepage.php';
aero_home_activate();
ensure($inserted[0]['post_status']==='draft', 'Activation must create a draft');
ensure($inserted[0]['post_name']==='homepage2', 'Correct slug');
ensure($options['page_on_front']===73, 'Activation must not switch front page');
ensure($options['aero_home_previous_front_settings']['page_on_front']===73, 'Rollback saved');
ensure(!isset($meta[73]), 'Old homepage metadata untouched');
$menu = (object) array('object'=>'page','object_id'=>73,'url'=>'https://example.test/home/','classes'=>array());
ensure(aero_home_menu_destination(array($menu))[0]->url==='https://example.test/home/', 'Menu unchanged before launch');
$options['page_on_front']=1536;
ensure(aero_home_menu_destination(array($menu))[0]->url==='https://example.test/', 'Existing Home menu follows root after launch');
$options['page_on_front']=73;
$existing=new WP_Post();
aero_home_activate();
ensure(count($inserted)===1, 'Activation cannot duplicate or take over unrelated page');
ensure($options['aero_home_page_id']===1536, 'Unrelated existing slug cannot replace managed ID');
ob_start(); aero_home_news(); $empty=ob_get_clean();
ensure($query_args['post_status']==='publish', 'News only published');
ensure($query_args['posts_per_page']===3, 'News limited to three');
ensure($query_args['cat']===14, 'Uses actual News category');
ensure($query_args['orderby']['date']==='DESC', 'Newest first');
ensure(strpos($empty,'news-empty')!==false, 'Empty category has readable fallback');
$category=false;
ob_start(); aero_home_news(); $missing=ob_get_clean();
ensure(strpos($missing,'news-empty')!==false, 'Missing category never exposes unrelated posts');
echo "PASS: draft-only activation, original homepage retained, idempotency, rollback, News query and empty states\n";
