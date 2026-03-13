<?php
/**
 * Settings view.
 *
 * @package HBT_Trendyol_Profit_Tracker
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// --- ZORUNLU CRON ONARICI (Eksik Görevleri Anında Kurar) ---
if ( class_exists('HBT_Cron_Manager') ) {
    HBT_Cron_Manager::instance()->schedule_events();
}
// -----------------------------------------------------------

// --- MANUEL TEST VE TEŞHİS TETİKLEYİCİLERİ ---
if ( isset($_GET['hbt_test_add']) && class_exists('HBT_Cron_Manager') ) {
    HBT_Cron_Manager::instance()->sync_orders_fast();
    echo '<div class="notice notice-success is-dismissible"><p><strong>ADIM 1 BAŞARILI:</strong> Hızlı Döngü tetiklendi ve aktif mağazalar arka plan kuyruğuna (sıraya) eklendi.</p></div>';
}
if ( isset($_GET['hbt_test_run']) && class_exists('HBT_Cron_Manager') ) {
    HBT_Cron_Manager::instance()->process_background_queue();
    echo '<div class="notice notice-success is-dismissible"><p><strong>ADIM 2 BAŞARILI:</strong> Arka plan işçisi (worker) çalıştırıldı. Eğer sıradaki iş bittiyse log tablosuna yazılmış olmalı!</p></div>';
}
if ( isset($_GET['hbt_test_clear']) ) {
    delete_option('hbt_background_queue');
    echo '<div class="notice notice-warning is-dismissible"><p>Kuyruk tamamen temizlendi.</p></div>';
}
// -----------------------------------------------------------

// Orijinal veritabanı bağlantılarınız ve sınıflarınız (Korundu)
$settings = new HBT_Settings();
$values   = $settings->get_all();
$db_stats = HBT_Database::instance()->get_table_stats();

// Son 300 senkronizasyon logunu çekiyoruz
$sync_logs = array();
if ( method_exists( HBT_Database::instance(), 'get_sync_logs' ) ) {
    $sync_logs = HBT_Database::instance()->get_sync_logs( 300 );
}

// Kuyruk durumunu oku (Teşhis için)
$queue = get_option('hbt_background_queue', array());
$queue_count = is_array($queue) ? count($queue) : 0;
?>
<div class="wrap hbt-tpt-wrap">
    
    <div class="hbt-page-header">
        <h1 class="hbt-page-title">
            <span class="dashicons dashicons-admin-settings"></span> 
            <?php esc_html_e( 'Sistem Ayarları', 'hbt-trendyol-profit-tracker' ); ?>
        </h1>
    </div>

    <div class="hbt-alert-box hbt-alert-info" style="margin-bottom: 24px;">
        <span class="dashicons dashicons-info"></span> 
        <div><?php esc_html_e( 'Eklentinin genel hesaplama kurallarını, cron senkronizasyon sürelerini, bildirim tercihlerini ve sistem durumunu bu ekrandan yönetebilirsiniz.', 'hbt-trendyol-profit-tracker' ); ?></div>
    </div>

    <form id="settings-form">
        <div class="hbt-tabs" style="margin-bottom: 24px; border-bottom: 2px solid var(--hbt-border);">
            <button type="button" class="hbt-tab active" data-tab="general" style="margin-bottom: -2px;"><span class="dashicons dashicons-admin-generic" style="margin-top:2px;"></span> <?php esc_html_e( 'Genel', 'hbt-trendyol-profit-tracker' ); ?></button>
            <button type="button" class="hbt-tab" data-tab="sync" style="margin-bottom: -2px;"><span class="dashicons dashicons-update" style="margin-top:2px;"></span> <?php esc_html_e( 'Senkronizasyon & Loglar', 'hbt-trendyol-profit-tracker' ); ?></button>
            <button type="button" class="hbt-tab" data-tab="notifications" style="margin-bottom: -2px;"><span class="dashicons dashicons-bell" style="margin-top:2px;"></span> <?php esc_html_e( 'Bildirimler', 'hbt-trendyol-profit-tracker' ); ?></button>
            <button type="button" class="hbt-tab" data-tab="export" style="margin-bottom: -2px;"><span class="dashicons dashicons-download" style="margin-top:2px;"></span> <?php esc_html_e( 'Dışa Aktarma', 'hbt-trendyol-profit-tracker' ); ?></button>
            <button type="button" class="hbt-tab" data-tab="system" style="margin-bottom: -2px;"><span class="dashicons dashicons-shield" style="margin-top:2px;"></span> <?php esc_html_e( 'Sistem & Veritabanı', 'hbt-trendyol-profit-tracker' ); ?></button>
        </div>

        <div class="hbt-tab-content active" id="tab-general">
            <div class="hbt-card" style="max-width: 800px;">
                <h3 class="hbt-widget-title" style="margin-top: 0;"><span class="dashicons dashicons-money-alt"></span> <?php esc_html_e( 'Genel Hesaplama Ayarları', 'hbt-trendyol-profit-tracker' ); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="hbt-filter-group">
                        <label for="default_vat_rate" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Varsayılan KDV Oranı (%)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="default_vat_rate" name="default_vat_rate" min="0" max="100" class="regular-text" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['default_vat_rate'] ); ?>">
                    </div>
                    <div class="hbt-filter-group">
                        <label for="currency_cache_minutes" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Kur Cache Süresi (dakika)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="currency_cache_minutes" name="currency_cache_minutes" min="1" class="regular-text" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['currency_cache_minutes'] ); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="hbt-tab-content" id="tab-sync" style="display:none;">
            <div class="hbt-card" style="margin-bottom: 24px;">
                <h3 class="hbt-widget-title" style="margin-top: 0;"><span class="dashicons dashicons-update"></span> <?php esc_html_e( 'Cron Senkronizasyon Sıklıkları', 'hbt-trendyol-profit-tracker' ); ?></h3>
                <p style="color: var(--hbt-text-muted); font-size: 13px; margin-bottom: 20px;">Sistemin arka planda verileri ne kadar sürede bir otomatik çekeceğini (dakika cinsinden) belirleyin. (Not: Sipariş Çekme Sıklığı iptal edilip, yerine Hızlı ve Derin olmak üzere çifte kuyruk mimarisine geçilmiştir.)</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="hbt-filter-group" style="opacity: 0.6; pointer-events: none;">
                        <label for="cron_order_interval" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Eski Sipariş Çekme (Devre Dışı)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="cron_order_interval" name="cron_order_interval" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['cron_order_interval'] ); ?>">
                    </div>
                    <div class="hbt-filter-group">
                        <label for="cron_currency_interval" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Kur Güncelleme Sıklığı (dakika)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="cron_currency_interval" name="cron_currency_interval" min="5" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['cron_currency_interval'] ); ?>">
                    </div>
                    <div class="hbt-filter-group">
                        <label for="cron_financial_interval" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Finansal Veri Çekme Sıklığı (dakika)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="cron_financial_interval" name="cron_financial_interval" min="30" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['cron_financial_interval'] ); ?>">
                    </div>
                    <div class="hbt-filter-group">
                        <label for="cron_product_interval" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Ürün Senkron Sıklığı (dakika)', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <input type="number" id="cron_product_interval" name="cron_product_interval" min="60" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['cron_product_interval'] ); ?>">
                    </div>
                </div>

                <div style="margin-top: 24px; border: 1px solid #93C5FD; background: #EFF6FF; padding: 20px; border-radius: var(--hbt-radius);">
                    <h3 style="margin: 0 0 12px 0; color: #1E3A8A; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Tam Otomasyon İçin Sunucu Ayarı (Cron Job)', 'hbt-trendyol-profit-tracker' ); ?>
                    </h3>
                    <p style="color: #1E40AF; font-size: 13px; line-height: 1.6; margin-bottom: 15px;">
                        Arka plandaki akıllı kuyruk (queue) mimarisinin <strong>sitenize ziyaretçi girmese bile</strong> kesintisiz çalışabilmesi için hosting panelinizden gerçek bir Cron Job tanımlamanız gereklidir. Aşağıdaki adresi kullanarak <strong>"Her 5 Dakikada Bir (*/5 * * * *)"</strong> çalışacak bir görev ekleyin:
                    </p>
                    
                    <div style="background: #1E293B; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px;">
                        <code style="color: #38BDF8; font-family: monospace; font-size: 14px; background: transparent; padding: 0; word-break: break-all;">
                            <?php echo esc_url( site_url( 'wp-cron.php?doing_wp_cron' ) ); ?>
                        </code>
                    </div>
                    
                    <p style="color: #1E40AF; font-size: 13px; margin: 0;">
                        <strong>Plesk Kullanıcıları:</strong> Zamanlanmış Görevler (Cron jobs) menüsünden Görev Tipi olarak <strong>"Bir URL Getir (Fetch a URL)"</strong> seçeneğini seçip yukarıdaki linki yapıştırmalıdır.<br>
                        <strong>cPanel Kullanıcıları:</strong> Cron İşleri menüsünden komut alanına: <code>wget -q -O - <?php echo esc_url( site_url( 'wp-cron.php?doing_wp_cron' ) ); ?> >/dev/null 2>&1</code> yazmalıdır.
                    </p>
                </div>
            </div>

            <div class="hbt-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
                <h3 class="hbt-widget-title" style="margin: 0; padding: 15px 20px; background: #F8FAFC; border-bottom: 1px solid var(--hbt-border);">
                    <span class="dashicons dashicons-list-view" style="color: var(--hbt-secondary);"></span> <?php esc_html_e( 'Senkronizasyon Geçmişi (Loglar)', 'hbt-trendyol-profit-tracker' ); ?>
                </h3>
                <div style="padding: 20px;">
                    <table id="sync-logs-table" class="wp-list-table widefat fixed striped display" style="border: none; margin: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 15%;"><?php esc_html_e( 'Tarih', 'hbt-trendyol-profit-tracker' ); ?></th>
                                <th style="width: 15%;"><?php esc_html_e( 'Mağaza', 'hbt-trendyol-profit-tracker' ); ?></th>
                                <th style="width: 12%;"><?php esc_html_e( 'İşlem Tipi', 'hbt-trendyol-profit-tracker' ); ?></th>
                                <th style="width: 8%; text-align:center;" title="API'den Toplam Gelen Sipariş">Çekilen</th>
                                <th style="width: 8%; text-align:center;" title="Veritabanına İlk Kez Eklenen">Eklenen</th>
                                <th style="width: 8%; text-align:center;" title="Durumu/Fiyatı Güncellenen">Değişen</th>
                                <th style="width: 8%; text-align:center;" title="Değişiklik Olmadığı İçin Atlanan (Geçilen)">Atlanan</th>
                                <th style="width: 8%; text-align:center; color: var(--hbt-danger);" title="Hata Alanlar">Hata</th>
                                <th><?php esc_html_e( 'Durum / Mesaj', 'hbt-trendyol-profit-tracker' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( ! empty( $sync_logs ) ) : ?>
                                <?php foreach ( $sync_logs as $log ) : 
                                    // Rozet Renkleri
                                    $badge_bg = '#E2E8F0'; $badge_color = '#475569'; $type_label = $log->sync_type;
                                    if ($log->sync_type === 'auto_fast') { $badge_bg = '#DBEAFE'; $badge_color = '#1D4ED8'; $type_label = 'Otomatik (Hızlı)'; }
                                    elseif ($log->sync_type === 'auto_deep') { $badge_bg = '#EDE9FE'; $badge_color = '#6D28D9'; $type_label = 'Otomatik (Derin)'; }
                                    elseif ($log->sync_type === 'manual') { $badge_bg = '#FEF3C7'; $badge_color = '#B45309'; $type_label = 'Manuel (Buton)'; }

                                    $status_color = $log->status === 'success' ? 'var(--hbt-success)' : 'var(--hbt-danger)';
                                    $status_icon  = $log->status === 'success' ? 'yes' : 'warning';
                                ?>
                                    <tr>
                                        <td data-sort="<?php echo esc_attr( strtotime( $log->created_at ) ); ?>">
                                            <?php echo esc_html( wp_date( 'd.m.Y H:i', strtotime( $log->created_at ) ) ); ?>
                                        </td>
                                        <td style="font-weight: 500;"><?php echo esc_html( $log->store_name ); ?></td>
                                        <td>
                                            <span style="background: <?php echo $badge_bg; ?>; color: <?php echo $badge_color; ?>; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                                <?php echo esc_html( $type_label ); ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center; font-weight:600;"><?php echo (int)$log->fetched; ?></td>
                                        <td style="text-align:center; color: var(--hbt-success); font-weight:600;">+<?php echo (int)$log->inserted; ?></td>
                                        <td style="text-align:center; color: var(--hbt-primary); font-weight:600;"><?php echo (int)$log->updated; ?></td>
                                        <td style="text-align:center; color: #64748b; font-weight:600;"><?php echo (int)$log->skipped; ?></td>
                                        <td style="text-align:center; color: var(--hbt-danger); font-weight:600;"><?php echo (int)$log->failed; ?></td>
                                        <td style="font-size: 12px; color: <?php echo $status_color; ?>;">
                                            <span class="dashicons dashicons-<?php echo $status_icon; ?>" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                            <?php echo esc_html( $log->message ); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="hbt-tab-content" id="tab-notifications" style="display:none;">
            <div class="hbt-card" style="max-width: 800px;">
                <h3 class="hbt-widget-title" style="margin-top: 0;"><span class="dashicons dashicons-bell"></span> <?php esc_html_e( 'Bildirim Kuralları', 'hbt-trendyol-profit-tracker' ); ?></h3>
                
                <table class="form-table" style="margin-top: 20px;">
                    <tr>
                        <th style="padding: 15px 10px 15px 0;"><label style="font-weight: 600; color: var(--hbt-primary);"><?php esc_html_e( 'Zarar Eden Sipariş Uyarısı', 'hbt-trendyol-profit-tracker' ); ?></label></th>
                        <td style="padding: 15px 10px;">
                            <label class="hbt-toggle">
                                <input type="checkbox" id="notification_loss_alert" name="notification_loss_alert" <?php checked( (bool) $values['notification_loss_alert'] ); ?>>
                                <span class="hbt-toggle-slider"></span>
                            </label>
                            <p class="description" style="margin-top: 8px; font-size: 12px;">Sipariş kârı 0'ın veya kritik eşiğin altına düştüğünde bildirim üretilir.</p>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 15px 10px 15px 0;"><label style="font-weight: 600; color: var(--hbt-primary);"><?php esc_html_e( 'Eksik Maliyet Uyarısı', 'hbt-trendyol-profit-tracker' ); ?></label></th>
                        <td style="padding: 15px 10px;">
                            <label class="hbt-toggle">
                                <input type="checkbox" id="notification_cost_missing" name="notification_cost_missing" <?php checked( (bool) $values['notification_cost_missing'] ); ?>>
                                <span class="hbt-toggle-slider"></span>
                            </label>
                            <p class="description" style="margin-top: 8px; font-size: 12px;">Siparişteki bir ürünün maliyeti girilmemişse (0$ ise) sistem sizi uyarır.</p>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 15px 10px 15px 0;"><label for="critical_loss_threshold" style="font-weight: 600; color: var(--hbt-primary);"><?php esc_html_e( 'Kritik Zarar Eşiği (%)', 'hbt-trendyol-profit-tracker' ); ?></label></th>
                        <td style="padding: 15px 10px;">
                            <input type="number" id="critical_loss_threshold" name="critical_loss_threshold" step="0.1" style="width: 120px; padding: 8px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);" value="<?php echo esc_attr( (string) $values['critical_loss_threshold'] ); ?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="hbt-tab-content" id="tab-export" style="display:none;">
            <div class="hbt-card" style="max-width: 800px;">
                <h3 class="hbt-widget-title" style="margin-top: 0;"><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Dışa Aktarma (Export) Ayarları', 'hbt-trendyol-profit-tracker' ); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="hbt-filter-group">
                        <label for="export_default_format" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'Varsayılan Format', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <select id="export_default_format" name="export_default_format" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);">
                            <option value="csv" <?php selected( $values['export_default_format'], 'csv' ); ?>>CSV</option>
                            <option value="excel" <?php selected( $values['export_default_format'], 'excel' ); ?>>Excel</option>
                            <option value="pdf" <?php selected( $values['export_default_format'], 'pdf' ); ?>>PDF</option>
                        </select>
                    </div>
                    <div class="hbt-filter-group">
                        <label for="pdf_orientation" style="font-weight: 600; color: var(--hbt-primary); margin-bottom: 8px; display: block; font-size: 13px;"><?php esc_html_e( 'PDF Sayfa Yönü', 'hbt-trendyol-profit-tracker' ); ?></label>
                        <select id="pdf_orientation" name="pdf_orientation" style="width: 100%; padding: 10px 12px; border-radius: var(--hbt-radius-sm); border: 1px solid var(--hbt-border);">
                            <option value="landscape" <?php selected( $values['pdf_orientation'], 'landscape' ); ?>><?php esc_html_e( 'Yatay (Landscape)', 'hbt-trendyol-profit-tracker' ); ?></option>
                            <option value="portrait" <?php selected( $values['pdf_orientation'], 'portrait' ); ?>><?php esc_html_e( 'Dikey (Portrait)', 'hbt-trendyol-profit-tracker' ); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="hbt-tab-content" id="tab-system" style="display:none;">

            <div class="hbt-live-monitor hbt-card" style="margin-bottom: 24px; padding: 0; overflow: hidden; border: 1px solid #E2E8F0;">
                <div style="background: #0F172A; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: #fff; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-desktop" style="color: #38BDF8;"></span> 
                        <?php esc_html_e( 'Akıllı Senkronizasyon Monitörü (HBT Queue Motoru)', 'hbt-trendyol-profit-tracker' ); ?>
                    </h3>
                  <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" class="button button-secondary" id="btn-monitor-toggle" style="background:transparent; border:1px solid #38BDF8; color:#38BDF8; cursor:pointer;">Monitörü Başlat</button>
                        <button type="button" class="button" id="btn-monitor-add-jobs" style="background:#F59E0B; border:none; color:#fff;">Cron'u Ateşle (İş Yükle)</button>
                        <button type="button" class="button button-primary" id="btn-monitor-run">İşçiyi Çalıştır (Erit)</button>
                    </div>
                </div>
                
                <div style="padding: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <div style="background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Bekleyen Görev</div>
                        <div id="mon-pending" style="font-size: 28px; font-weight: 700; color: #0F172A; margin-top: 5px;">0</div>
                    </div>
                    <div style="background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">İşlenen (Şu An)</div>
                        <div id="mon-processing" style="font-size: 28px; font-weight: 700; color: #3B82F6; margin-top: 5px;">0</div>
                    </div>
                    <div style="background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Başarısız (Hata)</div>
                        <div id="mon-failed" style="font-size: 28px; font-weight: 700; color: #EF4444; margin-top: 5px;">0</div>
                    </div>
                    <div style="background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 12px; color: #64748B; font-weight: 600; text-transform: uppercase;">Toplam İş Yükü</div>
                        <div id="mon-total" style="font-size: 28px; font-weight: 700; color: #10B981; margin-top: 5px;">0</div>
                    </div>
                </div>

                <div style="padding: 15px 20px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #475569; margin-bottom: 8px;">
                        <strong>İşlem Durumu:</strong>
                        <span id="mon-eta">Tahmini Süre: Hesaplanıyor...</span>
                    </div>
                    <div style="width: 100%; background: #E2E8F0; border-radius: 4px; height: 8px; overflow: hidden;">
                        <div id="mon-progress-bar" style="width: 0%; height: 100%; background: #10B981; transition: width 0.5s ease;"></div>
                    </div>
                    <p style="font-size: 12px; color: #94A3B8; margin: 10px 0 0 0;">(Bu monitör, sunucuyu yormamak adına yalnızca bu sekme açıkken her 3 saniyede bir veritabanını yoklar.)</p>
                    <div style="margin-top: 15px; border: 1px solid #334155; border-radius: 6px; overflow: hidden;">
                        <div style="background: #1E293B; padding: 8px 15px; font-size: 11px; color: #94A3B8; font-weight: bold; border-bottom: 1px solid #334155; display:flex; justify-content:space-between;">
                            <span>>_ SİSTEM TERMİNALİ (CANLI AKIŞ)</span>
                            <span style="color:#10B981;">● Online</span>
                        </div>
                        <div id="mon-live-logs" style="background: #0F172A; color: #38BDF8; font-family: 'Courier New', Courier, monospace; font-size: 12px; padding: 15px; height: 140px; overflow-y: auto; line-height: 1.6;">
                            [Sistem] Monitör bağlantısı bekleniyor...
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
                
                <div>
                    <div class="hbt-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
                        <h3 class="hbt-widget-title" style="margin: 0; padding: 15px 20px; background: #F8FAFC; border-bottom: 1px solid var(--hbt-border);">
                            <span class="dashicons dashicons-database" style="color: var(--hbt-secondary);"></span> <?php esc_html_e( 'Veritabanı İstatistikleri', 'hbt-trendyol-profit-tracker' ); ?>
                        </h3>
                        <table class="wp-list-table widefat fixed striped" style="margin: 0; border: none;">
                            <thead>
                                <tr>
                                    <th style="padding-left: 20px;"><?php esc_html_e( 'Tablo Adı', 'hbt-trendyol-profit-tracker' ); ?></th>
                                    <th style="padding-right: 20px;"><?php esc_html_e( 'Kayıt Sayısı', 'hbt-trendyol-profit-tracker' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $db_stats as $table => $count ) : ?>
                                    <tr>
                                        <td style="padding-left: 20px; font-weight: 500;"><?php echo esc_html( $table ); ?></td>
                                        <td style="padding-right: 20px; font-family: monospace; font-size: 14px;"><?php echo esc_html( (string) $count ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="hbt-card" style="padding: 0; overflow: hidden;">
                        <h3 class="hbt-widget-title" style="margin: 0; padding: 15px 20px; background: #F8FAFC; border-bottom: 1px solid var(--hbt-border);">
                            <span class="dashicons dashicons-clock" style="color: var(--hbt-primary);"></span> <?php esc_html_e( 'Cron (Zamanlanmış Görev) Durumu', 'hbt-trendyol-profit-tracker' ); ?>
                        </h3>
                        <table class="wp-list-table widefat fixed striped" style="margin: 0; border: none;">
                            <thead>
                                <tr>
                                    <th style="padding-left: 20px;"><?php esc_html_e( 'Hook Adı', 'hbt-trendyol-profit-tracker' ); ?></th>
                                    <th style="padding-right: 20px;"><?php esc_html_e( 'Sonraki Çalışma', 'hbt-trendyol-profit-tracker' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cron_hooks = array( 'hbt_sync_orders_fast', 'hbt_sync_orders_deep', 'hbt_process_background_queue', 'hbt_sync_currency', 'hbt_sync_financials', 'hbt_run_calculations', 'hbt_check_returns', 'hbt_sync_products' );
                                foreach ( $cron_hooks as $hook ) :
                                    $ts = wp_next_scheduled( $hook );
                                ?>
                                    <tr>
                                        <td style="padding-left: 20px; font-family: monospace; font-size: 12px;"><?php echo esc_html( $hook ); ?></td>
                                        <td style="padding-right: 20px; font-size: 13px; color: <?php echo $ts ? 'var(--hbt-success)' : 'var(--hbt-text-muted)'; ?>;">
                                            <?php echo $ts ? esc_html( gmdate( 'Y-m-d H:i:s', $ts + (get_option('gmt_offset') * HOUR_IN_SECONDS) ) ) : esc_html__( 'Zamanlanmamış', 'hbt-trendyol-profit-tracker' ); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <div class="hbt-card" style="border: 1px solid #FECACA; background: #FEF2F2;">
                        <h3 style="margin: 0 0 12px 0; color: #DC2626; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                            <span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'Sistem Onarım Araçları', 'hbt-trendyol-profit-tracker' ); ?>
                        </h3>
                        <p style="color: #991B1B; font-size: 13px; line-height: 1.5; margin-bottom: 20px;">
                            Maliyet veya komisyonlarda geçmişe dönük büyük bir değişiklik yaptığınızda (örn: ürün maliyetini 0'dan 10$'a çıkarmak), tüm kârlılık verilerinin güncellenmesi için siparişlerin yeniden hesaplanması gerekir. <strong>Bu işlem sipariş yoğunluğuna göre birkaç dakika sürebilir.</strong>
                        </p>
                        
                        <p style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0;">
                            <button type="button" id="btn-recalculate-all" class="hbt-btn hbt-btn-outline" style="color: #DC2626 !important; border-color: #FECACA !important; background: #fff !important;">
                                <span class="dashicons dashicons-update-alt"></span> <?php esc_html_e( 'Tüm Siparişleri Yeniden Hesapla', 'hbt-trendyol-profit-tracker' ); ?>
                            </button>
                            <span id="recalculate-progress" style="display:none; font-weight: 600; color: #DC2626; font-size: 13px;">
                                <span class="dashicons dashicons-update hbt-spinner"></span> <?php esc_html_e( 'İşleniyor...', 'hbt-trendyol-profit-tracker' ); ?>
                            </span>
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <p class="submit" style="margin-top: 24px; padding: 20px 24px; background: #fff; border: 1px solid var(--hbt-border); border-radius: var(--hbt-radius); display: flex; justify-content: flex-end; align-items: center; box-shadow: var(--hbt-shadow); margin-bottom: 0;">
            <span id="settings-result" style="margin-right: 16px; font-weight: 600; font-size: 14px;"></span>
            <button type="button" id="btn-save-settings" class="button button-primary">
                <span class="dashicons dashicons-saved" style="margin-top: 3px;"></span>
                <?php esc_html_e( 'Ayarları Kaydet', 'hbt-trendyol-profit-tracker' ); ?>
            </button>
        </p>

    </form>
</div>

<style>
/* Input Focus Efektleri */
#settings-form input[type="number"]:focus, 
#settings-form select:focus {
    border-color: var(--hbt-secondary) !important;
    box-shadow: 0 0 0 1px var(--hbt-secondary) !important;
    outline: none;
}

/* Modern Toggle (Switch) Yapısı */
.hbt-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}
.hbt-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.hbt-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #CBD5E1;
    transition: .3s;
    border-radius: 24px;
}
.hbt-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.hbt-toggle input:checked + .hbt-toggle-slider {
    background-color: var(--hbt-primary);
}
.hbt-toggle input:focus + .hbt-toggle-slider {
    box-shadow: 0 0 1px var(--hbt-primary);
}
.hbt-toggle input:checked + .hbt-toggle-slider:before {
    transform: translateX(20px);
}

/* DataTables Özelleştirmeleri (Log Tablosu) */
.dataTables_wrapper .dataTables_filter { margin-bottom: 15px; margin-top: 5px; }
.dataTables_wrapper .dataTables_length { margin-bottom: 15px; margin-top: 5px; }
.dataTables_wrapper .dataTables_paginate { margin-top: 15px; }
.dataTables_wrapper .dataTables_info { margin-top: 20px; }
/* Monitör Işığı Animasyonu */
@keyframes hbt-pulse-anim {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}
.hbt-pulse-light { display: inline-block; width: 10px; height: 10px; background: #22C55E; border-radius: 50%; animation: hbt-pulse-anim 2s infinite; }
table.dataTable.no-footer { border-bottom: 1px solid var(--hbt-border); }
</style>

<script>
jQuery(document).ready(function($) {
    if ($('#sync-logs-table').length) {
        $('#sync-logs-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json' },
            pageLength: 10,
            order: [[ 0, 'desc' ]],
            stateSave: true
        });
    }

    let lastLogId = 0;
    let currentActiveJobId = null; // Hangi görevin başladığını takip etmek için
    
    // ÜST EKRAN: Sistem Görevleri (En yenisi en üste eklenecek)
    function appendToTerminal(text, color = '#4ADE80') {
        const terminal = $('#hbt-terminal-output');
        if (terminal.length === 0) return;
        const time = new Date().toLocaleTimeString('tr-TR');
        terminal.prepend('<div style="color: '+color+'; margin-bottom:4px; padding-bottom:4px; border-bottom:1px solid rgba(255,255,255,0.05);">> ['+time+'] '+text+'</div>');
    }

    function updateMonitor() {
        if ($('#hbt-monitor-idle').length === 0) return;

        $.post(ajaxurl, { action: 'hbt_monitor_status' }, function(res) {
            if (res.success) {
                const data = res.data;
                $('#mon-queue-count').text(data.queue_count);

                // AKTİF İŞLEM TAKİBİ VE "BAŞLADI" LOGU (KIRMIZI/TURUNCU)
                if (data.active_job) {
                    $('#hbt-monitor-idle').hide();
                    $('#hbt-monitor-active').show();
                    
                    let sName = data.active_job.store_name || 'Bilinmeyen Mağaza';
                    let typeLabel = data.active_job.sync_type === 'auto_fast' ? 'Hızlı Tarama' : (data.active_job.sync_type === 'auto_deep' ? 'Derin Tarama' : 'Manuel');
                    
                    $('#mon-store-name').text(sName);
                    $('#mon-sync-type').text(typeLabel);
                    $('#mon-fetched').text(data.active_job.fetched || 0);
                    $('#mon-inserted').text(data.active_job.inserted || 0);
                    $('#mon-updated').text(data.active_job.updated || 0);
                    $('#mon-skipped').text(data.active_job.skipped || 0);
                    $('#mon-page').text( (parseInt(data.active_job.page) + 1) || 1 );

                    // Eğer bu ID'yi ilk defa görüyorsak, terminale "Başladı" yazdır
                    if (currentActiveJobId !== data.active_job.id) {
                        currentActiveJobId = data.active_job.id;
                        appendToTerminal(`BAŞLADI: ${sName} mağazası için [${typeLabel}] işlemi veritabanına bağlandı...`, '#F87171'); // Kırmızı/Turuncu
                    }

                } else {
                    $('#hbt-monitor-active').hide();
                    $('#hbt-monitor-idle').show();
                    currentActiveJobId = null; // İş bittiğinde ID'yi sıfırla
                }

                // ÜST EKRAN BİTİŞ LOGLARI (BİTTİ: YEŞİL)
                if (data.recent_logs && data.recent_logs.length > 0) {
                    let logs = [...data.recent_logs].reverse();
                    if (lastLogId === 0) {
                        lastLogId = Math.max(...data.recent_logs.map(l => parseInt(l.id)));
                        logs.forEach(function(log) {
                            let color = log.status === 'success' ? '#22C55E' : '#EF4444'; // Başarılıysa Yeşil
                            appendToTerminal(`BİTTİ: ${log.store_name} -> ${log.message}`, color);
                        });
                    } else {
                        logs.forEach(function(log) {
                            if (parseInt(log.id) > lastLogId) {
                                lastLogId = parseInt(log.id);
                                let color = log.status === 'success' ? '#22C55E' : '#EF4444'; // Başarılıysa Yeşil
                                appendToTerminal(`BİTTİ: ${log.store_name} -> ${log.message}`, color);
                            }
                        });
                    }
                }
                
                // ALT EKRAN: CANLI SİPARİŞ AKIŞI
                if (typeof window.processedStreams === 'undefined') window.processedStreams = [];
                if (data.stream_logs && data.stream_logs.length > 0) {
                    data.stream_logs.forEach(function(slog) {
                        if (!window.processedStreams.includes(slog.id)) {
                            window.processedStreams.push(slog.id);
                            let color = slog.type === 'insert' ? '#22C55E' : '#3B82F6';
                            let icon  = slog.type === 'insert' ? '[+]' : '[~]';
                            const st  = $('#hbt-stream-output');
                            
                            st.prepend('<div style="color: '+color+'; margin-bottom:4px; padding-bottom:4px; border-bottom:1px solid rgba(255,255,255,0.05); font-weight:600;">> '+slog.time+' '+icon+' '+slog.msg+'</div>');
                        }
                    });
                    if (window.processedStreams.length > 150) window.processedStreams.splice(0, 50);
                }
            }
        });
    }

    setInterval(updateMonitor, 3000);
    updateMonitor();

    $(document).on('click', '#btn-monitor-add', function(e) {
        e.preventDefault();
        appendToTerminal("SİSTEM: Manuel hızlı tarama komutu gönderildi...", "#FDE047"); // Sarı
        $.post(ajaxurl, { action: 'hbt_monitor_trigger', cmd: 'add_fast' }, function(res) {
            updateMonitor();
        });
    });

    $(document).on('click', '#btn-monitor-run', function(e) {
        e.preventDefault();
        appendToTerminal("SİSTEM: İşçi manuel olarak tetiklendi...", "#FDE047"); // Sarı
        $.post(ajaxurl, { action: 'hbt_monitor_trigger', cmd: 'run_worker' }, function(res) {
            updateMonitor();
        });
    });

    $(document).on('click', '#btn-monitor-clear', function(e) {
        e.preventDefault();
        if(confirm("Tüm kuyruğu temizlemek istediğinize emin misiniz?")) {
            appendToTerminal("SİSTEM: Kuyruk temizleme işlemi yapıldı.", "#EF4444"); // Kırmızı
            $.post(ajaxurl, { action: 'hbt_monitor_trigger', cmd: 'clear_queue' }, function(res) {
                $('#hbt-stream-output').html('<div style="color: #64748B;">> Ekran temizlendi, yeni işlem bekleniyor...</div>');
                currentActiveJobId = null;
                updateMonitor();
            });
        }
    });
});
</script>