<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import CRUD from "@/Components/CRUD.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ managers: Object });
</script>

<template>
    <Head title="Менеджер" />
    <DashboardLayout>
        <CRUD
            title="Менеджер"
            meta="Менеджер"
            name="managers"
            :items="props.managers"
            :show-add-button="false"
            :show-custom-add-button="true"
            :show-custom-edit-button="true"
            :fields="[
                { label: 'Имя', value: 'name' },
                { label: 'Логин', value: 'login' },
            ]"
        />
    </DashboardLayout>
</template>
