<template>
	<div class="col-lg-12">
		<div class="card card-outline-info">
			<div class="card-header">
            	<h4 class="m-b-0 text-white">{{ trans('panic.panic_config') }}</h4>
       		</div>

			<div class="card-block">
				<!-- Configurações gerais -->
				<div class="panel-heading">
					<h3 class="panel-title">{{ trans('panic.panic_general_config') }}</h3>
					<hr>
				</div>

				<div class="row">
					<!-- Ativar botão de pânico para usuário -->
					<div class="col-lg-6">
						<label for="usr"> 
							{{ trans('panic.panic_button_enabled_user') }}
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.panic_button_enabled_user')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<select class="form-control" v-model="general_settings.panic_button_enabled_user">
							<option value="true">{{ trans('setting.yes') }}</option>
							<option value="false">{{ trans('setting.no') }}</option>
						</select>
					</div>

					<!-- Ativar botãod e pânico para motorista -->
					<div class="col-lg-6">
						<label for="usr"> 
							{{ trans('panic.panic_button_enabled_driver') }}
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.panic_button_enabled_driver')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<select class="form-control" v-model="general_settings.panic_button_enabled_provider">
							<option value="true">{{ trans('setting.yes') }}</option>
							<option value="false">{{ trans('setting.no') }}</option>
						</select>
					</div>
				</div><br>

				<!-- Configurações Segup -->
				<div class="panel-heading">
					<h3 class="panel-title">{{ trans('panic.panic_segup_config') }}</h3>
					<hr>
				</div>

				<div class="row">
					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.segup_login')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.segup_login')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<input type="text" min="0" required class="form-control" v-model="segup_settings.segup_login">
					</div>

					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.segup_password')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.segup_password')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<input type="text" min="0" required class="form-control" v-model="segup_settings.segup_password">
					</div>
				</div><br>

				<div class="row">
					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.segup_request_url')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.segup_request_url')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<input type="text" min="0" required class="form-control" v-model="segup_settings.segup_request_url">
					</div>

					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.segup_verification_url')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.segup_verification_url')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<input type="text" min="0" required class="form-control" v-model="segup_settings.segup_verification_url">
					</div>
				</div><br>

				<!-- Configurações admin -->
				<div class="panel-heading">
					<h3 class="panel-title">{{ trans('panic.panic_admin_config') }}</h3>
					<hr>
				</div>

				<div class="row">
					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.admin')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.admin')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<select class="form-control" v-model="selectedAdmin">
							<option disabled value="">Escolha um administrador</option>

							<option v-for="admin in admins" :key="admin.username" :value="admin"> {{ admin.username }}</option>
						</select>
					</div>

					<div class="col-lg-6">
						<label for="usr"> 
							{{trans('panic.admin_phone')}} 
							<a href="#" class="question-field" data-toggle="tooltip" :title="trans('panic.admin_phone')"><span class="mdi mdi-comment-question-outline"></span></a> <span class="required-field"></span>
						</label>

						<input type="text" min="0" required class="form-control" v-model="admin_settings.panic_admin_phone_number">
					</div>
				</div><br><br>

				<div class="form-group text-right button-save">
					<button type="button" class="btn btn-success" @click="saveSettings">
						<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
							{{ trans('keywords.save') }}
						</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import axios from 'axios';

export default {
    props: [

	],
    data() {
        return {
            general_settings: {
                panic_button_enabled_user: false,
                panic_button_enabled_provider: false
            },
			segup_settings: {
				security_provider_agency: "segup",
				segup_login: "ramon@drivesocial.io",
				segup_password: "1q2w3e4r5t6y",
				segup_request_url: "http://sistemas.segup.pa.gov.br/alerta/api/posicao",
				segup_verification_url: "http://sistemas.segup.pa.gov.br/alerta/api/usuario/autenticar"
			},
			admin_settings: {
				panic_admin_id: null,
				panic_admin_phone_number: "",
				panic_admin_email: ""
			},
			admins: [
			],
			selectedAdmin: null
        };
    },
    methods: {
		async getSettings(rota) {
			let response = await axios.get(rota);

			if (response.status == 200) {
				return response.data;
			}

			return null;
		},
		saveSettings() {
			//primeiro tenta salvar as configurações gerais
			this.saveRequest(
				'/lib/panic/settings/save', 
				this.general_settings, 
				'Configurações gerais salvas com sucesso!', 
				'Erro ao atualizar configurações gerais'
			);

			//tenta salvar as configurações segup
			this.saveRequest(
				'/lib/panic/settings/save/segup', 
				this.segup_settings, 
				'Configurações Segup salvas com sucesso!', 
				'Erro ao atualizar configurações Segup'
			);

			//por fim tenta salvar as configurações do adm
			this.admin_settings.panic_admin_id = this.selectedAdmin.id;
			this.admin_settings.panic_admin_email = this.selectedAdmin.username;

			this.saveRequest(
				'/lib/panic/settings/save/admin', 
				this.admin_settings, 
				'Configurações do admin salvas com sucesso!', 
				'Erro ao atualizar configurações admin'
			);

			location.reload();
		},
		saveRequest(rota, settings, successMessage, errorMessage) {
			axios.post(rota, settings).then((response) => {
				if (response.status == 200) {
					this.$toasted.show(
                    	successMessage, 
						{ 
							theme: "bubble", 
							type: "danger" ,
							position: "bottom-center", 
							duration : 5000
						}
                	);
				} else {
					throw new Error(errorMessage);
				}
			}).catch((error) => {
				this.$toasted.show(
					error, 
					{ 
						theme: "bubble", 
						type: "danger" ,
						position: "bottom-center", 
						duration : 5000
					}
                );
			});
		}
    },
    async created() {
		//primeiro tenta recuperar as configurações gerais
		let response = await this.getSettings('/lib/panic/settings');
		this.general_settings = Object.assign({}, this.general_settings, response);

		//recupera as configurações segup
		response = await this.getSettings('/lib/panic/settings/segup');
		this.segup_settings = Object.assign({}, this.segup_settings, response);

		//recupera a lista de administradores
		this.admins = await this.getSettings('/lib/panic/admins');

		//Por fim recupera as configurações do admin e seta o selected admin
		response = await this.getSettings('/lib/panic/settings/admin');
		this.admin_settings = Object.assign({}, this.admin_settings, response);

		this.selectedAdmin = {
			id: this.admin_settings.panic_admin_id,
			username: this.admin_settings.panic_admin_email,
			admin_institution: null
		};
    },
};
</script>