<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap hbt-wrap">
    
    <div class="hbt-page-header">
        <h1 class="hbt-page-title">
            <span class="dashicons dashicons-chart-area"></span> 
            <?php esc_html_e( 'SaaS Finansal Dashboard', 'hbt-trendyol-profit-tracker' ); ?>
        </h1>
        <div class="hbt-header-actions">
            <a href="?page=hbt-tpt-stores" class="hbt-btn hbt-btn-outline">
                <span class="dashicons dashicons-update"></span> Trendyol'dan Çek
            </a>
            <a href="?page=hbt-tpt-ad-expenses" class="hbt-btn hbt-btn-primary">
                <span class="dashicons dashicons-money-alt"></span> Reklam Gideri Gir
            </a>
        </div>
    </div>

    <div class="hbt-goal-container" id="hbt-goal-container" title="Aylık Kâr Hedefinizi değiştirmek için buraya tıklayın">
        <div class="hbt-goal-info">
            <h3>Aylık Kâr Hedefi <span class="dashicons dashicons-edit"></span></h3>
            <p id="hbt-goal-status-text">Yükleniyor...</p>
        </div>
        <div class="hbt-goal-bar-wrap">
            <div class="hbt-goal-bar-fill" id="hbt-goal-bar"></div>
            <div class="hbt-goal-text" id="hbt-goal-text">%0</div>
        </div>
    </div>

    <h3 class="hbt-widget-title">
        <span class="dashicons dashicons-money-alt"></span> Net Kâr Özeti (Tüm Giderler Düşülmüş)
    </h3>
    <div class="hbt-kpi-grid">
        <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-calendar-alt"></span> Bugün Net Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-today">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-controls-skipback"></span> Dün Net Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-yesterday">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-calendar"></span> Bu Hft. Net Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-week">0.00 ₺</span>
        </div>
       <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-undo"></span> Geçen Hft. Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-lastweek">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-portfolio"></span> Bu Ay Net Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-month">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-profit">
            <span class="hbt-card-label"><span class="dashicons dashicons-archive"></span> Geçen Ay Kâr</span>
            <span class="hbt-card-value" id="hbt-profit-lastmonth">0.00 ₺</span>
        </div>
    </div>

    <h3 class="hbt-widget-title">
        <span class="dashicons dashicons-chart-bar"></span> Brüt Ciro Özeti
    </h3>
    <div class="hbt-kpi-grid">
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-cart"></span> Bugün Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-today">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-store"></span> Dün Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-yesterday">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-chart-pie"></span> Bu Hafta Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-week">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-image-rotate-left"></span> Geçen Hft. Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-lastweek">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-chart-line"></span> Bu Ay Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-month">0.00 ₺</span>
        </div>
        <div class="hbt-card hbt-card-compact hbt-card-revenue">
            <span class="hbt-card-label"><span class="dashicons dashicons-analytics"></span> Geçen Ay Ciro</span>
            <span class="hbt-card-value" id="hbt-rev-lastmonth">0.00 ₺</span>
        </div>
    </div>

    <div class="hbt-dashboard-row">
        <div class="hbt-col-8">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-chart-area"></span> Son 30 Günlük Kâr ve Marj Trendi</h3>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="chart-trend"></canvas>
            </div>
        </div>
        <div class="hbt-col-4">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-chart-pie"></span> Son 30 Gün Gider Dağılımı</h3>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="chart-expenses"></canvas>
            </div>
        </div>
    </div>

    <div class="hbt-dashboard-row">
        <div class="hbt-col-4">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-awards"></span> Son 30 Günün Yıldız Ürünleri</h3>
            <ul class="hbt-top-products-list" id="hbt-top-products">
                <li>Yükleniyor...</li>
            </ul>
        </div>
        <div class="hbt-col-8">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-bell"></span> Akıllı Uyarılar ve Aksiyonlar</h3>
            <div id="hbt-smart-alerts">
                <p style="color:var(--hbt-text-muted);">Yükleniyor...</p>
            </div>
        </div>
    </div>

    <div class="hbt-dashboard-row">
        <div class="hbt-col-8" style="flex: 100%;">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-store"></span> Mağaza Bazlı Net Kâr Özeti</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Mağaza Adı</th>
                        <th>Bugünkü Net Kâr</th>
                        <th>Dünkü Net Kâr</th>
                        <th>Son 30 Günlük Net Kâr</th>
                    </tr>
                </thead>
                <tbody id="hbt-store-breakdown-body">
                    <tr><td colspan="4" style="text-align:center;">Yükleniyor...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="hbt-dashboard-row">
        <div class="hbt-col-8" style="flex: 100%;">
            <h3 class="hbt-widget-title"><span class="dashicons dashicons-cart"></span> Mağaza Bazlı Ciro Özeti</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Mağaza Adı</th>
                        <th>Bugünkü Ciro</th>
                        <th>Dünkü Ciro</th>
                        <th>Son 30 Günlük Ciro</th>
                    </tr>
                </thead>
                <tbody id="hbt-store-revenue-body">
                    <tr><td colspan="4" style="text-align:center;">Yükleniyor...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>



<script>
jQuery(document).ready(function($) {
    
    // Hedef Kârı Değiştirme
    $('#hbt-goal-container').on('click', function() {
        var currentGoal = $(this).attr('data-goal') || 50000;
        var newGoal = prompt("Yeni Aylık Net Kâr Hedefinizi (TL) girin:", currentGoal);
        if (newGoal !== null && !isNaN(newGoal) && newGoal > 0) {
            $.post(hbtTpt.ajaxurl, {
                action: 'hbt_save_profit_goal',
                nonce: hbtTpt.nonce,
                goal: newGoal
            }, function(res) {
                if(res.success) location.reload();
            });
        }
    });

    // Yeni SaaS Renklerine Uygun Money Format
    var formatMoney = function(val, forceColor) {
        var str = parseFloat(val).toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ₺';
        if (forceColor) {
            var color = val >= 0 ? '#10B981' : '#EF4444'; // Yeni Emerald ve Red
            return '<span style="color:'+color+'; font-weight:800;">' + str + '</span>';
        }
        return str;
    };

  // --- BURADAN İTİBAREN DEĞİŞTİRİN ---

    // Grafik nesnelerini hafızada tutmak için değişkenler (Yenilemede üst üste binmemesi için gerekli)
    var trendChart = null;
    var expensesChart = null;

    // Ana Verileri Çeken Fonksiyon
    function fetchDashboardData() {
        // Yenilendiğini belli etmek için ufak bir soluklaşma efekti
        $('#hbt-goal-status-text').css('opacity', '0.5');

        $.post(hbtTpt.ajaxurl, { action: 'hbt_get_dashboard_data', nonce: hbtTpt.nonce }, function(res) {
            if (!res.success) return;
            var d = res.data;

            // KPI Ciro Kartları 
            $('#hbt-rev-today').text(formatMoney(d.revenue_today));
            $('#hbt-rev-yesterday').text(formatMoney(d.revenue_yesterday));
            $('#hbt-rev-week').text(formatMoney(d.revenue_week));
            $('#hbt-rev-lastweek').text(formatMoney(d.revenue_last_week));
            $('#hbt-rev-month').text(formatMoney(d.revenue_month));
            $('#hbt-rev-lastmonth').text(formatMoney(d.revenue_last_month));

            // KPI Net Kâr Kartları
            $('#hbt-profit-today').html(formatMoney(d.profit_today, true));
            $('#hbt-profit-yesterday').html(formatMoney(d.profit_yesterday, true));
            $('#hbt-profit-week').html(formatMoney(d.profit_week, true));
            $('#hbt-profit-lastweek').html(formatMoney(d.profit_last_week, true));
            $('#hbt-profit-month').html(formatMoney(d.profit_month, true));
            $('#hbt-profit-lastmonth').html(formatMoney(d.profit_last_month, true));

            // Hedef Barını Doldur
            var goal = parseFloat(d.profit_goal);
            $('#hbt-goal-container').attr('data-goal', goal);
            var profitMonth = parseFloat(d.profit_month);
            var percent = goal > 0 ? (profitMonth / goal) * 100 : 0;
            if(percent < 0) percent = 0;
            var displayPercent = percent > 100 ? 100 : percent;
            
            $('#hbt-goal-bar').css('width', displayPercent + '%');
            $('#hbt-goal-text').text('%' + percent.toFixed(1) + ' Tamamlandı');
            $('#hbt-goal-status-text').text(profitMonth.toLocaleString('tr-TR', {minimumFractionDigits:0}) + ' ₺ / Hedef: ' + goal.toLocaleString('tr-TR', {minimumFractionDigits:0}) + ' ₺');

            // Akıllı Uyarılar
            var alertsHtml = '';
            if(d.smart_alerts && d.smart_alerts.length > 0) {
                $.each(d.smart_alerts, function(i, alert) {
                    alertsHtml += '<div class="hbt-alert-box hbt-alert-' + alert.type + '"><span class="dashicons ' + alert.icon + '"></span> <div>' + alert.msg + '</div></div>';
                });
            } else {
                alertsHtml = '<p style="color:#64748B; font-size:13px; margin:0;">Şu an için uyarı bulunmuyor, her şey yolunda! 🎉</p>';
            }
            $('#hbt-smart-alerts').html(alertsHtml);

            // En İyi Ürünler
            var productsHtml = '';
            if (d.top_products && d.top_products.length > 0) {
                $.each(d.top_products, function(i, p) {
                    productsHtml += '<li><div class="hbt-tp-name" title="'+p.product_name+'">' + p.product_name + ' <span style="font-weight:normal; color:#64748B; font-size:11px;">('+p.total_qty+' Adet)</span></div> <div class="hbt-tp-profit">+' + parseFloat(p.total_profit).toLocaleString('tr-TR') + ' ₺</div></li>';
                });
            } else {
                productsHtml = '<li><p style="color:#64748B; font-size:13px;">Yeterli satış verisi yok.</p></li>';
            }
            $('#hbt-top-products').html(productsHtml);

            // Mağaza Bazlı Rapor ve Ciro Tablosu
            var storesMap = {};
            function initStore(id, name) { 
                if(!storesMap[id]) storesMap[id] = { 
                    name: name, 
                    today: 0, yesterday: 0, l30: 0,
                    rev_today: 0, rev_yesterday: 0, rev_l30: 0
                }; 
            }
            
            if(d.store_comparison) {
                $.each(d.store_comparison, function(i, st) { 
                    initStore(st.id, st.store_name); 
                    storesMap[st.id].l30 = parseFloat(st.profit || 0); 
                    storesMap[st.id].rev_l30 = parseFloat(st.revenue || 0); 
                });
            }
            if(d.stores_today) {
                $.each(d.stores_today, function(i, st) { 
                    initStore(st.id, st.store_name); 
                    storesMap[st.id].today = parseFloat(st.profit || 0); 
                    storesMap[st.id].rev_today = parseFloat(st.revenue || 0); 
                });
            }
            if(d.stores_yesterday) {
                $.each(d.stores_yesterday, function(i, st) { 
                    initStore(st.id, st.store_name); 
                    storesMap[st.id].yesterday = parseFloat(st.profit || 0); 
                    storesMap[st.id].rev_yesterday = parseFloat(st.revenue || 0); 
                });
            }

            var tbodyProfit = '';
            var tbodyRevenue = '';

            $.each(storesMap, function(id, st) {
                tbodyProfit += '<tr>';
                tbodyProfit += '<td><strong>' + st.name + '</strong></td>';
                tbodyProfit += '<td>' + formatMoney(st.today, true) + '</td>';
                tbodyProfit += '<td>' + formatMoney(st.yesterday, true) + '</td>';
                tbodyProfit += '<td>' + formatMoney(st.l30, true) + '</td>';
                tbodyProfit += '</tr>';

                var percentChangeStr = '';
                if (st.rev_yesterday > 0) {
                    var change = ((st.rev_today - st.rev_yesterday) / st.rev_yesterday) * 100;
                    var color = change >= 0 ? '#10B981' : '#EF4444'; 
                    var sign = change > 0 ? '+' : '';
                    percentChangeStr = ' <span style="color:'+color+'; font-size:12px; font-weight:bold; margin-left:6px;">(' + sign + change.toFixed(1) + '%)</span>';
                } else if (st.rev_today > 0 && st.rev_yesterday === 0) {
                    percentChangeStr = ' <span style="color:#10B981; font-size:12px; font-weight:bold; margin-left:6px;">(+100%)</span>';
                }

                tbodyRevenue += '<tr>';
                tbodyRevenue += '<td><strong>' + st.name + '</strong></td>';
                tbodyRevenue += '<td>' + formatMoney(st.rev_today, true) + percentChangeStr + '</td>';
                tbodyRevenue += '<td>' + formatMoney(st.rev_yesterday, true) + '</td>';
                tbodyRevenue += '<td>' + formatMoney(st.rev_l30, true) + '</td>';
                tbodyRevenue += '</tr>';
            });

            if(tbodyProfit === '') tbodyProfit = '<tr><td colspan="4" style="text-align:center; color:#64748B;">Kayıtlı mağaza veya veri bulunamadı.</td></tr>';
            $('#hbt-store-breakdown-body').html(tbodyProfit);

            if(tbodyRevenue === '') tbodyRevenue = '<tr><td colspan="4" style="text-align:center; color:#64748B;">Kayıtlı mağaza veya veri bulunamadı.</td></tr>';
            $('#hbt-store-revenue-body').html(tbodyRevenue);

            // ÇİFT EKSENLİ TREND GRAFİĞİ
            if (document.getElementById('chart-trend')) {
                if (trendChart !== null) trendChart.destroy(); // Eski grafiği sil

                var labels = [], profitData = [], marginData = [];
                if(d.trend) {
                    $.each(d.trend, function(i, item) {
                        labels.push(item.day.slice(5)); 
                        profitData.push(item.profit);
                        marginData.push(item.margin);
                    });
                }
                trendChart = new Chart(document.getElementById('chart-trend'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Net Kâr (TL)', data: profitData, borderColor: '#2563EB', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.4, yAxisID: 'y', pointRadius: 3 },
                            { label: 'Kâr Marjı (%)', data: marginData, borderColor: '#F59E0B', backgroundColor: 'transparent', borderDash: [5, 5], tension: 0.4, yAxisID: 'y1', pointRadius: 3 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { labels: { font: { family: "'Inter', sans-serif" } } } },
                        scales: {
                            y: { type: 'linear', display: true, position: 'left', title: {display:true, text:'Net Kâr (TL)'}, grid: {color: '#E2E8F0'} },
                            y1: { type: 'linear', display: true, position: 'right', grid: {drawOnChartArea: false}, title: {display:true, text:'Marj (%)'} }
                        }
                    }
                });
            }

            // GİDER DAĞILIMI PASTA GRAFİĞİ
            if (document.getElementById('chart-expenses')) {
                if (expensesChart !== null) expensesChart.destroy(); // Eski grafiği sil

                var exp = d.expense_breakdown;
                if(exp) {
                    var realProfit = Math.max(0, exp.total_sales - exp.total_cost - exp.total_comm - exp.total_ship - exp.total_ads);
                    expensesChart = new Chart(document.getElementById('chart-expenses'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Ürün Maliyeti', 'Komisyon', 'Kargo', 'Reklam/Sabit', 'Net Kâr'],
                            datasets: [{
                                data: [exp.total_cost, exp.total_comm, exp.total_ship, exp.total_ads, realProfit],
                                backgroundColor: ['#F59E0B', '#F97316', '#EF4444', '#64748B', '#10B981'], 
                                borderWidth: 2, borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, cutout: '70%',
                            plugins: { legend: { position: 'right', labels: { padding: 20, boxWidth: 12, font: {size: 12, family: "'Inter', sans-serif"} } } }
                        }
                    });
                }
            }

            // İşlem bittiğinde opaklığı düzelt
            $('#hbt-goal-status-text').css('opacity', '1');
        });
    }

    // 1. Sayfa açıldığında verileri hemen çek
    fetchDashboardData();

    // 2. Her 60 saniyede bir (60000 ms) arka planda sessizce yenile
    setInterval(fetchDashboardData, 60000);

});
</script>