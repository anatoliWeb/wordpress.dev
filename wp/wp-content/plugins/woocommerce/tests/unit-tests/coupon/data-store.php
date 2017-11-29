<?php
/**
 * Data Store Tests: Tests WC_Coupon's WC_Data_Store.
 * @package WooCommerce\Tests\Coupon
 */
class WC_Tests_Coupon_Data_Store extends WC_Unit_Test_Case {

	/**
	 * Make sure the coupon store loads.
	 *
	 * @since 2.7.0
	 */
	function test_coupon_store_loads() {
		$store = new WC_Data_Store( 'coupon' );
		$this->assertTrue( is_callable( array( $store, 'read' ) ) );
		$this->assertEquals( 'WC_Coupon_Data_Store_CPT', $store->get_current_class_name() );
	}

	/**
	 * Test coupon create.
	 * @since 2.7.0
	 */
	function test_coupon_create() {
		$code = 'coupon-' . time();
		$coupon = new WC_Coupon;
		$coupon->set_code( $code );
		$coupon->set_description( 'This is a test comment.' );
		$coupon->save();

		$this->assertEquals( $code, $coupon->get_code() );
		$this->assertNotEquals( 0, $coupon->get_id() );
	}

	/**
	 * Test coupon deletion.
	 * @since 2.7.0
	 */
	function test_coupon_delete() {
		$coupon = WC_Helper_Coupon::create_coupon();
		$coupon_id = $coupon->get_id();
		$this->assertNotEquals( 0, $coupon_id );
		$coupon->delete( true );
		$coupon = new WC_Coupon( $coupon_id );
		$this->assertEquals( 0, $coupon->get_id() );
	}

	/**
	 * Test coupon update.
	 * @since 2.7.0
	 */
	function test_coupon_update() {
		$coupon = WC_Helper_Coupon::create_coupon();
		$coupon_id = $coupon->get_id();
		$this->assertEquals( 'dummycoupon', $coupon->get_code() );
		$coupon->set_code( 'dummycoupon2' );
		$coupon->save();
		$coupon = new WC_Coupon( $coupon->get_id() );
		$this->assertEquals( 'dummycoupon2', $coupon->get_code() );
	}

	/**
	 * Test coupon reading from the DB.
	 * @since 2.7.0
	 */
	function test_coupon_read() {
		$code = 'coupon-' . time();
		$coupon = new WC_Coupon;
		$coupon->set_code( $code );
		$coupon->set_description( 'This is a test coupon.' );
		$coupon->set_usage_count( 5 );
		$coupon->save();
		$coupon_id = $coupon->get_id();

		$coupon_read = new WC_Coupon( $coupon_id );

		$this->assertEquals( 5, $coupon_read->get_usage_count() );
		$this->assertEquals( $code, $coupon_read->get_code() );
		$this->assertEquals( 'This is a test coupon.', $coupon_read->get_description() );
	}

	/**
	 * Test coupon saving.
	 * @since 2.7.0
	 */
	function test_coupon_save() {
		$coupon = WC_Helper_Coupon::create_coupon();
		$coupon_id = $coupon->get_id();
		$coupon->set_code( 'dummycoupon2' );
		$coupon->save();
		$coupon = new WC_Coupon( $coupon_id ); // Read from DB to retest
		$this->assertEquals( 'dummycoupon2', $coupon->get_code() );
		$this->assertEquals( $coupon_id, $coupon->get_id() );

		$new_coupon = new WC_Coupon;
		$new_coupon->set_code( 'dummycoupon3' );
		$new_coupon->save();
		$new_coupon_id = $new_coupon->get_id();
		$this->assertEquals( 'dummycoupon3', $new_coupon->get_code() );
		$this->assertNotEquals( 0, $new_coupon_id );
	}

	/**
	 * Test coupon increase, decrease, user usage count methods.
	 * @since 2.7.0
	 */
	function test_coupon_usage_magic_methods() {
		$coupon  = WC_Helper_Coupon::create_coupon();
		$user_id = 1;

		$this->assertEquals( 0, $coupon->get_usage_count() );
		$this->assertEmpty( $coupon->get_used_by() );

		$coupon->inc_usage_count( 'woo@woo.local' );

		$this->assertEquals( 1, $coupon->get_usage_count() );
		$this->assertEquals( array( 'woo@woo.local' ), $coupon->get_used_by() );

		$coupon->inc_usage_count( $user_id );
		$coupon->inc_usage_count( $user_id );

		$data_store = WC_Data_Store::load( 'coupon' );
		$this->assertEquals( 2, $data_store->get_usage_by_user_id( $coupon, $user_id ) );

		$coupon->dcr_usage_count( 'woo@woo.local' );
		$coupon->dcr_usage_count( $user_id );
		$this->assertEquals( 1, $coupon->get_usage_count() );
		$this->assertEquals( array( 1 ), $coupon->get_used_by() );
	}

}
