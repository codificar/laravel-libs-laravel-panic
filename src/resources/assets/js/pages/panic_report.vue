<template>
	<div class="tab-content">
		<div class="col-lg-12">
			<div class="card card-outline-info">
				<div class="card-header">
                    <h4 class="m-b-0 text-white">{{ trans('panic.filters') }}</h4>
                </div>

				<div class="card-block">
					<div class="row">
						<!-- Request ID -->
						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								<label for="request_id" class="control-label">{{ trans('panic.request_id') }}</label>                                  
								<input 
									class="form-control" 
									maxlenght="255" 
									auto-focus 
									type="number"
									:placeholder="trans('panic.request_id')"
									v-model="filter.request_id" 
								/>
							</div>
						</div>

						<!-- User -->
						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								<label for="user" class="control-label">
									{{ trans('panic.panic_user') }}
								</label>                                  
								<autocomplete
									source="/admin/searchreferral?type=0&name="
									method="get"
									input-class="form-control"
									:placeholder="trans('panic.panic_user')"
									results-property="referrals"
									:results-display="renderAutocompleteResults"
									@selected="selectUser"
									@clear="clearUser"
								/>
							</div>
						</div>

						<!-- Provider -->
						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								<label for="provider_id" class="control-label">
									{{ trans('panic.panic_provider') }}
								</label>                                  
								<autocomplete
									source="/admin/searchreferral?type=1&name="
									method="get"
									input-class="form-control"
									name="institution_id"
									:placeholder="trans('panic.panic_provider')"
									results-property="referrals"
									:results-display="renderAutocompleteResults"
									@selected="selectProvider"
									@clear="clearProvider"
								/>
							</div>
						</div>

						<!-- Action -->
                        <div class="form-group">
                            <div class="pull-right">
                                <div class="col-md-6 col-md-offset-4">
                                    <button @click="fetch" type="button" class="btn btn-success">
                                        <i class="fa fa-search"></i> 
                                        {{ trans('panic.filter') }}
                                    </button>               
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-content">
			 <div class="col-lg-12">
                <div class="card card-outline-info">            
                    <div class="card-block">
						<pagination 
                            :data="panic" 
                            @pagination-change-page="fetch" 
                        >
                        </pagination>

						<table class="table">
							<tr>
                                <th>{{ trans('panic.panic_id') }}</th>
								<th>{{ trans('panic.request_id') }}</th>
                                <th>{{ trans('panic.panic_user') }}</th>
                                <th>{{ trans('panic.panic_provider') }}</th>
                                <th>{{ trans('panic.history') }}</th>
                                <th>{{ trans('panic.data') }}</th>
                            </tr>
							<tr 
                                v-for="(item, index) in panic.data"
                                :key="index"
                            >
								<td>{{ item.id }}</td>
								<td>{{ item.request_id }}</td>
								<td>{{ item.user_name }}</td>
								<td>{{ item.provider_name }}</td>
								<td>{{ item.history }}</td>
								<td>{{ item.date }}</td>
							</tr>
						</table>

						<pagination 
                            :data="panic" 
                            @pagination-change-page="fetch" 
                        >
                        </pagination>
					</div>
				</div>
			 </div>
		</div>
	</div>
</template>

<script>
import autocomplete from 'vuejs-auto-complete';
import axios from 'axios';

export default {
	components: {
        autocomplete
    },
	data() {
        return {
            filter: {
                request_id: '',
                user_id: '',
                provider_id: ''
            },
            panic: {}
        }
    },
 	methods: {
		fetch(page = 1) {
            axios.get('/lib/panic/view/fetch', {
                params: {
                    page: isNaN(page) ? 1 : page,
                    filter: this.filter
                }
            })
            .then(res => {
                const {data} = res;
				console.log(data)
                
                if (data.success) {
                    this.panic = data.panic.panic;
                }
            });

        },
		renderAutocompleteResults(result) {
            return `${result.first_name} ${result.last_name}`;
        },
		selectUser(result) {
            const { selectedObject } = result;
			this.filter.user_id = selectedObject.id;
        },
        clearUser() {
            this.filter.user_id = '';
        },
        selectProvider(result) {
            const { selectedObject } = result;
			this.filter.provider_id = selectedObject.id;

        },
        clearProvider() {
            this.filter.provider_id = '';
        }
	},
	mounted() {
        this.fetch();
	}
}
</script>