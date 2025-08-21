<?php
/**
 * Ticket scanner page.
 *
 * @since 0.0.9
 */
?>
<div class="wrap container-fluid">
    <section class="tk-wrap">
        <style>
            :root{
                --bg:#0b0d12;
                --card:#121723;
                --muted:#8892a6;
                --text:#e7ecf3;
                --primary:#4f8cff;
                --primary-press:#2f6eea;
                --ring:rgba(79,140,255,.3);
                --border:#283042;
                --ok:#2ecc71;
                --warn:#ffb020;
                --err:#ff6b6b;
                --radius:16px;
                --gap:16px;
            }
            .tk-wrap{font:14px/1.4 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;color:var(--text);background:var(--bg);padding:24px;border-radius:var(--radius);box-shadow:0 0 0 1px #0f1320,0 10px 30px rgba(0,0,0,.35);}            
            .tk-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;}
            .tk-title{font-size:18px;font-weight:700;letter-spacing:.2px;}
            .tk-sub{color:var(--muted);font-size:13px}
            .tk-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;}
            .tk-btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:10px 14px;border:1px solid var(--border);background:#0f1526;color:var(--text);cursor:pointer;transition:.15s transform,.15s background,.15s border-color;text-decoration:none;font-weight:600;}
            .tk-btn:hover{transform:translateY(-1px);border-color:#34405a;}
            .tk-btn:active{transform:translateY(0);}
            .tk-btn.primary{background:var(--primary);border-color:var(--primary);color:white;}
            .tk-btn.primary:hover{background:var(--primary-press);}
            .tk-bad{color:var(--err);}
            .tk-scanner{text-align:center;}
            .tk-scanner #qr-reader{width:100%;max-width:400px;margin:0 auto;}
            .tk-scanner #scan-result{margin-top:16px;text-align:left;}
            .tk-scanner #rescan-btn{display:none;margin-top:16px;}
            .tk-result .tk-btn{margin-top:16px;}
            #wpadminbar,#adminmenumain,#adminmenuback,#adminmenuwrap{display:none;}
            .notice{display:none;}
            #wpcontent,#wpfooter{margin-left:0;}
            #takamoa-scanner-home{position:fixed;top:20px;left:20px;z-index:999;}
        </style>
        <a href="<?= esc_url(admin_url()); ?>" class="tk-btn" id="takamoa-scanner-home">Home</a>
        <header class="tk-header">
            <div>
                <div class="tk-title"><?php echo esc_html(get_admin_page_title()); ?></div>
                <div class="tk-sub">Scannez un billet pour vérifier son statut.</div>
            </div>
        </header>
        <div class="tk-card tk-scanner">
            <div id="qr-reader"></div>
            <div id="scan-result"></div>
            <button id="rescan-btn" class="tk-btn primary">Re-scan</button>
        </div>
    </section>
</div>
