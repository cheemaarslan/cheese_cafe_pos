<template>
    <VueElementLoading spinner="bar-fade-scale" color="#b7161d" :active="props.isActive" :is-full-screen="false"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingContentComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>
