<?php
/**
 * Notification manager.
 *
 * @package HBT_Trendyol_Profit_Tracker
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

class HBT_Notification_Manager {

	/**
	 * Database instance.
	 *
	 * @var HBT_Database
	 */
	private HBT_Database $db;

	/**
	 * Singleton instance.
	 *
	 * @var HBT_Notification_Manager|null
	 */
	private static ?HBT_Notification_Manager $instance = null;

	/**
	 * Get singleton.
	 *
	 * @return HBT_Notification_Manager
	 */
	public static function instance(): HBT_Notification_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->db = HBT_Database::instance();

		// Hook admin notices display.
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
        
		// YENİ: Kategori bazlı bildirimleri toplu okundu yapmak için AJAX kancası
		add_action( 'wp_ajax_hbt_dismiss_notice_type', array( $this, 'ajax_dismiss_notice_type' ) );
	}

	/**
	 * AJAX: Belirli bir kategorideki tüm bildirimleri okundu yapar.
	 */
	public function ajax_dismiss_notice_type(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Yetkisiz erişim.' );
		}
		
		$type = isset( $_POST['notice_type'] ) ? sanitize_text_field( $_POST['notice_type'] ) : '';
		
		if ( ! empty( $type ) ) {
			global $wpdb;
			$table = $wpdb->prefix . 'hbt_notifications';
			// Gelen tipe (kategoriye) ait tüm okunmamış bildirimleri okundu (1) yap
			$wpdb->update(
				$table,
				array( 'is_read' => 1 ),
				array( 'notification_type' => $type, 'is_read' => 0 )
			);
		}
		wp_send_json_success();
	}

	/**
	 * Display admin notices in the WP admin area.
	 */
	public function display_admin_notices(): void {
		// Sadece eklenti sayfalarında çalışsın
		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, 'hbt-tpt' ) === false ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'hbt_notifications';
		
		// Bildirimleri türlerine göre (Kur hatası, Zarar vb.) grupla ve toplam sayısını bul
		$grouped_notices = $wpdb->get_results( "
			SELECT notification_type, 
			       COUNT(id) as total_count, 
			       MAX(title) as latest_title, 
			       MAX(message) as latest_message 
			FROM {$table} 
			WHERE is_read = 0 
			GROUP BY notification_type
			ORDER BY MAX(created_at) DESC
		" );

		if ( empty( $grouped_notices ) ) {
			return;
		}

		echo '<style>
			.hbt-rotator-item { transition: opacity 0.3s ease-in-out; }
			.hbt-rotator-item p { margin: 0.5em 0; font-size: 14px; }
			.hbt-btn-dismiss-type { display: inline-block; margin-top: 5px; text-decoration: none; color: #b32d2e; font-weight: 600; }
			.hbt-btn-dismiss-type:hover { color: #d63638; text-decoration: underline; }
		</style>';
		echo '<div id="hbt-rotating-notices-container">';

		foreach ( $grouped_notices as $index => $group ) {
			$class = $this->get_notice_class( $group->notification_type );
			$display = $index === 0 ? 'block' : 'none';

			// Başlık ve Adet Sayısı
			$title = esc_html( $group->latest_title );
			if ( $group->total_count > 1 ) {
				$title .= ' (' . intval( $group->total_count ) . ' Adet)';
			}

			echo '<div class="notice ' . esc_attr( $class ) . ' hbt-admin-notice hbt-rotator-item" id="notice-grp-' . esc_attr( $group->notification_type ) . '" style="display: ' . $display . ';">';
			echo '<p><strong>' . $title . ':</strong> ' . esc_html( $group->latest_message ) . '</p>';
			
			// Eğer aynı tipten birden fazla varsa kullanıcıyı bilgilendir
			if ( $group->total_count > 1 ) {
				echo '<p style="font-size: 12px; color: #666;"><em>Bu kategoride ' . ( intval( $group->total_count ) - 1 ) . ' bildirim daha var. Detaylar için Bildirimler sayfasına bakabilirsiniz.</em></p>';
			}

			// Kapat Butonu (AJAX ile bu kategorideki her şeyi silecek)
			echo '<p><a href="#" class="hbt-btn-dismiss-type" data-type="' . esc_attr( $group->notification_type ) . '"><span class="dashicons dashicons-dismiss" style="font-size: 16px; line-height: 1.2;"></span> Bu Kategorideki Tüm Bildirimleri Okundu İşaretle</a></p>';
			echo '</div>';
		}

		echo '</div>';

		// JavaScript: Hem 3 saniyede bir döndürme işlemini hem de "Kapat" butonunun AJAX isteğini yönetir
		?>
		<script>
		document.addEventListener("DOMContentLoaded", function() {
			let container = document.getElementById('hbt-rotating-notices-container');
			if (!container) return;

			// 3 Saniyede Bir Döndürme (Rotator) Mantığı
			let rotateInterval = setInterval(function() {
				let items = container.querySelectorAll('.hbt-rotator-item');
				if (items.length <= 1) {
					if (items.length === 1) { 
						items[0].style.display = 'block'; 
						items[0].style.opacity = 1; 
					}
					return; 
				}
				
				let visibleIndex = -1;
				for (let i = 0; i < items.length; i++) {
					if (items[i].style.display !== 'none') {
						visibleIndex = i;
						break;
					}
				}
				
				if (visibleIndex === -1) {
					items[0].style.display = 'block';
					items[0].style.opacity = 1;
					return;
				}
				
				items[visibleIndex].style.display = 'none';
				let nextIndex = (visibleIndex + 1) % items.length;
				
				items[nextIndex].style.opacity = 0;
				items[nextIndex].style.display = 'block';
				setTimeout(() => { items[nextIndex].style.opacity = 1; }, 50);
				
			}, 3000);

			// AJAX ile Kategori Bazlı Bildirim Kapatma Mantığı
			let closeBtns = container.querySelectorAll('.hbt-btn-dismiss-type');
			closeBtns.forEach(function(btn) {
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					let type = this.getAttribute('data-type');
					let noticeDiv = document.getElementById('notice-grp-' + type);
					
					// Ekranda hemen gizle (kullanıcıyı bekletmemek için)
					if (noticeDiv) noticeDiv.remove();

					// Arka planda okundu yapmak üzere WordPress'e gönder
					jQuery.post(ajaxurl, {
						action: 'hbt_dismiss_notice_type',
						notice_type: type
					});
				});
			});
		});
		</script>
		<?php
	}
	
	/**
	 * Convert notification_type to WP notice class.
	 *
	 * @param string $type
	 * @return string
	 */
	private function get_notice_class( string $type ): string {
		switch ( $type ) {
			case 'sync_error':
			case 'critical_loss':
				return 'notice-error';
			case 'loss_alert':
				return 'notice-warning';
			case 'cost_missing':
			default:
				return 'notice-info';
		}
	}

	/**
	 * Create a new notification.
	 *
	 * @param string $type         Notification type.
	 * @param string $title        Notification title.
	 * @param string $message      Notification message.
	 * @param int    $reference_id Optional reference ID (order ID, item ID, etc.).
	 * @return bool
	 */
	public function create_notification( string $type, string $title, string $message, int $reference_id = 0 ): bool {
		// Hatalı 'add_notification' yerine doğru 'create_notification' metodu çağrıldı.
		$result = $this->db->create_notification( $type, $title, $message, $reference_id );
		return $result !== false;
	}
}