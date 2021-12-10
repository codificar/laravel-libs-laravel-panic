<script>
    export default {
        props: [],
        components: {
        },
        data() {
            return {
                panic_settings: {
                    panic_button_enabled_user: false,
                    panic_button_enabled_provider: false
                },
            };
        },
        methods: {
        },
        async mounted() {

        },
    };
</script>
<template>
    <div>
        <div class="tab-content">
            <div class="card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">
                        {{ trans("setting.panic_config") }}
                    </h4>
                </div>
                <div class="card-block">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    {{ trans("setting.panic_general_config") }}
                                </h3>
                                <hr />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input
                                        class="form-check-input checkbox-style"
                                        type="checkbox"
                                        id="panic_button_enabled_user"
                                        v-model="panic_settings.panic_button_enabled_user"
                                    />
                                    <label class="form-check-label" for="panic_button_enabled_user">{{
                                        trans("setting.panic_button_enabled_user")
                                    }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input
                                        class="form-check-input checkbox-style"
                                        type="checkbox"
                                        id="panic_button_enabled_provider"
                                        v-model="panic_settings.panic_button_enabled_provider"
                                    />
                                    <label class="form-check-label" for="panic_button_enabled_provider">{{
                                        trans("setting.panic_button_enabled_provider")
                                    }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>