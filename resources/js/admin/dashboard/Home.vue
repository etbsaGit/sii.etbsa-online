<template>
    <div class="page_wrap_vue pa-3">
   
    </div>
</template>

<script>
    export default {
        mounted() {

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Dashboard',name:''}
            ]);
        }
    }
</script>