<?php
	/**
	* Tickets management page with new design.
	*
	* @since 0.0.8
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
            .tk-table{width:100%;border-collapse:collapse;}
            .tk-table th,.tk-table td{padding:12px;text-align:left;border-bottom:1px solid var(--border);}
            .tk-table th{background:#101622;font-weight:600;}
            .tk-table tbody tr:nth-child(even){background:#0e1320;}
            .tk-table tbody tr:last-child td{border-bottom:none;}
            .tk-table tbody tr:hover{background:#0f1526;}
            .tk-btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:10px 14px;border:1px solid var(--border);background:#0f1526;color:var(--text);cursor:pointer;transition:.15s transform,.15s background,.15s border-color;text-decoration:none;font-weight:600;}
            .tk-btn:hover{transform:translateY(-1px);border-color:#34405a;}
            .tk-btn:active{transform:translateY(0);}
            .tk-btn.danger{border-color:var(--err);color:var(--err);}
            .tk-btn.danger:hover{background:rgba(255,107,107,.1);}
		</style>
        <header class="tk-header">
            <div>
                <div class="tk-title"><?php echo esc_html(get_admin_page_title()); ?></div>
                <div class="tk-sub">Liste des billets générés.</div>
            </div>
		</header>
		<div class="tk-card">
			<table id="takamoa-tickets-table" class="tk-table">
				<thead>
					<tr>
						<th>Référence</th>
						<th>Description</th>
						<th>QR Code</th>
						<th>Date création</th>
						<th>Date mise à jour</th>
						<th>Status</th>
						<th>Dernière notification</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($results as $row) : ?>
						<tr data-reference="<?= esc_attr($row->reference) ?>">
							<td><?= esc_html($row->reference) ?></td>
							<td><?= esc_html($row->description ?: '—') ?></td>
							<td><?= $row->qrcode_link ? '<a href="' . esc_url($row->qrcode_link) . '" target="_blank">Voir</a>' : '—'; ?></td>
							<td><?= esc_html($row->created_at) ?></td>
							<td><?= esc_html($row->updated_at ?: '—') ?></td>
							<td><?= esc_html($row->status) ?></td>
							<td><?= esc_html($row->last_notification ?: '—') ?></td>
							<td>
								<button type="button" class="tk-btn takamoa-send-ticket-email">Envoyer par email</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>
