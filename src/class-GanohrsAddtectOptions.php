<?php
/**
 * Add Tect - "Add Tect" will can Ad Detect!
 *
 * PHP Version >= 5.0 (Tested 8.1.6 & 7.4.28)
 *
 * @since      0.0.1
 * @package    Ganohrs Add Tect
 * @licence    GPL2
 * @link       https://ganohr.net/
 * @author     Ganohr<ganohr@gmail.com>
 */

// 直接呼び出しは禁止
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'addtect_options' ) ) :


	/**
	 * Add Tect Options class
	 *
	 * @author     Ganohr<ganohr@gmail.com>
	 * @return     void
	 */
	class GanohrsAddtectOptions {

		/**
		 * 設定ページ用の識別ID
		 */
		const PAGE_ID = 'ganohrs-addtect-options';

		/**
		 * オプション記憶用
		 */
		private $options = array();

		/**
		 * コンストラクタ
		 *
		 * @return     void
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
			add_filter( 'plugin_action_links_ganohrs-addtect/ganohrs-addtect.php', array( $this, 'add_plugin_action_links' ) );
		}

		/**
		 * プラグイン一覧に設定リンクを付加する
		 *
		 * @param array $links プラグイン一覧のリンクリスト
		 * @return array 更新後のプラグイン一覧のリンクリスト
		 */
		function add_plugin_action_links( $links ) {
			$url = admin_url( 'options-general.php?page=' . self::PAGE_ID );
			array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . __( 'Settings' ) . '</a>' );
			return $links;
		}

		/**
		 * 設定ページへプラグインを追加する
		 *
		 * @return     void
		 */
		public function add_plugin_page() {
			$load_hook = add_options_page(
				'Add Tect - "Add Tect" will Ad Detect!',
				'Add Tect - "Add Tect" will Ad Detect!',
				'manage_options',
				self::PAGE_ID,
				array( $this, 'admin_manage_page' )
			);
		}

		/**
		 * オプションページ
		 *
		 * @return     void
		 */
		public function admin_manage_page() {
			$this->options = get_option( 'ganohrs_addtect_options' );
		}

		/**
		 * ページ初期化
		 *
		 * @return     void
		 */
		public function page_init() {
			register_setting(
				'ganohrs_addtect_options_group',
				'ganohrs_addtect_options',
				array( $this, 'sanitize_and_check' )
			);

			add_settings_section(
				'ganohrs_addtect_cookie_setting_section',
				'Cookie Setting',
				null,
				'ganohrs_addtect_options'
			);
			add_settings_field(
				'use_cookie_modal',
				'Use Cookie Modal',
				array( $this, 'use_cookie_modal_callback' ),
				'ganohrs_addtect_options',
				'ganohrs_addtect_cookie_setting_section'
			);
			add_settings_section(
				'ganohrs_addtect_setting_section',
				'Ad Detect Setting',
				null,
				'ganohrs_addtect_options'
			);
			add_settings_field(
				'use_addetect_modal',
				'Use Ad Detect Modal',
				array( $this, 'use_addetect_modal_callback' ),
				'ganohrs_addtect_options',
				'ganohrs_addtect_setting_section'
			);
		}

		/**
		 * 入力値をサニタイズし、適切な値に設定する
		 *
		 * @param array $input POSTされた入力値の配列
		 * @return サニタイズされた入力値の配列
		 */
		public function sanitize_and_check( $input ) {
			$new_input = array();

			$new_input['use_cookie_modal']   = isset( $input['use_cookie_modal'] ) ? $input['use_cookie_modal'] : 'true';
			$new_input['use_addetect_modal'] = isset( $input['use_addetect_modal'] ) ? $input['use_addetect_modal'] : 'true';
			return $new_input;
		}

		/**
		 * Cookie Modalを用いるかどうかの変更用コールバック
		 *
		 * @return     void
		 */
		public function use_cookie_modal_callback() {
			$use_cookie_modal = isset( $this->options['use_cookie_modal'] ) ? $this->options['use_cookie_modal'] : '';
			if ( ! is_string( $use_cookie_modal ) || strlen( $use_cookie_modal ) === 0 ) {
				$use_cookie_modal = 'no';
			}
			?>
				<label for="use_cookie_modal-yes" ><input id="use_cookie_modal-yes" type="radio" name="ganohrs_addtect_options[use_cookie_modal]" <?php echo ( $use_cookie_modal === 'yes' ? 'checked' : '' ); ?> value="yes" />Yes</label>
				<label for="use_cookie_modal-no"  ><input id="use_cookie_modal-no"  type="radio" name="ganohrs_addtect_options[use_cookie_modal]" <?php echo ( $use_cookie_modal !== 'yes' ? 'checked' : '' ); ?> value="no"  />No </label>
			<?php
		}

		/**
		 * Ad Detect Modalを用いるかどうかの変更用コールバック
		 *
		 * @return     void
		 */
		public function use_addetect_modal_callback() {
			$use_addetect_modal = isset( $this->options['use_addetect_modal'] ) ? $this->options['use_addetect_modal'] : '';
			if ( ! is_string( $use_addetect_modal ) || strlen( $use_addetect_modal ) === 0 ) {
				$use_addetect_modal = 'fix';
			}
			?>
				<label for="use_addetect_modal-yes"><input id="use_addetect_modal-yes" type="radio" name="ganohrs_addtect_options[use_addetect_modal]" <?php echo ( $use_addetect_modal === 'fix' ? 'checked' : '' ); ?> value="fix"       />Fix    </label>
				<label for="use_addetect_modal-no" ><input id="use_addetect_modal-no"  type="radio" name="ganohrs_addtect_options[use_addetect_modal]" <?php echo ( $use_addetect_modal !== 'fix' ? 'checked' : '' ); ?> value="nothing" />Nothing</label>
			<?php
		}
	}

	if ( is_admin() ) {
		new ganohrs_addtect_options();
	}

endif;
