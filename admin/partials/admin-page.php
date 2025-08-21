<?php
/**
 * Main plugin configuration page.
 *
 * Matches design of designs-page UI.
 *
 * @since 0.0.7
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
            .tk-wrap{
                font:14px/1.4 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;
                color:var(--text);
                background:var(--bg);
                padding:24px;
                border-radius:var(--radius);
                box-shadow:0 0 0 1px #0f1320,0 10px 30px rgba(0,0,0,.35);
            }
            .tk-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;}
            .tk-title{font-size:18px;font-weight:700;letter-spacing:.2px;}
            .tk-sub{color:var(--muted);font-size:13px}
            .tk-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;}
            .tk-fields{display:grid;gap:14px;}
            .tk-field{display:flex;flex-direction:column;gap:8px;}
            .tk-label{font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;}
            .tk-input{width:100%;box-sizing:border-box;background:#0e1320;color:var(--text);border:1px solid var(--border);border-radius:12px;padding:12px 12px;outline:none;transition:.15s border-color,.15s box-shadow,.15s transform;}
            .tk-input:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--ring);}
            .tk-btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:10px 14px;border:1px solid var(--border);background:#0f1526;color:var(--text);cursor:pointer;transition:.15s transform,.15s background,.15s border-color;text-decoration:none;font-weight:600;}
            .tk-btn:hover{transform:translateY(-1px);border-color:#34405a;}
            .tk-btn:active{transform:translateY(0);}
            .tk-btn.primary{background:var(--primary);border-color:var(--primary);color:white;}
            .tk-btn.primary:hover{background:var(--primary-press);}
            .tk-actions{display:flex;gap:10px;flex-wrap:wrap;}
            .tk-hint{color:var(--muted);font-size:12px;}
        </style>
        <header class="tk-header">
            <div>
                <div class="tk-title"><?php echo esc_html(get_admin_page_title()); ?></div>
                <div class="tk-sub">Collez ici votre clé API Papi.mg (retrouvable dans votre espace boutique).</div>
            </div>
        </header>
        <form method="post" action="options.php" class="tk-card tk-fields">
            <?php
            settings_fields('takamoa_papi_key_group');
            $value = esc_attr(get_option('takamoa_papi_api_key', ''));
            ?>
            <div class="tk-field">
                <label class="tk-label" for="takamoa_papi_api_key">Clé API</label>
                <input type="text" id="takamoa_papi_api_key" name="takamoa_papi_api_key" class="tk-input" value="<?php echo $value; ?>" />
                <span class="tk-hint">Cette clé permet la connexion à Papi.mg.</span>
            </div>
            <div class="tk-field tk-actions">
                <button type="submit" class="tk-btn primary">Enregistrer</button>
            </div>
        </form>
    </section>
</div>
