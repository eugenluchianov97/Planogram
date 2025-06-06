<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    shops: Array,
});

const showDropDown = ref(false);

function hideDropDown() {
    setTimeout(() => {
        showDropDown.value = false;
    }, 300);
}
function logout() {
    axios
        .post("/logout")
        .then((res) => {
            window.location.href = "/";
        })
        .catch((err) => {});
}
</script>

<template>
    <div
        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 bg-white px-4 sm:gap-x-6 sm:px-6 lg:px-8 m-3 shadow-lg rounded-md"
    >
        <button
            type="button"
            class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
            @click="showSidebar = true"
        >
            <span class="sr-only">Open sidebar</span>
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
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                ></path>
            </svg>
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
            <div class="flex flex-1"></div>

            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <!-- Separator -->
                <div
                    class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10"
                    aria-hidden="true"
                ></div>

                <!-- Profile dropdown -->
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
            </div>
        </div>
    </div>
    <main class="">
        <div class="py-8 min-h-screen">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="font-semibold text-xl mb-6">
                    Доступные торговые точки
                </h1>
                <ul
                    role="list"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <li
                        v-for="shop in shops"
                        :key="shop.id"
                        class="overflow-hidden rounded-xl border border-gray-200"
                    >
                        <div
                            class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-200 p-6"
                        >
                            <div
                                class="text-sm font-medium leading-6 text-gray-900"
                            >
                                {{ shop.name }}
                            </div>
                            <div class="relative ml-auto">
                                <Link
                                    :href="route('planogram', shop.id)"
                                    class="text-sm flex items-center gap-x-1 justify-center px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white hover:text-gray-50"
                                    id="options-menu-0-button"
                                >
                                    <span>Перейти</span>
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
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                        />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                        <dl
                            class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6"
                        >
                            <div class="flex justify-between gap-x-4 py-3">
                                <dt class="text-gray-500">Адрес</dt>
                                <dd class="text-gray-700">
                                    <time datetime="2022-12-13">{{
                                        shop.address
                                    }}</time>
                                </dd>
                            </div>
                            <div class="flex justify-between gap-x-4 py-3">
                                <dt class="text-gray-500">Добавлен</dt>
                                <dd class="flex items-start gap-x-2">
                                    <div class="font-medium text-gray-900">
                                        {{
                                            new Date(
                                                shop.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
    </main>
</template>
