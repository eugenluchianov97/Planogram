<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import CRUD from "@/Components/CRUD.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ admins: Object });
</script>

<template>
    <Head title="Администраторы" />
    <DashboardLayout>
        <CRUD
            title="Администраторы"
            meta=" Администратор"
            name="admins"
            :items="props.admins"
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
