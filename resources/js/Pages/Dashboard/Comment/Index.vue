<script setup>
import CRUD from "@/Components/CRUD.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ comments: Object });

</script>

<template>
    <Head title="Витрины"></Head>
    <DashboardLayout>
        <CRUD
            title="Коментарии"
            meta="Коментария"
            name="comments"
            :items="props.comments"
            :show-add-button="false"
            :show-custom-add-button="false"
            :show-custom-edit-button="false"
            :fields="[
                { label: 'ID', value: 'id' },
                { label: 'Пользователь', value: 'user.name' },
                { label: 'Торговая Точка', value: 'showcase.shop.name' },
                { label: 'Витрина', value: 'showcase.name' },
                { label: 'Коментария', value: 'comment' },
                { label: 'Файл', value: 'document', type: 'file' },
                { label: 'Дата', value: 'created_at' },
            ]"
            :actions="{
                type: Object,
                default: {
                    removeButton: false,
                    editButton: false,
                },
            }"
            :links="{
                'showcase.shop.name': {
                    routeName: 'shops.show',
                    routeParam: 'showcase.shop.id',
                },
            }"
        />
    </DashboardLayout>
</template>
