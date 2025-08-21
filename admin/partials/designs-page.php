<?php
/**
 * Ticket designs management page.
 *
 * @since 0.0.3
 */
?>
                               <div class="wrap container-fluid">
                                               <h1>Designs de billets</h1>
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
                                                                               font: 14px/1.4 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;
                                                                               color:var(--text);
                                                                               background:var(--bg);
                                                                               padding:24px;
                                                                               border-radius:var(--radius);
                                                                               box-shadow:0 0 0 1px #0f1320,0 10px 30px rgba(0,0,0,.35);
                                                                       }
                                                                       .tk-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;}
                                                                       .tk-title{font-size:18px;font-weight:700;letter-spacing:.2px;}
                                                                       .tk-sub{color:var(--muted);font-size:13px}
                                                                       .tk-grid{display:grid;gap:var(--gap);grid-template-columns:1.1fr .9fr;}
                                                                       @media (max-width:1100px){.tk-grid{grid-template-columns:1fr;}}
                                                                       .tk-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;}
                                                                       .tk-fields{display:grid;gap:14px;grid-template-columns:1fr 1fr;}
                                                                       .tk-fields .full{grid-column:1 / -1;}
                                                                       .tk-field{display:flex;flex-direction:column;gap:8px;position:relative;}
                                                                       .tk-label{font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;}
                                                                       .tk-hint{color:var(--muted);font-size:12px;}
                                                                       .tk-input,.tk-number,.tk-text{width:100%;box-sizing:border-box;background:#0e1320;color:var(--text);border:1px solid var(--border);border-radius:12px;padding:12px 12px;outline:none;transition:.15s border-color,.15s box-shadow,.15s transform;}
                                                                       .tk-input:focus,.tk-number:focus,.tk-text:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--ring);}
                                                                       .tk-number{appearance:textfield;}
                                                                       .tk-number::-webkit-outer-spin-button,.tk-number::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
                                                                       .tk-row{display:grid;gap:12px;grid-template-columns:1fr 1fr;}
                                                                       .tk-row-3{grid-template-columns:1fr 1fr 1fr;}
                                                                       @media (max-width:680px){.tk-row,.tk-row-3{grid-template-columns:1fr;}}
                                                                       .tk-btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:10px 14px;border:1px solid var(--border);background:#0f1526;color:var(--text);cursor:pointer;transition:.15s transform,.15s background,.15s border-color;text-decoration:none;font-weight:600;}
                                                                       .tk-btn:hover{transform:translateY(-1px);border-color:#34405a;}
                                                                       .tk-btn:active{transform:translateY(0);}
                                                                       .tk-btn.primary{background:var(--primary);border-color:var(--primary);color:white;}
                                                                       .tk-btn.primary:hover{background:var(--primary-press);}
                                                                       .tk-actions{display:flex;gap:10px;flex-wrap:wrap;}
                                                                       .tk-preview-wrap{display:grid;gap:12px;}
                                                                       .tk-preview-head{display:flex;align-items:center;justify-content:space-between;}
                                                                       .tk-badge{font-size:11px;color:#cfe0ff;background:#152138;padding:6px 8px;border-radius:999px;border:1px solid #1b2a49;}
                                                                       .tk-preview{position:relative;width:100%;border-radius:14px;overflow:hidden;border:1px dashed #33405b;background:#0c1220;min-height:180px;}
                                                                       .tk-preview img{display:block;width:100%;height:auto;object-fit:contain;background:#0c1220;}
                                                                       .tk-qr{position:absolute;border:2px dashed var(--warn);border-radius:6px;box-shadow:inset 0 0 0 1px rgba(0,0,0,.25),0 0 0 3px rgba(255,176,32,.15);display:flex;align-items:center;justify-content:center;font-size:11px;color:#ffdba0;background:rgba(255,176,32,.08);pointer-events:none;}
                                                                       .tk-metrics{display:flex;gap:12px;flex-wrap:wrap;color:var(--muted);font-size:12px;}
                                                                       .tk-metrics .dot{width:6px;height:6px;background:var(--primary);border-radius:999px;display:inline-block;margin-right:6px;}
                                                                       .tk-footer{margin-top:18px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
                                                                       .tk-small{color:var(--muted);font-size:12px;}
                                                                       .tk-valid{color:var(--ok);}
                                                                       .tk-bad{color:var(--err);}
                                                                       .tk-table{width:100%;border-collapse:collapse;}
                                                                       .tk-table th,.tk-table td{padding:12px;text-align:left;border-bottom:1px solid var(--border);}
                                                                       .tk-table th{background:#101622;font-weight:600;}
                                                                       .tk-table tbody tr:nth-child(even){background:#0e1320;}
                                                                       .tk-table tbody tr:last-child td{border-bottom:none;}
                                                                       .tk-table tbody tr:hover{background:#0f1526;}
                                                                       .tk-icon-btn{background:none;border:none;color:var(--text);cursor:pointer;padding:4px;border-radius:8px;}
                                                                       .tk-icon-btn:hover{background:#0f1526;}
                                                                       .tk-star{color:var(--warn);}
                                                                       .tk-btn.danger{border-color:var(--err);color:var(--err);}
                                                                       .tk-btn.danger:hover{background:rgba(255,107,107,.1);}
                                                                       .tk-btn[disabled]{opacity:.5;cursor:not-allowed;}
                                                               </style>

                                                               <header class="tk-header">
                                                                               <div>
                                                                                               <div class="tk-title">Créer un design de billet</div>
                                                                                               <div class="tk-sub">Définis l’image, les dimensions et place le QR code précisément avec l’aperçu en direct.</div>
                                                                               </div>
                                                                               <div class="tk-actions">
                                                                                               <button type="button" class="tk-btn" id="tk-fill-demo" title="Remplir des valeurs de démo">Remplir démo</button>
                                                                               </div>
                                                               </header>

                                                               <div class="tk-grid">
                                                                               <div class="tk-card">
                                                                                               <form id="takamoa-add-design" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
                                                                                                               <?php wp_nonce_field('takamoa_save_design'); ?>
                                                                                                               <input type="hidden" name="action" value="takamoa_save_design">

                                                                                                               <div class="tk-fields">
                                                                                                                               <div class="tk-field full">
                                                                                                                                               <label class="tk-label" for="design_title">Titre du design</label>
                                                                                                                                               <input type="text" id="design_title" name="design_title" class="tk-input" placeholder="Ex. Billet Concert VIP – 210×74 mm">
                                                                                                                                               <span class="tk-hint">Nom interne visible dans l’admin.</span>
                                                                                                                               </div>

                                                                                                                               <div class="tk-field full">
                                                                                                                                               <label class="tk-label" for="design_image">Image du billet</label>
                                                                                                                                               <div style="display:grid;grid-template-columns:1fr auto;gap:10px;">
                                                                                                                                                               <input type="text" id="design_image" name="design_image" class="tk-input" placeholder="https://…/billet.png">
                                                                                                                                                               <button type="button" class="tk-btn" id="select_design_image" title="Choisir depuis la médiathèque">Sélectionner…</button>
                                                                                                                                               </div>
                                                                                                                                               <span class="tk-hint">PNG/JPG conseillé. L’aperçu se mettra à jour automatiquement.</span>
                                                                                                                               </div>

                                                                                                                               <div class="tk-field full">
                                                                                                                                               <label class="tk-label">Dimensions du billet (px)</label>
                                                                                                                                               <div class="tk-row">
                                                                                                                                                               <div class="tk-field">
                                                                                                                                                                               <label class="tk-label" for="ticket_width">Largeur</label>
                                                                                                                                                                               <input type="number" id="ticket_width" name="ticket_width" class="tk-number" min="1" placeholder="ex. 2480">
                                                                                                                                                               </div>
                                                                                                                                                               <div class="tk-field">
                                                                                                                                                                               <label class="tk-label" for="ticket_height">Hauteur</label>
                                                                                                                                                                               <input type="number" id="ticket_height" name="ticket_height" class="tk-number" min="1" placeholder="ex. 874">
                                                                                                                                                               </div>
                                                                                                                                               </div>
                                                                                                                                               <span class="tk-hint">Doit correspondre aux dimensions réelles du fichier image (en pixels).</span>
                                                                                                                               </div>

                                                                                                                               <div class="tk-field full">
                                                                                                                                               <label class="tk-label">QR Code (px)</label>
                                                                                                                                               <div class="tk-row-3">
                                                                                                                                                               <div class="tk-field">
                                                                                                                                                                               <label class="tk-label" for="qrcode_size">Taille</label>
                                                                                                                                                                               <input type="number" id="qrcode_size" name="qrcode_size" class="tk-number" min="1" placeholder="ex. 380">
                                                                                                                                                               </div>
                                                                                                                                                               <div class="tk-field">
                                                                                                                                                                               <label class="tk-label" for="qrcode_top">Position top</label>
                                                                                                                                                                               <input type="number" id="qrcode_top" name="qrcode_top" class="tk-number" min="0" placeholder="ex. 72">
                                                                                                                                                               </div>
                                                                                                                                                               <div class="tk-field">
                                                                                                                                                                               <label class="tk-label" for="qrcode_left">Position left</label>
                                                                                                                                                                               <input type="number" id="qrcode_left" name="qrcode_left" class="tk-number" min="0" placeholder="ex. 1820">
                                                                                                                                                               </div>
                                                                                                                                               </div>
                                                                                                                                               <span class="tk-hint">Réglages en pixels relatifs à l’image d’origine.</span>
                                                                                                                               </div>

                                                                                                                               <div class="tk-field full" style="margin-top:6px;">
                                                                                                                                               <div class="tk-actions">
                                                                                                                                                               <button type="submit" name="submit" id="submit" class="tk-btn primary">Ajouter</button>
                                                                                                                                                               <button type="button" class="tk-btn" id="tk-center-qr">Centrer le QR</button>
                                                                                                                                                               <button type="button" class="tk-btn" id="tk-reset">Réinitialiser</button>
                                                                                                                                               </div>
                                                                                                                                               <div class="tk-small" id="tk-quick-check"></div>
                                                                                                                               </div>
                                                                                                               </div>
                                                                                               </form>
                                                                               </div>

                                                                               <aside class="tk-card tk-preview-wrap" aria-live="polite">
                                                                                               <div class="tk-preview-head">
                                                                                                               <div class="tk-title" style="font-size:15px;">Aperçu en direct</div>
                                                                                                               <span class="tk-badge" id="tk-scale-badge">100%</span>
                                                                                               </div>

                                                                                               <div class="tk-preview" id="tk-preview">
                                                                                                               <img id="tk-preview-img" alt="Prévisualisation du billet (se met à jour lorsque l'URL est renseignée)">
                                                                                                               <div class="tk-qr" id="tk-qr" style="display:none;">QR</div>
                                                                                               </div>

                                                                                               <div class="tk-metrics" id="tk-metrics">
                                                                                                               <div><span class="dot"></span>Billet&nbsp;<strong id="m-w">–</strong> × <strong id="m-h">–</strong> px</div>
                                                                                                               <div><span class="dot"></span>QR&nbsp;<strong id="m-s">–</strong> px | top <strong id="m-t">–</strong> px | left <strong id="m-l">–</strong> px</div>
                                                                                                               <div><span class="dot"></span>Affichage&nbsp;<strong id="m-dw">–</strong> × <strong id="m-dh">–</strong> px</div>
                                                                                               </div>

                                                                                               <div class="tk-footer">
                                                                                                               <div class="tk-small">Astuce : utilise “Centrer le QR” pour valider rapidement un placement de base.</div>
                                                                                               </div>
                                                                               </aside>
                                                               </div>

                                                               <script>
                                                                       (function(){
                                                                               const $ = sel => document.querySelector(sel);
                                                                               const designImgInput = $('#design_image');
                                                                               const previewImg = $('#tk-preview-img');
                                                                               const previewWrap = $('#tk-preview');
                                                                               const qr = $('#tk-qr');
                                                                               const w = $('#ticket_width');
                                                                               const h = $('#ticket_height');
                                                                               const s = $('#qrcode_size');
                                                                               const t = $('#qrcode_top');
                                                                               const l = $('#qrcode_left');
                                                                               const scaleBadge = $('#tk-scale-badge');
                                                                               const quick = $('#tk-quick-check');

                                                                               const mW = $('#m-w'), mH = $('#m-h'), mS = $('#m-s'), mT = $('#m-t'), mL = $('#m-l'), mDW = $('#m-dw'), mDH = $('#m-dh');

                                                                               const mediaBtn = $('#select_design_image');
                                                                               if (mediaBtn) {
                                                                                               mediaBtn.addEventListener('click', function(){
                                                                                                               if (window.wp && wp.media) {
                                                                                                                               const frame = wp.media({
                                                                                                                                               title: 'Sélectionner l’image du billet',
                                                                                                                                               button: { text: 'Utiliser cette image' },
                                                                                                                                               library: { type: 'image' },
                                                                                                                                               multiple: false
                                                                                                                               });
                                                                                                                               frame.on('select', function(){
                                                                                                                                               const file = frame.state().get('selection').first().toJSON();
                                                                                                                                               designImgInput.value = file.url || '';
                                                                                                                                               previewImg.src = file.url || '';
                                                                                                                                               if (file.width) { w.value = file.width; }
                                                                                                                                               if (file.height) { h.value = file.height; }
                                                                                                                                               setTimeout(updatePreview, 50);
                                                                                                                               });
                                                                                                                               frame.open();
                                                                                                               } else {
                                                                                                                               alert("Médiathèque WordPress indisponible ici.\nRenseigne l’URL manuellement.");
                                                                                                               }
                                                                                               });
                                                                               }

                                                                               const inputs = [designImgInput, w, h, s, t, l];
                                                                               inputs.forEach(el => el && el.addEventListener('input', updatePreview));
                                                                               previewImg.addEventListener('load', updatePreview);
                                                                               window.addEventListener('resize', () => { requestAnimationFrame(updatePreview); });

                                                                               $('#tk-center-qr').addEventListener('click', () => {
                                                                                               const W = num(w.value), H = num(h.value), S = num(s.value);
                                                                                               if (!W || !H || !S) return;
                                                                                               t.value = Math.max(0, Math.round((H - S) / 2));
                                                                                               l.value = Math.max(0, Math.round((W - S) / 2));
                                                                                               updatePreview();
                                                                               });

                                                                               $('#tk-reset').addEventListener('click', () => {
                                                                                               document.getElementById('takamoa-add-design').reset();
                                                                                               previewImg.removeAttribute('src');
                                                                                               qr.style.display = 'none';
                                                                                               updatePreview();
                                                                               });

                                                                               $('#tk-fill-demo').addEventListener('click', () => {
                                                                                               if (!designImgInput.value) { designImgInput.value = 'https://picsum.photos/2480/874'; }
                                                                                               if (!previewImg.src) { previewImg.src = designImgInput.value; }
                                                                                               w.value = 2480; h.value = 874; s.value = 380; t.value = 72; l.value = 1820;
                                                                                               updatePreview();
                                                                               });

                                                                               function num(v){ const n = parseFloat(v); return isFinite(n) ? n : 0; }

                                                                               function updatePreview(){
                                                                                               const W = num(w.value), H = num(h.value), S = num(s.value), T = num(t.value), L = num(l.value);
                                                                                               mW.textContent = W || '–'; mH.textContent = H || '–';
                                                                                               mS.textContent = S || '–'; mT.textContent = T || '–'; mL.textContent = L || '–';

                                                                                               const url = designImgInput.value.trim();
                                                                                               if (url && previewImg.src !== url) previewImg.src = url;

                                                                                               const rect = previewImg.getBoundingClientRect();
                                                                                               const displayedW = Math.round(rect.width || 0);
                                                                                               const displayedH = Math.round(rect.height || 0);
                                                                                               mDW.textContent = displayedW || '–';
                                                                                               mDH.textContent = displayedH || '–';

                                                                                               let scaleX = 1, scaleY = 1, scalePct = 100;
                                                                                               if (W > 0 && H > 0 && displayedW > 0 && displayedH > 0){
                                                                                                               scaleX = displayedW / W;
                                                                                                               scaleY = displayedH / H;
                                                                                                               scalePct = Math.round(((scaleX + scaleY) / 2) * 100);
                                                                                               }
                                                                                               scaleBadge.textContent = scalePct + '%';

                                                                                               if (W > 0 && H > 0 && S > 0 && (T >= 0) && (L >= 0) && displayedW > 0 && displayedH > 0) {
                                                                                                               const qW = Math.max(1, Math.round(S * scaleX));
                                                                                                               const qH = Math.max(1, Math.round(S * scaleY));
                                                                                                               const qT = Math.round(T * scaleY);
                                                                                                               const qL = Math.round(L * scaleX);

                                                                                                               qr.style.width = qW + 'px';
                                                                                                               qr.style.height = qH + 'px';
                                                                                                               qr.style.top = qT + 'px';
                                                                                                               qr.style.left = qL + 'px';
                                                                                                               qr.style.display = 'flex';

                                                                                                               const overflowX = (qL + qW) > displayedW;
                                                                                                               const overflowY = (qT + qH) > displayedH;

                                                                                                               if (overflowX || overflowY){
                                                                                                                               quick.innerHTML = '⚠️ <span class="tk-bad">Le QR déborde de l’image affichée.</span>';
                                                                                                               } else {
                                                                                                                               quick.innerHTML = '✅ <span class="tk-valid">Placement valide.</span>';
                                                                                                               }
                                                                                               } else {
                                                                                                               qr.style.display = 'none';
                                                                                                               quick.textContent = '';
                                                                                               }
                                                                               }

                                                                               updatePreview();
                                                                       })();
                                                               </script>
                                               </section>

                                               <section class="tk-wrap" style="margin-top:24px;">
               <div class="tk-card">
                   <table class="tk-table">
                       <thead>
                           <tr>
                               <th>Par défaut</th>
                               <th>ID</th>
                               <th>Titre</th>
                               <th>Image</th>
                               <th>Billet (px)</th>
                               <th>Taille QR</th>
                               <th>Position QR</th>
                               <th>Date</th>
                               <th>Actions</th>
                           </tr>
                       </thead>
                       <tbody>
                       <?php foreach ($designs as $d) : ?>
                           <tr>
                               <td>
                                   <button type="button" class="tk-icon-btn takamoa-set-default" data-id="<?= esc_attr($d->id) ?>" title="Définir comme par défaut">
                                       <i class="fa <?= $d->id == $default_design ? 'fa-star tk-star' : 'fa-star-o'; ?>" aria-hidden="true"></i>
                                       <span class="screen-reader-text">
                                           <?= $d->id == $default_design ? 'Design par défaut' : 'Définir comme par défaut'; ?>
                                       </span>
                                   </button>
                               </td>
                               <td><?= esc_html($d->id) ?></td>
                               <td><?= esc_html($d->title) ?></td>
                               <td><?= $d->image_url ? '<img src="' . esc_url($d->image_url) . '" style="max-width:150px;height:auto;" />' : '—'; ?></td>
                               <td><?= esc_html($d->ticket_width . '×' . $d->ticket_height) ?></td>
                               <td><?= esc_html($d->qrcode_size) ?></td>
                               <td><?= esc_html($d->qrcode_left . ',' . $d->qrcode_top) ?></td>
                               <td><?= esc_html($d->created_at) ?></td>
                               <td>
                                   <?php
                                   $delete_url = wp_nonce_url(
                                       admin_url('admin-post.php?action=takamoa_delete_design&design_id=' . intval($d->id)),
                                       'takamoa_delete_design_' . intval($d->id)
                                   );
                                   ?>
                                   <?php if ($d->id == $default_design) : ?>
                                       <button class="tk-btn danger" disabled>Supprimer</button>
                                   <?php else : ?>
                                       <a href="<?= esc_url($delete_url) ?>" class="tk-btn danger takamoa-delete-design" data-id="<?= esc_attr($d->id) ?>">Supprimer</a>
                                   <?php endif; ?>
                               </td>
                           </tr>
                       <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>
           </section>
                                </div>
