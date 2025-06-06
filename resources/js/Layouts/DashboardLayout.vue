<template>
    <div class="bg-white">
        <div>
            <div class="relative z-50 lg:hidden" aria-modal="true">
                <div
                    class="fixed inset-0 bg-gray-900/80"
                    :class="{ hidden: !showSidebar }"
                ></div>

                <div
                    class="fixed inset-0 flex"
                    :class="{ hidden: !showSidebar }"
                >
                    <div class="relative mr-16 flex w-full max-w-xs flex-1">
                        <div
                            class="absolute left-full top-0 flex w-16 justify-center pt-5"
                        >
                            <button
                                type="button"
                                class="-m-2.5 p-2.5"
                                @click="showSidebar = false"
                            >
                                <span class="sr-only">Close sidebar</span>
                                <svg
                                    class="h-6 w-6 text-white"
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

                        <!-- Sidebar component, swap this element with another sidebar if you like -->
                        <div
                            class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10"
                        >
                            <div class="flex h-16 shrink-0 items-center">
                                <span class="text-lg font-bold text-white"
                                    >Админ панель</span
                                >
                            </div>
                            <nav class="flex flex-1 flex-col">
                                <ul
                                    role="list"
                                    class="flex flex-1 flex-col gap-y-7"
                                >
                                    <li>
                                        <ul role="list" class="-mx-2 space-y-1">
                                            <li
                                                v-for="item in $page.props.auth
                                                    .user.role == 'admin'
                                                    ? items
                                                    : items.filter(
                                                          (o) =>
                                                              ![
                                                                  'products.index',
                                                                  'comments.index',
                                                              ].includes(
                                                                  o.routeName
                                                              )
                                                      )"
                                                :key="item.id"
                                            >
                                                <a
                                                    :href="
                                                        route(item.routeName)
                                                    "
                                                    class="text-white group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                                    :class="{
                                                        'bg-gray-800':
                                                            route().current() ===
                                                            item.routeName,
                                                    }"
                                                >
                                                    <span
                                                        v-html="item.icon"
                                                    ></span>
                                                    {{ item.label }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li
                                        v-if="
                                            $page.props.auth.user.role ==
                                            'admin'
                                        "
                                    >
                                        <div
                                            class="text-xs font-semibold leading-6 text-gray-400"
                                        >
                                            Пользователи и группы
                                        </div>
                                        <ul
                                            role="list"
                                            class="-mx-2 mt-2 space-y-1"
                                        >
                                            <li
                                                v-for="(
                                                    downItem, index
                                                ) in downItems"
                                                :key="index"
                                            >
                                                <a
                                                    :href="
                                                        route(
                                                            downItem.routeName
                                                        )
                                                    "
                                                    class="text-gray-400 hover:text-white hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                                    :class="{
                                                        'bg-gray-800':
                                                            route().current() ===
                                                            downItem.routeName,
                                                    }"
                                                >
                                                    <span
                                                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-gray-700 bg-gray-800 text-[0.625rem] font-medium text-gray-400 group-hover:text-white"
                                                        >{{
                                                            downItem.icon
                                                        }}</span
                                                    >
                                                    <span class="truncate">{{
                                                        downItem.label
                                                    }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <!-- <li class="mt-auto">
                                        <a
                                            href="#"
                                            class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                                            :class="{
                                                'bg-gray-800':
                                                    route().current() ===
                                                    'admin.profile',
                                            }"
                                        >
                                            <svg
                                                class="h-6 w-6 shrink-0"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                ></path>
                                            </svg>
                                            Профиль
                                        </a>
                                    </li> -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div
                :class="[
                    'hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col',
                    { 'lg:hidden': !showSidebar },
                ]"
            >
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div
                    class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4"
                >
                    <div class="flex h-16 shrink-0 items-center">
                        <span class="text-lg font-bold text-white"
                            >Админ панель</span
                        >
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    <li
                                        v-for="item in $page.props.auth.user
                                            .role == 'admin'
                                            ? items
                                            : items.filter(
                                                  (o) =>
                                                      ![
                                                          'products.index',
                                                          'comments.index',
                                                      ].includes(o.routeName)
                                              )"
                                        :key="item.id"
                                    >
                                        <a
                                            :href="route(item.routeName)"
                                            class="text-white group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                            :class="{
                                                'bg-gray-800':
                                                    route().current() ===
                                                    item.routeName,
                                            }"
                                        >
                                            <span v-html="item.icon"></span>
                                            {{ item.label }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li v-if="$page.props.auth.user.role == 'admin'">
                                <div
                                    class="text-xs font-semibold leading-6 text-gray-400"
                                >
                                    Пользователи и группы
                                </div>
                                <ul role="list" class="-mx-2 mt-2 space-y-1">
                                    <li
                                        v-for="(downItem, index) in downItems"
                                        :key="index"
                                    >
                                        <a
                                            :href="route(downItem.routeName)"
                                            href="#"
                                            class="text-gray-400 hover:text-white hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                            :class="{
                                                'bg-gray-800':
                                                    route().current() ===
                                                    downItem.routeName,
                                            }"
                                        >
                                            <span
                                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-gray-700 bg-gray-800 text-[0.625rem] font-medium text-gray-400 group-hover:text-white"
                                                >{{ downItem.icon }}</span
                                            >
                                            <span class="truncate">{{
                                                downItem.label
                                            }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- <li class="mt-auto">
                                <a
                                    href="#"
                                    class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                                    :class="{
                                        'bg-gray-800':
                                            route().current() ===
                                            'admin.profile',
                                    }"
                                >
                                    <svg
                                        class="h-6 w-6 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                    </svg>
                                    Профиль
                                </a>
                            </li> -->
                        </ul>
                    </nav>
                </div>
            </div>

            <div :class="{ 'lg:pl-72': showSidebar }">
                <div
                    class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8"
                >
                    <button
                        type="button"
                        class="-m-2.5 p-2.5 text-gray-700"
                        @click="showSidebar = !showSidebar"
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
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            ></path>
                        </svg>
                    </button>

                    <!-- Separator -->
                    <div
                        class="h-6 w-px bg-gray-900/10 lg:hidden"
                        aria-hidden="true"
                    ></div>

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div class="flex flex-1"></div>
                        <!-- <form class="relative flex flex-1" action="#" method="GET">
              <label for="search-field" class="sr-only">Search</label>
              <svg
                class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                  clip-rule="evenodd"
                ></path>
              </svg>
              <input
                id="search-field"
                class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                placeholder="Search..."
                type="search"
                name="search"
              />
            </form> -->
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- <button
                type="button"
                class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
              >
                <span class="sr-only">View notifications</span>
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
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                  ></path>
                </svg>
              </button> -->

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
                                    <span
                                        class="hidden lg:flex lg:items-center"
                                    >
                                        <span
                                            class="ml-4 text-sm font-semibold leading-6 text-gray-900"
                                            aria-hidden="true"
                                            >{{
                                                $page.props.auth.user.name
                                            }}</span
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
                                    <!-- <a
                                        href="#"
                                        class="block px-3 py-1 text-sm leading-6 text-gray-900"
                                        role="menuitem"
                                        tabindex="-1"
                                        id="user-menu-item-0"
                                        >Профиль</a
                                    > -->

                                    <button
                                        type="buttom"
                                        class="block px-3 py-1 text-sm leading-6 text-gray-900"
                                        @click="logout"
                                    >
                                        Выйти
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <main
                    :class="
                        mainClass.length > 0
                            ? ''
                            : 'py-10 sm:px-8 px-3 bg-gray-100 min-h-[92vh]'
                    "
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        mainClass: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            items: [
                {
                    label: "Главная",
                    routeName: "dashboard",
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>`,
                },
                {
                    label: "Торговые точки",
                    routeName: "shops.index",
                    icon: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>`,
                },
                {
                    label: "Витрины",
                    routeName: "showcases.index",
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.242 5.992h12m-12 6.003H20.24m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 1 1 1.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 0 1 0 2.25H3.74m0-.002h.375a1.125 1.125 0 0 1 0 2.25H2.99" /></svg>`,
                },
                {
                    label: "Продукты",
                    routeName: "products.index",
                    icon: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>`,
                },
                {
                    label: "Комментарии",
                    routeName: "comments.index",
                    icon: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>`,
                },
            ],

            downItems: [
                {
                    label: "Пользователи",
                    routeName: "users.index",
                    icon: `П`,
                },
                {
                    label: "Менеджер",
                    routeName: "managers.index",
                    icon: `М`,
                },
                {
                    label: "Администраторы",
                    routeName: "admins.index",
                    icon: `А`,
                },
            ],
            showDropDown: false,
            showSidebar: true,
        };
    },
    methods: {
        hideDropDown() {
            setTimeout(() => {
                this.showDropDown = false;
            }, 300);
        },
        logout() {
            axios
                .post("/logout")
                .then((res) => {
                    window.location.href = "/";
                })
                .catch((err) => {});
        },
    },
};
</script>
