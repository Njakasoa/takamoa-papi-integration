document.querySelectorAll('.takamoa-papi-app').forEach((el) => {
	new Vue({
		el,
		data: {
			clientFirstName: '',
			clientLastName: '',
			amount: el.dataset.amount || '',
			reference: el.dataset.reference || '',
			payment: el.dataset.payment === 'no' ? 'no' : 'yes',
			ticket: el.dataset.ticket || '',
			payerEmail: '',
			payerPhone: '',
			description: '',
			provider: '',
			loading: false,
			link: '',
			status: '',
			error: '',
			success: '',
			polling: null,
		},
		computed: {
			clientName() {
				return `${this.clientFirstName} ${this.clientLastName}`.trim();
			},
			fields() {
				return TakamoaPapiVars.optionalFields || [];
			},
			providers() {
				return TakamoaPapiVars.providers || [];
			},
		},
		mounted() {
			if (this.providers.length === 1) {
				this.provider = this.providers[0];
			}
		},
		methods: {
			submitForm() {
				this.loading = true;
				this.error = '';
				this.link = '';
				this.success = '';
				this.status = '';

				const data = {
					action: 'takamoa_create_payment',
					_ajax_nonce: TakamoaPapiVars.api_nonce,
					clientName: this.clientName,
					amount: this.amount,
					reference: this.reference,
					payerEmail: this.payerEmail,
					payerPhone: this.payerPhone,
					description: this.description,
					design_id: this.ticket,
				};

				if (this.provider) {
					data.provider = this.provider;
				}

				fetch(TakamoaPapiVars.ajax_url, {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams(data),
				})
					.then((res) => res.json())
					.then((res) => {
						if (res.success && res.data.link) {
							this.link = res.data.link;
							if (this.payment === 'yes') {
								window.open(this.link, '_blank');
								this.polling = setInterval(this.checkStatus, 2000);
							} else {
								this.success = 'Merci pour votre inscription.';
								this.loading = false;
							}
						} else {
							this.error = res.data?.message || 'Erreur';
							this.loading = false;
						}
					})
					.catch(() => {
						this.loading = false;
						this.error = 'Erreur réseau. Veuillez réessayer.';
					});
			},
			checkStatus() {
				if (!this.reference) return;

				const data = {
					action: 'takamoa_check_payment_status',
					_ajax_nonce: TakamoaPapiVars.api_nonce,
					reference: this.reference,
				};

				fetch(TakamoaPapiVars.ajax_url, {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams(data),
				})
					.then((res) => res.json())
					.then((res) => {
						if (res.success && res.data.status) {
							const status = res.data.status;

							if (status === 'SUCCESS') {
								clearInterval(this.polling);
								this.status = 'SUCCESS';
								this.success = 'Paiement confirmé. Merci pour votre inscription.';
								this.loading = false;
							} else if (status === 'FAILED') {
								clearInterval(this.polling);
								this.status = 'FAILED';
								this.error =
									'Le paiement a échoué. Veuillez réessayer ou contacter le support.';
								this.loading = false;
							}
						}
					})
					.catch(() => {
						clearInterval(this.polling);
						this.error = 'Erreur lors de la vérification du paiement.';
						this.loading = false;
					});
			},
		},
		template: `
			<div class="takamoa-papi-form" :class="{ 'is-loading': loading }">
				<div class="takamoa-papi-loading-overlay" v-if="loading && payment === 'yes'">
					<div class="spinner"></div>
					<p v-if="link" class="takamoa-papi-message">
						Félicitations ! Vous êtes inscrit.<br>
						Pour confirmer votre place, veuillez procéder au paiement :
						<br>
						<a :href="link" target="_blank" class="takamoa-papi-link">Confirmer mon inscription par paiement</a>
					</p>
				</div>

				<form @submit.prevent="submitForm" class="takamoa-papi-form-box">
					<input id="clientLastName" name="clientLastName" v-model="clientLastName" required placeholder="NOM" class="takamoa-papi-input" />
					<input id="clientFirstName" name="clientFirstName" v-model="clientFirstName" required placeholder="PRÉNOM" class="takamoa-papi-input" />
					<input id="payerPhone" name="payerPhone" v-model="payerPhone" required placeholder="TÉLÉPHONE" class="takamoa-papi-input" />
					<input id="payerEmail" name="payerEmail" v-model="payerEmail" required placeholder="EMAIL" class="takamoa-papi-input" />
					<input id="description" name="description" v-model="description" required placeholder="ENTREPRISE" class="takamoa-papi-input" />

					<div v-if="providers.length > 1">
						<select id="provider" name="provider" v-model="provider" class="takamoa-papi-input">
							<option disabled value="">Choisir une méthode de paiement</option>
							<option v-for="p in providers" :value="p">{{ p }}</option>
						</select>
					</div>

					<button type="submit" :disabled="loading" class="takamoa-papi-button">
						{{ loading ? 'Veuillez patienter...' : 'S’INSCRIRE' }}
					</button>

					<p v-if="success" class="takamoa-papi-success">{{ success }}</p>
					<p v-if="error" class="takamoa-papi-error">{{ error }}</p>
				</form>
			</div>
		`,
	});
});
