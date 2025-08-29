<?php
	/**
	* Advanced options page.
	*
	* @since 0.0.6
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
            .tk-hint{color:var(--muted);font-size:12px;}
            .tk-input,.tk-number{width:100%;box-sizing:border-box;background:#0e1320;color:var(--text);border:1px solid var(--border);border-radius:12px;padding:12px 12px;outline:none;transition:.15s border-color,.15s box-shadow,.15s transform;}
            .tk-input:focus,.tk-number:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--ring);}
            .tk-number{appearance:textfield;}
            .tk-number::-webkit-outer-spin-button,.tk-number::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
            .tk-row{display:grid;gap:12px;grid-template-columns:1fr 1fr;}
            @media (max-width:680px){.tk-row{grid-template-columns:1fr;}}
            .tk-btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:10px 14px;border:1px solid var(--border);background:#0f1526;color:var(--text);cursor:pointer;transition:.15s transform,.15s background,.15s border-color;text-decoration:none;font-weight:600;}
            .tk-btn:hover{transform:translateY(-1px);border-color:#34405a;}
            .tk-btn:active{transform:translateY(0);}
            .tk-btn.primary{background:var(--primary);border-color:var(--primary);color:white;}
            .tk-btn.primary:hover{background:var(--primary-press);}
            .tk-actions{display:flex;gap:10px;flex-wrap:wrap;}
		</style>
        <header class="tk-header">
            <div>
                <div class="tk-title">Options avancées</div>
                <div class="tk-sub">Configurez les paramètres de l'intégration.</div>
            </div>
		</header>
		<form method="post" action="options.php" class="tk-card tk-fields">
			<?php
				settings_fields('takamoa_papi_settings_group');
				$default_success = home_url('/paiementreussi');
				$success_value = esc_attr(get_option('takamoa_papi_success_url', $default_success));
				$default_failure = home_url('/paiementechoue');
				$failure_value = esc_attr(get_option('takamoa_papi_failure_url', $default_failure));
				$duration = esc_attr(get_option('takamoa_papi_valid_duration', 60));
				$providers = (array) get_option('takamoa_papi_providers', []);
				$fields = (array) get_option('takamoa_papi_optional_fields', []);
				$test_mode = get_option('takamoa_papi_test_mode', false);
				$test_reason = esc_attr(get_option('takamoa_papi_test_reason'));
			?>
			<div class="tk-field">
				<label class="tk-label" for="takamoa_papi_success_url">URL après succès</label>
				<input type="url" id="takamoa_papi_success_url" name="takamoa_papi_success_url" class="tk-input" value="<?php echo $success_value; ?>" />
				<span class="tk-hint">Par défaut : <code><?php echo esc_html($default_success); ?></code></span>
			</div>
			<div class="tk-field">
				<label class="tk-label" for="takamoa_papi_failure_url">URL après échec</label>
				<input type="url" id="takamoa_papi_failure_url" name="takamoa_papi_failure_url" class="tk-input" value="<?php echo $failure_value; ?>" />
				<span class="tk-hint">Par défaut : <code><?php echo esc_html($default_failure); ?></code></span>
			</div>
			<div class="tk-field">
				<label class="tk-label" for="takamoa_papi_valid_duration">Durée de validité du lien (en minutes)</label>
				<input type="number" id="takamoa_papi_valid_duration" name="takamoa_papi_valid_duration" class="tk-number" min="1" value="<?php echo $duration; ?>" />
			</div>
			<div class="tk-field">
				<label class="tk-label">Méthodes de paiement disponibles</label>
				<div class="tk-row">
					<?php
						$all_providers = [
							'MVOLA' => 'MVOLA',
							'ORANGE_MONEY' => 'Orange Money',
							'AIRTEL_MONEY' => 'Airtel Money',
							'BRED' => 'BRED'
						];
						foreach ($all_providers as $key => $label) {
							$checked = in_array($key, $providers) ? 'checked' : '';
							echo '<label class="tk-label"><input type="checkbox" name="takamoa_papi_providers[]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label>';
						}
					?>
				</div>
			</div>
			<div class="tk-field">
				<label class="tk-label">Champs à afficher dans le formulaire</label>
				<div class="tk-row">
					<?php
						$all_fields = [
							'payerEmail' => 'Email client',
							'payerPhone' => 'Téléphone client'
						];
						foreach ($all_fields as $key => $label) {
							$checked = in_array($key, $fields) ? 'checked' : '';
							echo '<label class="tk-label"><input type="checkbox" name="takamoa_papi_optional_fields[]" value="' . esc_attr($key) . '" ' . $checked . '> ' . esc_html($label) . '</label>';
						}
					?>
				</div>
			</div>
			<div class="tk-field">
				<label class="tk-label" for="takamoa_papi_test_mode">Mode test (transactions réelles)</label>
				<input type="checkbox" id="takamoa_papi_test_mode" name="takamoa_papi_test_mode" value="1" <?php checked($test_mode, true); ?> />
			</div>
			<div class="tk-field">
				<label class="tk-label" for="takamoa_papi_test_reason">Raison du test</label>
				<input type="text" id="takamoa_papi_test_reason" name="takamoa_papi_test_reason" class="tk-input" value="<?php echo $test_reason; ?>" />
			</div>
			<div class="tk-field tk-actions">
				<button type="submit" class="tk-btn primary">Enregistrer</button>
			</div>
		</form>
	</section>
</div>
