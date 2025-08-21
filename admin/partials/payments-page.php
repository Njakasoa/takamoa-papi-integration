<?php
/**
 * Payments history page with unified design.
 *
 * @since 0.0.8
 */
?>
<div class="wrap container-fluid">
    <h1>Historique des paiements</h1>
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
            .tk-actions{display:flex;gap:8px;flex-wrap:wrap;}
        </style>
        <div class="tk-card">
            <table id="takamoa-payments-table" class="tk-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Montant</th>
                        <th>Status</th>
                        <th>Méthode</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row) : ?>
                        <tr class="payment-row"
                            data-reference="<?= esc_attr($row->reference) ?>"
                            data-client="<?= esc_attr($row->client_name) ?>"
                            data-email="<?= esc_attr($row->payer_email) ?>"
                            data-phone="<?= esc_attr($row->payer_phone) ?>"
                            data-amount="<?= esc_attr(number_format($row->amount, 0, '', ' ') . ' MGA') ?>"
                            data-status="<?= esc_attr($row->payment_status) ?>"
                            data-method="<?= esc_attr($row->payment_method ?: '—') ?>"
                            data-date="<?= esc_attr($row->created_at) ?>"
                            data-description="<?= esc_attr($row->description) ?>"
                            data-provider="<?= esc_attr($row->provider) ?>"
                            data-success-url="<?= esc_attr($row->success_url) ?>"
                            data-failure-url="<?= esc_attr($row->failure_url) ?>"
                            data-notification-url="<?= esc_attr($row->notification_url) ?>"
                            data-link-creation="<?= esc_attr($row->link_creation) ?>"
                            data-link-expiration="<?= esc_attr($row->link_expiration) ?>"
                            data-payment-link="<?= esc_attr($row->payment_link) ?>"
                            data-currency="<?= esc_attr($row->currency) ?>"
                            data-fee="<?= esc_attr($row->fee) ?>"
                            data-notification-token="<?= esc_attr($row->notification_token) ?>"
                            data-is-test-mode="<?= esc_attr($row->is_test_mode) ?>"
                            data-test-reason="<?= esc_attr($row->test_reason) ?>"
                            data-raw-request="<?= esc_attr($row->raw_request) ?>"
                            data-raw-response="<?= esc_attr($row->raw_response) ?>"
                            data-raw-notification="<?= esc_attr($row->raw_notification) ?>"
                            data-updated-at="<?= esc_attr($row->updated_at) ?>"
                            data-id="<?= esc_attr($row->id) ?>">
                            <td><?= esc_html($row->reference) ?></td>
                            <td><?= esc_html($row->client_name) ?></td>
                            <td><?= esc_html($row->payer_email ?: '—') ?></td>
                            <td><?= esc_html($row->payer_phone ?: '—') ?></td>
                            <td><?= number_format($row->amount, 0, '', ' ') ?> MGA</td>
                            <td><?= esc_html($row->payment_status) ?></td>
                            <td><?= esc_html($row->payment_method ?: '—') ?></td>
                            <td><?= esc_html($row->created_at) ?></td>
                            <td>
                                <div class="tk-actions">
                                    <button type="button" class="tk-btn takamoa-notify">Notifier</button>
                                    <button type="button" class="tk-btn takamoa-regenerate-link" data-reference="<?= esc_attr($row->reference) ?>">Regénérer le lien</button>
                                    <button type="button" class="tk-btn takamoa-generate-ticket">Générer un billet</button>
                                    <button type="button" class="tk-btn takamoa-details">Détails</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade tk-modal" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails du paiement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="tk-table">
                            <tbody id="modal-basic-info">
                                <tr><th>ID</th><td id="modal-id"></td></tr>
                                <tr><th>Référence</th><td id="modal-reference"></td></tr>
                                <tr><th>Nom client</th><td id="modal-name"></td></tr>
                                <tr><th>Email</th><td id="modal-email"></td></tr>
                                <tr><th>Téléphone</th><td id="modal-phone"></td></tr>
                                <tr><th>Montant</th><td id="modal-amount"></td></tr>
                                <tr><th>Status</th><td id="modal-status"></td></tr>
                                <tr><th>Méthode</th><td id="modal-method"></td></tr>
                                <tr><th>Date</th><td id="modal-date"></td></tr>
                                <tr><th>Description</th><td id="modal-description"></td></tr>
                            </tbody>
                            <tbody id="modal-extra-info" class="d-none">
                                <tr><th>Provider</th><td id="modal-provider"></td></tr>
                                <tr><th>Success URL</th><td id="modal-success-url"></td></tr>
                                <tr><th>Failure URL</th><td id="modal-failure-url"></td></tr>
                                <tr><th>Notification URL</th><td id="modal-notification-url"></td></tr>
                                <tr><th>Link creation</th><td id="modal-link-creation"></td></tr>
                                <tr><th>Link expiration</th><td id="modal-link-expiration"></td></tr>
                                <tr><th>Payment link</th><td id="modal-payment-link"></td></tr>
                                <tr><th>Currency</th><td id="modal-currency"></td></tr>
                                <tr><th>Fee</th><td id="modal-fee"></td></tr>
                                <tr><th>Notification token</th><td id="modal-notification-token"></td></tr>
                                <tr><th>Test mode</th><td id="modal-test-mode"></td></tr>
                                <tr><th>Test reason</th><td id="modal-test-reason"></td></tr>
                                <tr><th>Raw request</th><td><pre id="modal-raw-request" class="mb-0"></pre></td></tr>
                                <tr><th>Raw response</th><td><pre id="modal-raw-response" class="mb-0"></pre></td></tr>
                                <tr><th>Raw notification</th><td><pre id="modal-raw-notification" class="mb-0"></pre></td></tr>
                                <tr><th>Updated at</th><td id="modal-updated-at"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tk-btn" id="toggle-more-info">Show more</button>
                    <button type="button" class="tk-btn" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade tk-modal" id="ticketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Générer un billet</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="ticket-design" class="form-select">
                        <?php foreach ($designs as $d) : ?>
                            <option value="<?= esc_attr($d->id) ?>" <?= selected($d->id, $default_design, false) ?>>
                                <?= esc_html($d->title) ?><?= $d->id == $default_design ? ' (par défaut)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tk-btn" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" id="generate-ticket-btn" class="tk-btn">Générer</button>
                </div>
            </div>
        </div>
    </div>
</div>
