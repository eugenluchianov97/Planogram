<template>
    <template v-if="name in errors">
        <ul class="my-2">
            <li
                class="text-red-500 font-semibold text-sm"
                :class="classList"
                v-for="(value, index) in errors[name]"
                :key="index"
            >
                {{ value }}
            </li>
        </ul>
    </template>
</template>

<script>
export default {
    props: {
        name: { type: String },
        errors: { type: Object, default: {} },
        classList: { type: String, default: "" },
    },
    data() {
        return {};
    },
    mounted() {},
};
</script>
