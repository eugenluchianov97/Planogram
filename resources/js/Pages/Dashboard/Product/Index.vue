<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import CRUD from "@/Components/CRUD.vue";
import CRUD2 from "@/Pages/Dashboard/Product/Components/CRUD2.vue";
import {Head, router} from "@inertiajs/vue3";
import Inertia from "vuedraggable";

const props = defineProps({products: Object});
</script>

<template>
  <Head title="Продукты"/>
  <DashboardLayout>
    <CRUD2
        title="Продукты"
        meta="Продукт"
        name="products"
        :items="props.products"
    />

<!--    <CRUD-->
<!--        title="Продукты"-->
<!--        meta="Продукт"-->
<!--        name="products"-->
<!--        :filter="true"-->
<!--        :items="props.products"-->
<!--        :show-add-button="false"-->
<!--        :show-custom-add-button="false"-->
<!--        :fields="[-->
<!--                { label: 'Название', value: 'Name' },-->
<!--                { label: 'Картинка',value:'Code',type:'img',contentType:'image' },-->
<!--                { label: 'Цена', value: 'Price'},-->
<!--                { label: 'Ширина', value: 'BoxWidth' },-->
<!--                { label: 'Высота', value: 'BoxHeight' },-->
<!--                { label: 'Глубина', value: 'BoxDepth' },-->
<!--                { label: 'Поставщик', value: 'Producer' },-->
<!--                { label: 'Сканер код', value: 'barcodes.id' },-->
<!--                { label: 'Shop id', value: 'shop.name' },-->
<!--            ]"-->
<!--        :actions="{-->
<!--               removeButton: false,-->
<!--               editButton: true,-->
<!--            }"-->
<!--        :fillable="[-->
<!--                { label: 'Картинка', value: 'Code',type:'image' },-->
<!--                { label: 'Ширина', value: 'BoxWidth' },-->
<!--                { label: 'Высота', value: 'BoxHeight' },-->
<!--                { label: 'Глубина', value: 'BoxDepth' },-->
<!--            ]"-->
<!--    />-->
  </DashboardLayout>
</template>
