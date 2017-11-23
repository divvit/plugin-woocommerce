<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://divvit.com
 * @since      1.0.0
 *
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/public
 * @author     Johannes Bugiel <johannes@outofscope.io>
 */
class Divvit_Tracking_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $Divvit_Tracking The ID of this plugin.
	 */
	private $Divvit_Tracking;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $Divvit_Tracking The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct($Divvit_Tracking, $version)
	{

		$this->Divvit_Tracking = $Divvit_Tracking;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->Divvit_Tracking, plugin_dir_url(__FILE__) . 'css/divvit-tracking-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->Divvit_Tracking, plugin_dir_url(__FILE__) . 'js/divvit-tracking-public.js', array('jquery'), $this->version, false);
	}

	public function insert_divvit_order_tracking_script($order_id) {

		$frontend_id = get_option('divvit_tracking_id');
		$order = new WC_Order($order_id);
		$products = $order->get_items();
		$coupons = $order->get_used_coupons();
		?>

		<script type="text/javascript">
			<?php $this->getDivvitInitScript(); ?>
			divvit.init('<?php echo $frontend_id; ?>');
			divvit.pageview();
			divvit.orderPlaced({
				products: [
					<?php foreach($products as $single_product):
					$product = new WC_Product($single_product['product_id']);
					$product_cats = $this->getProductCats($product);
					?>
					{
						id: "<?php echo $product->get_id(); ?>",
						name: "<?php echo $product->get_title(); ?>",
						category: <?php echo json_encode($product_cats); ?>,
						price: "<?php echo substr_replace((string)$_product->price, '.', -2, 0); ?>",
						currency: "<?php echo $order->get_order_currency(); ?>",
						quantity: "<?php echo $single_product['qty']; ?>"
					},
					<?php endforeach; ?>
				],
				vouchers: [
					<?php foreach($coupons as $single_coupon):  ?>
					{
						voucher: "<?php echo $single_coupon; ?>",
						voucherDiscount: "<?php echo $order->cart_discount; ?>"
					},
					<?php endforeach; ?>
				],
				orderId: "<?php echo $order->id; ?>",
				total: "<?php echo $order->get_total(); ?>",
				totalProductsNet: "<?php echo $order->get_subtotal(); ?>",
				currency: "<?php echo $order->get_order_currency(); ?>",
				shipping: "<?php echo $order->get_total_shipping(); ?>",
				paymentMethod: "<?php echo $order->payment_method; ?>",
				customer: {
					idFields: {
						email: "<?php echo $order->billing_email; ?>"
					},
					name: "<?php echo $order->billing_first_name . ' ' . $order->billing_last_name  ?>"
				}
			});
		</script>
		<?php
	}

	public function insert_divvit_tracking_script() {
		$frontend_id = get_option('divvit_tracking_id');

		$cookie = WC()->session->get_session_cookie();
		$cookieHash = $cookie[3];

		if(is_cart()) {
			?>
			<script type="text/javascript">
				<?php $this->getDivvitInitScript(); ?>
				divvit.init('<?php echo $frontend_id; ?>');
				divvit.pageview();
				divvit.cartUpdated({
					cartID: '<?php echo $cookieHash; ?>',
					products: [
						<?php foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
							$_product = $values['data'];
							$product_cats = $this->getProductCats($_product);
						?>
						{
							id: '<?php echo $_product->id; ?>',
							name: '<?php echo $_product->post->post_name; ?>',
							category: <?php echo str_replace(array('\"', '\u', '"[', ']"'), array('"', 'u', '[', ']'), json_encode($product_cats)); ?>,
							price: '<?php echo substr_replace((string)$_product->price, '.', -2, 0); ?>',
							quantity: '<?php echo $values["quantity"]; ?>'
						},
						<?php } ?>
					],
					vouchers: [
						<?php foreach(WC()->cart->get_coupons() as $single_coupon):  ?>
						{
							voucher: "<?php echo $single_coupon->code; ?>",
							voucherDiscount: "<?php echo $single_coupon->coupon_amount; ?>"
						},
						<?php endforeach; ?>
					]
				});
			</script>
			<?php
		} else {
			?>
			<script type="text/javascript">
				<?php $this->getDivvitInitScript(); ?>
				divvit.init('<?php echo $frontend_id; ?>');
				divvit.pageview();
			</script>
			<?php
		}
	}

	public function divvit_add_cart_item() {
		$dvTrack = $_COOKIE['DV_TRACK'];
		if ($dvTrack) {
			$frontend_id = get_option('divvit_tracking_id');
			$products = '';
			foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				$product_cats = $this->getProductCats($_product);
				$price = (string)$_product->price;
				$price = substr_replace($price, '.', -2, 0);
				$quantity = $values['quantity'];
				$products .= '{"id":"'.$_product->id.'","name":"'.$_product->post->post_name.'","category":'.str_replace(array('\"', '\u', '"[', ']"'), array('"', 'u', '[', ']'), json_encode($product_cats)).',"price":"'.$price.'","quantity":"'.$quantity.'"},';
			}
			$cookie = WC()->session->get_session_cookie();
			$cookieHash = $cookie[3];
			$result = file_get_contents(
			$this->get_divvit_url('tracker').'/track.js'.
				'?i='.$frontend_id.
				'&e=cart'.
				'&v=1.0.0'.
				'&uid='.$dvTrack.
				'&m='.urlencode('{"cartID":"' . $cookieHash . '","products":['.rtrim($products, ',').']}')
			);
		}

	}

	/**
	 * Get corresponding divvit tag/tracker url
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public static function get_divvit_url($type = '')
	{
	    if ($type == 'tag') {
	        if (getenv('DIVVIT_TAG_URL') != '') {
	            return getenv('DIVVIT_TAG_URL');
	        } else {
	            return 'https://tag.divvit.com';
	        }
	    } else {
	        if (getenv('DIVVIT_TRACKING_URL') != '') {
	            return getenv('DIVVIT_TRACKING_URL');
	        } else {
	            return 'https://tracker.divvit.com';
	        }
	    }
	}

	public function getDivvitInitScript(){
		echo '!function(){var t=window.divvit=window.divvit||[];if(t.DV_VERSION="1.0.0",t.init=function(e){if(!t.bInitialized){var i=document.createElement("script");i.setAttribute("type","text/javascript"),i.setAttribute("async",!0),i.setAttribute("src","'.$this->get_divvit_url('tag').'/tag.js?id="+e);var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(i,n)}},!t.bInitialized){t.functions=["customer","pageview","cartAdd","cartRemove","cartUpdated","orderPlaced","nlSubscribed","dv"];for(var e=0;e<t.functions.length;e++){var i=t.functions[e];t[i]=function(e){return function(){return Array.prototype.unshift.call(arguments,e),t.push(arguments),t}}(i)}}}();';
	}

	public function getProductCats($product){
		$term = get_the_terms($product->id, 'product_cat');
		$cat_array = Array();

		if ($term) {
			foreach ($term as $single_term) {
				array_push($cat_array, $single_term->name);
			}
		}

		return json_encode($cat_array);
	}
}
