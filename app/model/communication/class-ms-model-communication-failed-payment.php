<?php
/**
 * @copyright Incsub (http://incsub.com/)
 *
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License, version 2, as  
 * published by the Free Software Foundation.                           
 *
 * This program is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of       
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
 * GNU General Public License for more details.                         
 *
 * You should have received a copy of the GNU General Public License    
 * along with this program; if not, write to the Free Software          
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               
 * MA 02110-1301 USA                                                    
 *
*/

/**
 * Communication model -  failed payment.
 *
 * Persisted by parent class MS_Model_Custom_Post_Type.
 *
 * @since 1.0.0
 * @package Membership
 * @subpackage Model
 */
class MS_Model_Communication_Failed_Payment extends MS_Model_Communication {
	
	/**
	 * Model custom post type.
	 *
	 * Both static and class property are used to handle php 5.2 limitations.
	 *
	 * @since 1.0.0
	 * @var string $POST_TYPE
	 * @var string $post_type is inherited.
	 */
	public static $POST_TYPE = 'ms_communication';
	
	/**
	 * Communication type.
	 *
	 * @since 1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_FAILED_PAYMENT;
	
	/**
	 * Add action to payment failed event.
	 *
	 * @since 1.0.0
	 * @var string The communication type.
	 */
	public function after_load() {
	
		parent::after_load();
		
		if( $this->enabled ) {
			$this->add_action( 'ms_model_event_' . MS_Model_Event::TYPE_PAYMENT_FAILED, 'enqueue_messages', 10, 2 );
		}
	}
	
	/**
	 * Get communication description.
	 *
	 * @since 1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __( 'Sent when a member automatic recurring payment fails.', MS_TEXT_DOMAIN );
	}
	
	/**
	 * Communication default communication.
	 *
	 * @since 1.0.0
	 */
	public function reset_to_default() {
	
		parent::reset_to_default();
		
		$this->subject = __( 'Your membership payment has failed', MS_TEXT_DOMAIN );
		$this->message = self::get_default_message();
		$this->enabled = false;
		$this->save();

		do_action( 'ms_model_communication_reset_to_default_after', $this->type, $this );
	}
	
	/**
	 * Get default email message.
	 *
	 * @since 1.0.0
	 * @return string The email message.
	 */
	public static function get_default_message() { //i18n please
		
		ob_start();
		?>
			<h2>Hi, <?php echo self::COMM_VAR_USERNAME; ?>,</h2>
			<br /><br />
			Unfortunately, your recurring payment for your <?php echo self::COMM_VAR_MS_NAME; ?> membership at <?php echo self::COMM_VAR_BLOG_NAME; ?> has failed.
			<br /><br />
			To continue as a member, please review and edit your billing information as necessary in your account here: <?php echo self::COMM_VAR_MS_ACCOUNT_PAGE_URL; ?>
			<br /><br />
			Here is your latest invoice which is now due:
			<br /><br />
			<?php echo self::COMM_VAR_MS_INVOICE; ?>
		<?php 
		$html = ob_get_clean();
		
		return apply_filters( 'ms_model_communication_failed_payment_get_default_message', $html );
	}
}