<?php
/**
 * Coupon API Tests
 * @package WooCommerce\Tests\API
 * @since 2.7.0
 */
class WC_Tests_API_Coupons extends WC_REST_Unit_Test_Case {

    protected $user;

	protected $endpoint;

	/**
	 * Setup test coupon data.
	 * @since 2.7.0
	 */
	 public function setUp() {
		parent::setUp();
		$this->endpoint = new WC_REST_Coupons_Controller();
		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
	}

	/**
	 * Test route registration.
	 * @since 2.7.0
	 */
	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/wc/v1/coupons', $routes );
		$this->assertArrayHasKey( '/wc/v1/coupons/(?P<id>[\d]+)', $routes );
		$this->assertArrayHasKey( '/wc/v1/coupons/batch', $routes );
	}

	/**
	 * Test getting coupons.
	 * @since 2.7.0
	 */
	public function test_get_coupons() {
		wp_set_current_user( $this->user );

		$coupon_1 = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );

		$post_1   = get_post( $coupon_1->id);
		$coupon_2 = WC_Helper_Coupon::create_coupon( 'dummycoupon-2' );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons' ) );
		$coupons = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 2, count( $coupons ) );
		$this->assertContains( array(
			'id'             => $coupon_1->id,
			'code'           => 'dummycoupon-1',
			'amount'         => '1.00',
			'date_created'   => wc_rest_prepare_date_response( $post_1->post_date_gmt ),
			'date_modified'  => wc_rest_prepare_date_response( $post_1->post_modified_gmt ),
			'discount_type'  => 'fixed_cart',
			'description'    => 'This is a dummy coupon',
			'date_expires'    => '',
			'usage_count'    => 0,
			'individual_use' => false,
			'product_ids'    => array(),
			'excluded_product_ids' => array(),
			'usage_limit'    => '',
			'usage_limit_per_user' => '',
			'limit_usage_to_x_items' => 0,
			'free_shipping' => false,
			'product_categories' => array(),
			'excluded_product_categories' => array(),
			'exclude_sale_items' => false,
			'minimum_amount' => '0.00',
			'maximum_amount' => '0.00',
			'email_restrictions' => array(),
			'used_by' => array(),
			'meta_data' => array(),
			'_links' => array(
				'self'       => array(
					array(
						'href' => rest_url( '/wc/v1/coupons/' . $coupon_1->id ),
					),
				),
				'collection' => array(
					array(
						'href' => rest_url( '/wc/v1/coupons' ),
					),
				),
			),
		), $coupons );
	}

	/**
	 * Test getting coupons without valid permissions.
	 * @since 2.7.0
	 */
	public function test_get_coupons_without_permission() {
		wp_set_current_user( 0 );
		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons' ) );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test getting a single coupon.
	 * @since 2.7.0
	 */
	public function test_get_coupon() {
		wp_set_current_user( $this->user );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$post     = get_post( $coupon->id );
		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons/' . $coupon->id ) );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( array(
			'id'             => $coupon->id,
			'code'           => 'dummycoupon-1',
			'amount'         => '1.00',
			'date_created'   => wc_rest_prepare_date_response( $post->post_date_gmt ),
			'date_modified'  => wc_rest_prepare_date_response( $post->post_modified_gmt ),
			'discount_type'  => 'fixed_cart',
			'description'    => 'This is a dummy coupon',
			'date_expires'    => null,
			'usage_count'    => 0,
			'individual_use' => false,
			'product_ids'    => array(),
			'excluded_product_ids' => array(),
			'usage_limit'    => null,
			'usage_limit_per_user' => null,
			'limit_usage_to_x_items' => 0,
			'free_shipping' => false,
			'product_categories' => array(),
			'excluded_product_categories' => array(),
			'exclude_sale_items' => false,
			'minimum_amount' => '0.00',
			'maximum_amount' => '0.00',
			'email_restrictions' => array(),
			'used_by' => array(),
			'meta_data' => array(),
		), $data );
	}

	/**
	 * Test getting a single coupon with an invalid ID.
	 * @since 2.7.0
	 */
	public function test_get_coupon_invalid_id() {
		wp_set_current_user( $this->user );
		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons/0' ) );
		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test getting a single coupon without valid permissions.
	 * @since 2.7.0
	 */
	public function test_get_coupon_without_permission() {
		wp_set_current_user( 0 );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons/' . $coupon->id ) );
		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test creating a single coupon.
	 * @since 2.7.0
	 */
	public function test_create_coupon() {
		wp_set_current_user( $this->user );
		$request = new WP_REST_Request( 'POST', '/wc/v1/coupons' );
		$request->set_body_params( array(
			'code'          => 'test',
			'amount'        => '5.00',
			'discount_type' => 'fixed_product',
			'description'   => 'Test',
			'usage_limit'   => 10,
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 201, $response->get_status() );
		$this->assertEquals( array(
			'id'             => $data['id'],
			'code'           => 'test',
			'amount'         => '5.00',
			'date_created'   => $data['date_created'],
			'date_modified'  => $data['date_modified'],
			'discount_type'  => 'fixed_product',
			'description'    => 'Test',
			'date_expires'    => null,
			'usage_count'    => 0,
			'individual_use' => false,
			'product_ids'    => array(),
			'excluded_product_ids' => array(),
			'usage_limit'    => 10,
			'usage_limit_per_user' => null,
			'limit_usage_to_x_items' => 0,
			'free_shipping' => false,
			'product_categories' => array(),
			'excluded_product_categories' => array(),
			'exclude_sale_items' => false,
			'minimum_amount' => '0.00',
			'maximum_amount' => '0.00',
			'email_restrictions' => array(),
			'used_by' => array(),
			'meta_data' => array(),
		), $data );
	}

	/**
	 * Test creating a single coupon with invalid fields.
	 * @since 2.7.0
	 */
	public function test_create_coupon_invalid_fields() {
		wp_set_current_user( $this->user );

		// test no code...
		$request = new WP_REST_Request( 'POST', '/wc/v1/coupons' );
		$request->set_body_params( array(
			'amount'        => '5.00',
			'discount_type' => 'fixed_product',
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 400, $response->get_status() );
	}

	/**
	 * Test creating a single coupon without valid permissions.
	 * @since 2.7.0
	 */
	public function test_create_coupon_without_permission() {
		wp_set_current_user( 0 );

		// test no code...
		$request = new WP_REST_Request( 'POST', '/wc/v1/coupons' );
		$request->set_body_params( array(
			'code'          => 'fail',
			'amount'        => '5.00',
			'discount_type' => 'fixed_product',
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test updating a single coupon.
	 * @since 2.7.0
	 */
	public function test_update_coupon() {
		wp_set_current_user( $this->user );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$post     = get_post( $coupon->id );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', '/wc/v1/coupons/' . $coupon->id ) );
		$data     = $response->get_data();
		$this->assertEquals( 'This is a dummy coupon', $data['description'] );
		$this->assertEquals( 'fixed_cart', $data['discount_type'] );
		$this->assertEquals( '1.00', $data['amount'] );

		$request = new WP_REST_Request( 'PUT', '/wc/v1/coupons/' . $coupon->id );
		$request->set_body_params( array(
			'amount' => '10.00',
			'description' => 'New description',
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( '10.00', $data['amount'] );
		$this->assertEquals( 'New description', $data['description'] );
		$this->assertEquals( 'fixed_cart', $data['discount_type'] );
	}

	/**
	 * Test updating a single coupon with an invalid ID.
	 * @since 2.7.0
	 */
	public function test_update_coupon_invalid_id() {
		wp_set_current_user( $this->user );

		$request = new WP_REST_Request( 'PUT', '/wc/v1/coupons/0' );
		$request->set_body_params( array(
			'code'   => 'tester',
			'amount' => '10.00',
			'description' => 'New description',
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 400, $response->get_status() );
	}

	/**
	 * Test updating a single coupon without valid permissions.
	 * @since 2.7.0
	 */
	public function test_update_coupon_without_permission() {
		wp_set_current_user( 0 );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$post     = get_post( $coupon->id );

		$request = new WP_REST_Request( 'PUT', '/wc/v1/coupons/' . $coupon->id );
		$request->set_body_params( array(
			'amount' => '10.00',
			'description' => 'New description',
		) );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test deleting a single coupon.
	 * @since 2.7.0
	 */
	public function test_delete_coupon() {
		wp_set_current_user( $this->user );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$request = new WP_REST_Request( 'DELETE', '/wc/v1/coupons/' . $coupon->id );
		$request->set_param( 'force', true );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test deleting a single coupon with an invalid ID.
	 * @since 2.7.0
	 */
	public function test_delete_coupon_invalid_id() {
		wp_set_current_user( $this->user );
		$request = new WP_REST_Request( 'DELETE', '/wc/v1/coupons/0' );
		$request->set_param( 'force', true );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}

	/**
	 * Test deleting a single coupon without valid permissions.
	 * @since 2.7.0
	 */
	public function test_delete_coupon_without_permission() {
		wp_set_current_user( 0 );
		$coupon   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$request  = new WP_REST_Request( 'DELETE', '/wc/v1/coupons/' . $coupon->id );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test batch operations on coupons.
	 * @since 2.7.0
	 */
	public function test_batch_coupon() {
		wp_set_current_user( $this->user );

		$coupon_1   = WC_Helper_Coupon::create_coupon( 'dummycoupon-1' );
		$coupon_2   = WC_Helper_Coupon::create_coupon( 'dummycoupon-2' );
		$coupon_3   = WC_Helper_Coupon::create_coupon( 'dummycoupon-3' );
		$coupon_4   = WC_Helper_Coupon::create_coupon( 'dummycoupon-4' );

		$request = new WP_REST_Request( 'POST', '/wc/v1/coupons/batch' );
		$request->set_body_params( array(
			'update' => array(
				array(
					'id'     => $coupon_1->id,
					'amount' => '5.15',
				),
			),
			'delete' => array(
				$coupon_2->id,
				$coupon_3->id,
			),
			'create' => array(
				array(
					'code'   => 'new-coupon',
					'amount' => '11.00',
				),
			),
		) );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( '5.15', $data['update'][0]['amount'] );
		$this->assertEquals( '11.00', $data['create'][0]['amount'] );
		$this->assertEquals( 'new-coupon', $data['create'][0]['code'] );
		$this->assertEquals( $coupon_2->id, $data['delete'][0]['id'] );
		$this->assertEquals( $coupon_3->id, $data['delete'][1]['id'] );

		$request = new WP_REST_Request( 'GET', '/wc/v1/coupons' );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 3, count( $data ) );
	}

	/**
	 * Test coupon schema.
	 * @since 2.7.0
	 */
	public function test_coupon_schema() {
		wp_set_current_user( $this->user );
		$request = new WP_REST_Request( 'OPTIONS', '/wc/v1/coupons' );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();
		$properties = $data['schema']['properties'];

		$this->assertEquals( 24, count( $properties ) );
		$this->assertArrayHasKey( 'id', $properties );
		$this->assertArrayHasKey( 'code', $properties );
		$this->assertArrayHasKey( 'date_created', $properties );
		$this->assertArrayHasKey( 'date_modified', $properties );
		$this->assertArrayHasKey( 'description', $properties );
		$this->assertArrayHasKey( 'discount_type', $properties );
		$this->assertArrayHasKey( 'amount', $properties );
		$this->assertArrayHasKey( 'date_expires', $properties );
		$this->assertArrayHasKey( 'usage_count', $properties );
		$this->assertArrayHasKey( 'individual_use', $properties );
		$this->assertArrayHasKey( 'product_ids', $properties );
		$this->assertArrayHasKey( 'excluded_product_ids', $properties );
		$this->assertArrayHasKey( 'usage_limit', $properties );
		$this->assertArrayHasKey( 'usage_limit_per_user', $properties );
		$this->assertArrayHasKey( 'limit_usage_to_x_items', $properties );
		$this->assertArrayHasKey( 'free_shipping', $properties );
		$this->assertArrayHasKey( 'product_categories', $properties );
		$this->assertArrayHasKey( 'excluded_product_categories', $properties );
		$this->assertArrayHasKey( 'exclude_sale_items', $properties );
		$this->assertArrayHasKey( 'minimum_amount', $properties );
		$this->assertArrayHasKey( 'maximum_amount', $properties );
		$this->assertArrayHasKey( 'email_restrictions', $properties );
		$this->assertArrayHasKey( 'used_by', $properties );
	}
}
