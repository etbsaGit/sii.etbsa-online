<template>
    <div class="page_wrap_vue">
        <v-tabs color="primary" v-model="active">

            <v-tab key="gps" href="#gps" ripple>
                GPS
            </v-tab>
            <v-tab key="manage-gps-groups" href="#manage-gps-groups" ripple>
                Administra Grupos GPS
            </v-tab>
            <v-tab key="upload" href="#upload" ripple>
                Upload
            </v-tab>

            <v-tab-item value="gps">
                <v-card flat>
                    <v-card-text>
                        <gps-lists></gps-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item value="manage-gps-groups">
                <v-card flat>
                    <v-card-text>
                        <gps-group-lists></gps-group-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item value="upload">
                <v-card flat>
                    <v-card-text>
                        <gps-upload></gps-upload>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
    import GpsGroupLists from '@admin/gps/components/GpsGroupLists.vue';
    import GpsUpload from '@admin/gps/components/GpsUpload.vue';
    import GpsLists from '@admin/gps/components/GpsLists.vue';
    export default {
        components: {
            GpsUpload,
            GpsGroupLists,
            GpsLists
        },
        mounted() {
            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'GPS',name:''}
            ]);
        },
        data() {
            return {
                active: 'gps'
            }
        },
        watch: {
            active(v) {
                console.log('active tab: ' + v);
            }
        },
        methods: {
        }
    }
</script>

<style scoped="">
    .finder_wrap {
        padding: 0 20px;
    }
</style>