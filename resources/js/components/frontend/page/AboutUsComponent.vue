<template>
    <div>
        <WhyChooseUsComponent />
        <TestimonialComponent />
    </div>
</template>

<script>
import WhyChooseUsComponent from "../home/WhyChooseUsComponent.vue";
import TestimonialComponent from "../home/TestimonialComponent.vue";

export default {
    name: "AboutUsComponent",
    components: {
        WhyChooseUsComponent,
        TestimonialComponent,
    },
};
</script>
