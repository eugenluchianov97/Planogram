<script setup>
import CRUD from "@/Components/CRUD.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({ showcases: Object });
</script>

<template>
    <Head title="Витрины"></Head>
    <DashboardLayout>
        <CRUD
            title="Витрины"
            meta="Витрина"
            name="showcases"
            :items="props.showcases"
            :show-add-button="false"
            :show-custom-add-button="false"
            :show-custom-edit-button="false"
            :actions="{
                removeButton: true,
                editButton: false,
            }"
            :fields="[
                { label: 'ID', value: 'id' },
                { label: 'Название', value: 'name' },
                { label: 'Торговая точка', value: 'shop.name' },
                { label: 'Ширина', value: 'width' },
                { label: 'Высота', value: 'height' },
                { label: 'Глубина полок', value: 'shelf_depth' },
                { label: 'Количество полок', value: 'shelves_count' },
                { label: 'Добавлен', value: 'created_at' },
            ]"
            :links="{
                'shop.name': {
                    routeName: 'shops.show',
                    routeParam: 'shop.id',
                },
            }"
        />
    </DashboardLayout>
</template>
