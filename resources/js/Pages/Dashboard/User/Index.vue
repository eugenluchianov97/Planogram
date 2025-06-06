<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import CRUD from "@/Components/CRUD.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ users: Object });
</script>

<template>
    <Head title="Пользователи" />
    <DashboardLayout>
        <CRUD
            title="Пользователи"
            meta="Пользователь"
            name="users"
            :items="props.users"
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
