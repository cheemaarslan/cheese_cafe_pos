<template>
    <VueElementLoading v-if="props.isActive" spinner="bar-fade-scale" color="#b7161d" :active="props.isActive" :is-full-screen="true"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>
