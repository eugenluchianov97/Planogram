<script setup>
import CRUD from "@/Components/CRUD.vue";
import CRUD2 from "@/Pages/Dashboard/Product/Components/CRUD2.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ shops: Object });
</script>

<template>
    <Head title="Торговые точки"></Head>
    <DashboardLayout>
        <CRUD
            title="Торговые точки"
            meta="Торговая точка"
            name="shops"
            :items="props.shops"
            :show-add-button="true"
            :show-custom-add-button="false"
            :show-custom-edit-button="false"
            :show_update="true"
            :fields="[
                { label: 'ID', value: 'id' },
                { label: 'Название', value: 'name' },
                { label: 'Адрес', value: 'address' },
                { label: 'Добавлен', value: 'created_at' },
            ]"
            :fillable="[
                { label: 'Название', value: 'name' },
                { label: 'Адрес', value: 'address' },

            ]"
            :links="{
                name: {
                    routeName: 'shops.show',
                    routeParam: 'id',
                },
            }"
        />
    </DashboardLayout>
</template>
