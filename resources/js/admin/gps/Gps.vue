<template>
    <div class="page_wrap_vue">
        <v-tabs color="primary" v-model="active">

            <v-tab key="gps" href="#gps" ripple>
                GPS
            </v-tab>
            <v-tab key="manage-groups" href="#manage-groups" ripple>
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
            <v-tab-item value="manage-groups">
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
    import GpsGroupLists from './components/GpsGroupLists.vue';
    import GpsUpload from './components/GpsUpload.vue';
    import GpsLists from './components/GpsLists.vue';
    export default {
        components: {
            GpsUpload,
            GpsGroupLists,
            GpsLists
        },
        mounted() {
            console.log('pages.GpsManager.vue');

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'GPS',name:''}
            ]);
        },
        data() {
            return {
                active: 'files'
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