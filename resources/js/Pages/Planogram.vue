<script setup>
import axios from "axios";
import Swal from "sweetalert2/dist/sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    shop: Object,
    showcases: Array,
    shops: Array,
});

const showAddModal = ref(false);
const isEdit = ref(false);
const isDublicate = ref(false);
const deleteModal = ref(false);
const selectedShowcase = ref(null);
const shop_id = ref(null);
const loading = ref(false);
const showDropDown = ref(false);
const modalForm = ref(null);

const form = ref({
    name: "",
    description: "",
    width: "",
    height: "",
    shelf_depth: "",
    shelves: "",
    distance_between_shelves: "",
    comment: "",
});

const removeItem = () => {
    axios
        .delete(route("showcase.remove", selectedShowcase.value.id))
        .then((res) => {
            Swal.fire({
                title: `Витрина успешно удалён`,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
            closeAddModal();

            const index = props.shop.showcases.indexOf(selectedShowcase.value);
            if (index > -1) {
                props.shop.showcases.splice(index, 1);
            }
        })
        .catch((err) => {
            Swal.fire({
                title: `Ошибка при удалении витрины`,
                timer: 4000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "error",
                toast: true,
                position: "top-end",
            });
        })
        .finally(() => {
            loading.value = false;
        });
};

const closeAddModal = () => {
    showAddModal.value = false;
    isEdit.value = false;
    deleteModal.value = false;
    form.value = {
        name: "",
        description: "",
        width: "",
        height: "",
        shelf_depth: "",
        shelves: "",
        distance_between_shelves: "",
        comment: "",
    };
};

const editForm = () => {
    form.value = JSON.parse(JSON.stringify(selectedShowcase.value));
    isEdit.value = true;
    isDublicate.value = false;
    showAddModal.value = true;
};

const dublicateForm = () => {
    form.value = JSON.parse(JSON.stringify(selectedShowcase.value));
    showAddModal.value = true;
    isEdit.value = false;
    isDublicate.value = true;
};

const saveForm = () => {
    loading.value = true;

    if (isEdit.value) {
        axios
            .put(route("showcase.update", form.value.id), form.value)
            .then((res) => {
                Swal.fire({
                    title: `Витрина успешно обновлен`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });
                closeAddModal();
            })
            .catch((err) => {
                Swal.fire({
                    title: `Ошибка при обновлении витрины`,
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "error",
                    toast: true,
                    position: "top-end",
                });
            })
            .finally(() => {
                loading.value = false;
            });
    } else if (isDublicate.value) {
        axios
            .post(
                route("showcase.duplicate", [shop_id.value, form.value.id]),
                form.value
            )
            .then((res) => {
                Swal.fire({
                    title: `Витрина успешно дублирован`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });
                closeAddModal();
            })
            .catch((err) => {
                Swal.fire({
                    title: `Ошибка при дублирование витрины`,
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "error",
                    toast: true,
                    position: "top-end",
                });
            })
            .finally(() => {
                loading.value = false;
            });
    } else {
        axios
            .post(route("showcase.store", props.shop.id), form.value)
            .then((res) => {
                Swal.fire({
                    title: `Витрина успешно добавлен`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });
                router.visit(route("planogram", props.shop.id), {
                    replace: true,
                });
                closeAddModal();
            })
            .catch((err) => {
                Swal.fire({
                    title: `Ошибка при добавлении витрины`,
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "error",
                    toast: true,
                    position: "top-end",
                });
            })
            .finally(() => {
                loading.value = false;
            });
    }
};

function hideDropDown() {
    setTimeout(() => {
        showDropDown.value = false;
    }, 300);
}
</script>

<template>
    <section class="flex items-start">
        <aside class="w-[256px] rounded-lg shadow-xl m-3 min-h-[95vh] p-2">
            <Link
                :href="route('home')"
                class="text-sm flex items-center gap-x-1 justify-center px-2.5 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-gray-800 font-medium"
                id="options-menu-0-button"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                    />
                </svg>

                <span>Главная страница</span>
            </Link>
            <ul class="mt-2">
                <li class="flex gap-x-1 items-center cursor-pointer">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m19.5 8.25-7.5 7.5-7.5-7.5"
                        />
                    </svg>
                    <span>{{ shop.name }}</span>
                </li>
                <li v-for="showcase in shop.showcases" :key="showcase.id">
                    <ul class="ml-3">
                        <li
                            class="flex gap-x-1 items-center cursor-pointer"
                            @click="selectedShowcase = showcase"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 12h14"
                                />
                            </svg>

                            <span> {{ showcase.name }}</span>
                        </li>
                        <li
                            v-for="shelves in showcase.shelves"
                            :key="shelves.id"
                        >
                            <ul class="ml-9">
                                <li class="cursor-pointer">
                                    #{{ shelves.position }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>
        <main class="w-full">
            <nav
                class="w-full bg-white shadow-lg rounded-md px-3 py-2 flex items-center justify-between"
            >
                <ul class="flex items-center gap-x-2 font-medium h-[38px]">
                    <template v-if="$page.props.auth.user.role != 'user'">
                        <li>
                            <button
                                type="button"
                                @click="showAddModal = true"
                                class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-gray-100 hover:bg-gray-200 transition-all duration-500"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 4.5v15m7.5-7.5h-15"
                                    />
                                </svg>
                                <span> Добавить </span>
                            </button>
                        </li>
                        <template v-if="selectedShowcase != null">
                            <li>
                                <button
                                    type="button"
                                    @click="editForm"
                                    class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-blue-500 hover:bg-blue-600 transition-all duration-500 text-white"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-4"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                        />
                                    </svg>

                                    <span> Редактировать </span>
                                </button>
                            </li>

                            <li>
                                <button
                                    type="button"
                                    @click="dublicateForm"
                                    class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-indigo-500 hover:bg-indigo-600 transition-all duration-500 text-white"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-4"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"
                                        />
                                    </svg>
                                    <span> Дублировать </span>
                                </button>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    @click="deleteModal = true"
                                    class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-red-500 hover:bg-red-700 transition-all duration-500 text-white"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-4"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                        />
                                    </svg>

                                    <span> Удалить </span>
                                </button>
                            </li>
                        </template>
                    </template>
                </ul>
                <div class="relative">
                    <button
                        @click="showDropDown = !showDropDown"
                        @blur="hideDropDown"
                        type="button"
                        class="-m-1.5 flex items-center p-1.5"
                        id="user-menu-button"
                    >
                        <span class="sr-only">Open user menu</span>
                        <svg
                            v-if="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>

                        <img
                            v-else
                            class="h-8 w-8 rounded-full bg-gray-50"
                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
                            alt=""
                        />
                        <span class="hidden lg:flex lg:items-center">
                            <span
                                class="ml-4 text-sm font-semibold leading-6 text-gray-900"
                                aria-hidden="true"
                                >{{ $page.props.auth.user.name }}</span
                            >
                            <svg
                                class="ml-2 h-5 w-5 text-gray-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        </span>
                    </button>

                    <div
                        v-show="showDropDown"
                        class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                    >
                        <!-- :class="{ 'bg-gray-50': activeIndex === 0 }" -->
                        <!-- :href="route('admin.profile')" -->
                        <Link
                            href="#"
                            class="block px-3 py-1 text-sm leading-6 text-gray-900"
                            role="menuitem"
                            tabindex="-1"
                            id="user-menu-item-0"
                            >Профиль</Link
                        >
                        <!-- :class="{ 'bg-gray-50': activeIndex === 1 }" -->
                        <Link
                            :href="route('logout')"
                            method="POST"
                            as="button"
                            class="block px-3 py-1 text-sm leading-6 text-gray-900"
                            role="menuitem"
                            tabindex="-1"
                            id="user-menu-item-1"
                            >Выйти
                        </Link>
                    </div>
                </div>
            </nav>

            <div class="py-4">
                <template v-if="selectedShowcase != null">
                    <h2 class="text-xl font-semibold">
                        {{ selectedShowcase.name }}
                    </h2>
                    <div>
                        <ul class="">
                            <li
                                v-for="shelves in selectedShowcase.shelves"
                                :key="shelves.id"
                                class="py-3 flex items-end gap-x-3 border-b-8 border-b-gray-500"
                            >
                                <span>#{{ shelves.position }}</span>
                                <div class="flex">
                                    <img
                                        src="https://globus-online.kg/upload/iblock/71d/n96nhbaxx5h99stjvwxnkeps4xsq50vn/37cafae0-e9cc-11eb-80c6-005056845eda.png"
                                        alt=""
                                        class="w-[32px]"
                                    />
                                    <img
                                        src="https://globus-online.kg/upload/iblock/71d/n96nhbaxx5h99stjvwxnkeps4xsq50vn/37cafae0-e9cc-11eb-80c6-005056845eda.png"
                                        alt=""
                                        class="w-[32px]"
                                    />
                                    <img
                                        src="https://globus-online.kg/upload/iblock/71d/n96nhbaxx5h99stjvwxnkeps4xsq50vn/37cafae0-e9cc-11eb-80c6-005056845eda.png"
                                        alt=""
                                        class="w-[32px]"
                                    />
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div
                        class="mt-7"
                        v-for="shelves in selectedShowcase.shelves"
                        :key="shelves.id"
                    >
                        <h2 class="text-lg font-semibold mb-2">
                            Полка #{{ shelves.position }}
                        </h2>
                        <div
                            class="relative overflow-x-auto shadow-md sm:rounded-lg"
                        >
                            <table
                                class="w-full text-sm text-left rtl:text-right text-gray-500"
                            >
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-3">№</th>
                                        <th scope="col" class="px-6 py-3">
                                            Название
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Цена
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white border-b">
                                        <th
                                            scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                        >
                                            1
                                        </th>
                                        <td class="px-6 py-4">
                                            Apple MacBook Pro 17"
                                        </td>
                                        <td class="px-6 py-4">1000</td>
                                    </tr>
                                    <tr class="bg-white border-b">
                                        <th
                                            scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                        >
                                            2
                                        </th>
                                        <td class="px-6 py-4">
                                            Microsoft Surface Pro
                                        </td>
                                        <td class="px-6 py-4">300</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <th
                                            scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                        >
                                            3
                                        </th>
                                        <td class="px-6 py-4">Magic Mouse 2</td>
                                        <td class="px-6 py-4">400</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </main>
    </section>

    <div
        v-if="showAddModal"
        class="fixed inset-0 bg-gray-500/75 transition-opacity"
    >
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div
                ref="modalForm"
                class="modalForm active pointer-events-none fixed inset-y-0 flex max-w-full pl-10 sm:pl-16 right-0"
            >
                <div class="pointer-events-auto w-screen max-w-2xl">
                    <form
                        @submit.prevent="saveForm"
                        class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl z-20"
                    >
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="bg-gray-50 px-4 py-6 sm:px-6">
                                <div
                                    class="flex items-start justify-between space-x-3"
                                >
                                    <div class="space-y-1">
                                        <h2
                                            class="text-base font-semibold leading-6 text-gray-900"
                                            id="slide-over-title"
                                        >
                                            {{
                                                isEdit
                                                    ? "Редактировать витрину"
                                                    : "Добавить новую витрину"
                                            }}
                                        </h2>
                                        <p class="text-sm text-gray-500">
                                            Начните с заполнения информацию
                                            ниже, чтобы создать витрину.
                                        </p>
                                    </div>
                                    <div class="flex h-7 items-center">
                                        <button
                                            type="button"
                                            @click="closeAddModal"
                                            class="relative text-gray-400 hover:text-gray-500"
                                        >
                                            <span
                                                class="absolute -inset-2.5"
                                            ></span>
                                            <span class="sr-only"
                                                >Close panel</span
                                            >
                                            <svg
                                                class="h-6 w-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider container -->
                            <div
                                class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0"
                            >
                                <!-- Project name -->
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="name"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Название</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            placeholder="Введите название"
                                            required
                                            v-model="form.name"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="description"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Описание витрини</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <textarea
                                            id="description"
                                            name="description"
                                            rows="3"
                                            placeholder="Введите описание витрини"
                                            required
                                            v-model="form.description"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        ></textarea>
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="width"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Ширина</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="number"
                                            name="width"
                                            id="width"
                                            min="1"
                                            placeholder="Введите ширину (см)"
                                            required
                                            v-model="form.width"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="height"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Высота</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="number"
                                            name="height"
                                            id="height"
                                            min="1"
                                            placeholder="Введите высоту (см)"
                                            required
                                            v-model="form.height"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="shelf_depth"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Глубина полок</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="number"
                                            name="shelf_depth"
                                            id="shelf_depth"
                                            min="1"
                                            placeholder="Введите глубина полок (см)"
                                            required
                                            v-model="form.shelf_depth"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="distance_between_shelves"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Расстояние между полками
                                        </label>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="number"
                                            name="distance_between_shelves"
                                            id="distance_between_shelves"
                                            min="1"
                                            placeholder="Введите расстояние между полками (см)"
                                            required
                                            v-model="
                                                form.distance_between_shelves
                                            "
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="shelves_count"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Количество полок</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input
                                            type="number"
                                            name="shelves_count"
                                            id="shelves_count"
                                            min="1"
                                            max="10"
                                            placeholder="Введите количество полок"
                                            required
                                            v-model="form.shelves_count"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="comment"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Комментарий</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <textarea
                                            id="comment"
                                            name="comment"
                                            rows="3"
                                            placeholder="Введите комментарий"
                                            v-model="form.comment"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        ></textarea>
                                    </div>
                                </div>
                                <div
                                    v-if="isDublicate"
                                    class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                >
                                    <div>
                                        <label
                                            for="shop_id"
                                            class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                            >Выберите торговую точку</label
                                        >
                                    </div>
                                    <div class="sm:col-span-2">
                                        <select
                                            type="number"
                                            name="shop_id"
                                            id="shop_id"
                                            min="1"
                                            placeholder="Введите количество полок"
                                            required
                                            v-model="shop_id"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        >
                                            <option
                                                v-for="sh in shops"
                                                :key="sh.id"
                                                :value="sh.id"
                                            >
                                                {{ sh.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div
                            class="flex-shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6"
                        >
                            <div class="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="closeAddModal"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                >
                                    Закрыть
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                >
                                    {{ isEdit ? "Обновить" : "Сохранить" }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50"
        v-if="deleteModal"
    >
        <div
            class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
        >
            <div
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
            >
                <div>
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                        >
                            <svg
                                class="h-6 w-6 text-red-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3
                            class="text-base font-semibold leading-6 text-gray-900"
                            id="modal-title"
                        >
                            Вы уверены?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Вы уверены, что хотите удалить витрину Все
                                следующие объекты и связанные с ними элементы
                                будут удалены.
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3"
                >
                    <button
                        type="button"
                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2"
                        @click="removeItem"
                    >
                        Удалить
                    </button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                        @click="deleteModal = false"
                    >
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
