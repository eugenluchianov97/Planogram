<script setup>
import {onMounted, ref, watch} from "vue";
import VPagination from "@hennge/vue3-pagination";
import { router } from "@inertiajs/vue3";
import "@hennge/vue3-pagination/dist/vue3-pagination.css";
import "sweetalert2/dist/sweetalert2.min.css";
import Swal from "sweetalert2/dist/sweetalert2";
import IError from "./IError.vue";

import Image from "@/Components/Image.vue";

const props = defineProps({
    items: Object,
    title: String,
    meta: String,
    name: String,
    showAddButton: {
        type: Boolean,
        default: true,
    },
    showCustomAddButton: {
        type: Boolean,
        default: false,
    },
    showCustomEditButton: {
        type: Boolean,
        default: false,
    },
    contentType: {
        type: String,
        default: "text",
    },
    actions: {
        type: Object,
        default: {
            removeButton: true,
            editButton: true,
        },
    },
    fields: {
        type: Array,
        default: [{ label: "Название", value: "name" }],
    },
    filterable: {
        type: Boolean,
        default: false,
    },
    fillable: {
        type: Array,
        default: [{ label: "Название", value: "name" }],
    },
    links: {
        type: Object,
        default: {},
    },
    show_update: {
        type: Boolean,
        default: false,
    },
    filter: {
      type: Boolean,
      default: false,
      required:false
    }
});

let currentPage = props.items.current_page;
let lastPage = props.items.last_page;



let showModal = ref(false);
let deleteModal = ref(false);

let item = ref({});
let loading = ref(false);

let errors = ref({});

let q = ref(route().params.q)


// const search = () => {
//   emits("changePage", 1);
//   router.get(route(`${props.name}.index`), { q: q.value }, {
//     preserveState: true,
//     replace: true,
//     preserveScroll: true,
//   });
// }

const updateHandler = (page) => {

    if (props.filterable) {
        emits("changePage", page);
    } else {
        let data = {
           page:page
        }

        if(props.filter){
          data.q = q.value;
        }

        router.get(route(`${props.name}.index`, data,{
          preserveState: true,
          replace: true,
          preserveScroll: true,
        }));
    }
};

const emits = defineEmits(["changePage"]);

const editItem = (data) => {

    item.value = data;
    showModal.value = true;
};

const addItem = () => {
    item.value = {
        name: "",
        image: "",
    };
    showModal.value = true;
};

const closeModal = () => {
    item.value = {
        name: "",
        image: "",
    };
    showModal.value = false;
    deleteModal.value = false;
};



const submit = () => {
    loading.value = true;
    console.log(item)



    if ("id" in item.value) {
        let form_data = new FormData();

        for( let k in item.value) {
          form_data.append(k,item.value[k]);
        }
        form_data.append('_method', 'PUT')

        let url = route(`${props.name}.update`, item.value.id);

        let config = {headers:{'Content-Type': 'multipart/form-data'}}
        axios.post(url, form_data,config).then((response) => {
              console.log(response);
                closeModal();
                router.visit("", { replace: true });
                Swal.fire({
                    title: `${props.meta} успешно обновлен`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });

            })
            .catch((err) => {
                errors.value = err.response.data.errors;
            })
            .finally(() => {
                loading.value = false;
            });
    } else {
        axios
            .post(route(`${props.name}.store`), item.value,)
            .then((response) => {
                closeModal();
                router.visit("", { replace: true });
                Swal.fire({
                    title: `${props.meta} успешно добавлен`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });
            })
            .catch((err) => {
                errors.value = err.response.data.errors;
            })
            .finally(() => {
                loading.value = false;
            });
    }
};

const removeItem = (item) => {
    axios.delete(route(`${props.name}.destroy`, item.id)).then((response) => {
        router.visit("", { replace: true });

        Swal.fire({
            title: `${props.meta} успешно удален`,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            icon: "success",
            toast: true,
            position: "top-end",
        });
    });
};

const showDeleteModal = (data) => {
    closeModal();
    item.value = data;
    deleteModal.value = true;
};




const uploadImage2 = (event) => {


  if (event.target.files && event.target.files[0]) {
    var reader = new FileReader();
    reader.onload = (e) => {
      item.value.src = e.target.result;
      item.value.image = event.target.files[0];
    };
    reader.readAsDataURL(event.target.files[0]);
  }
};

const uploadImage = (event) => {
  var input = event.target;
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = (e) => {
      item.value.image = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
};

const getTitle = (item, field) => {
    const keys = field.split(".");
    console.log(keys);

    keys.forEach((key) => {
        if (item != null) {
            item = item[key];
        }
    });
    return item;
};

function updateProducts(item) {
    axios
        .post(route(`${props.name}.store.update`, item.c_id))
        .then((response) => {
            Swal.fire({
                title: response.data.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
            closeModal();
            router.visit("", { replace: true });
        })
        .catch((err) => {
            Swal.fire({
                title: `Ошибка при обновлении товаров`,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
        })
        .finally(() => {
            loading.value = false;
        });
}

const getLink = (field, item) => {
    try {
        return route(
            props.links[field.value]["routeName"],
            props.links[field.value]["routeParam"].indexOf(".") > -1
                ? getTitle(item, props.links[field.value]["routeParam"])
                : item[props.links[field.value]["routeParam"]]
        );
    } catch (error) {
        return "";
    }
};





onMounted(() => {
    Object.values(props.fillable).forEach((val) => {
        item.value[val.value] = "";
    });
});
</script>

<template>
    <div  class="border rounded-md py-3 bg-white">
        <div v-if="props.filter" style="display:flex" class="px-3 col-span-2 pb-4 relative d-flex">
            <input type="text"  v-on:keyup.enter="updateHandler(1)" v-model="q" class="bg-gray-100 rounded-md px-3 py-1.5 w-full border-gray-300 pr-6" placeholder="Поиск...">
            <button v-on:click="updateHandler(1)" type="button" class="ml-2 p-1 text-md rounded-md flex items-center justify-center bg-cyan-800 text-white">
              Найти
            </button>
        </div>

        <div class="mx-auto sm:x-6 lg:mx-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold leading-6 text-gray-900">
                        {{ props.title }}
                    </h1>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <template v-if="$page.props.auth.user.role == 'admin'">
                        <button
                            v-if="props.showAddButton"
                            type="button"
                            @click="addItem"
                            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Добавить
                        </button>
                        <Link
                            v-else-if="showCustomAddButton"
                            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            :href="route(`${props.name}.create`)"
                        >
                            Добавить
                        </Link>
                    </template>
                </div>
            </div>

            <div>
                <slot name="filter" />
            </div>
        </div>
        <div class="mt-8 flow-root overflow-hidden">
            <div class="mx-auto sm:x-6 lg:mx-8">
                <table class="w-full text-left">
                    <thead class="bg-white">
                        <tr>
                            <th
                                v-for="field in fields"
                                :key="field.value"
                                scope="col"
                                class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900"
                            >
                                {{ field.label }}
                            </th>

                            <th v-if=" props.actions.editButton || props.actions.removeButton " scope="col" class="relative py-3.5 pl-3 w-36" >
                                <span>Действия</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr v-for="item in props.items.data" :key="item.id"  class="border-b" >
                            <template v-if="props.contentType === 'text'">
                                <td v-for="field in fields" :key="field.value" class="relative py-4 pr-3 text-sm font-medium text-gray-900">
                                    <div v-if=" 'type' in field && field.type === 'image' "
                                        class="bg-cover w-24 h-12 rounded-md shadow-md"
                                        :style="{
                                            backgroundImage: `url(${item.thumbnail})`,
                                            border:'1px solid red'
                                        }"
                                    >{{item.thumbnail}}</div>
                                  <div v-else-if=" 'type' in field && field.type === 'img' ">

                                    <Image  :element="item" class="w-[70px] h-[80px] object-contain" alt=""/>
                                  </div>
                                    <Link
                                        v-else-if="
                                            links.hasOwnProperty(field.value)
                                        "
                                        class="text-blue-700 underline"
                                        :href="getLink(field, item)"
                                    >
                                        {{
                                            field.value.indexOf(".") > -1
                                                ? getTitle(item, field.value)
                                                : item[field.value]
                                        }}
                                    </Link>
                                    <span
                                        v-else-if="
                                            field.value == 'created_at' &&
                                            item[field.value].length > 0
                                        "
                                    >
                                        {{
                                            new Date(
                                                item[field.value]
                                            ).toLocaleString()
                                        }}</span
                                    >
                                    <span
                                        v-else-if="
                                            'type' in field &&
                                            field.type == 'file' &&
                                            item[field.value].length > 0
                                        "
                                        class="text-blue-700 underline"
                                    >
                                        <a
                                            :href="item[field.value]"
                                            target="_blank"
                                        >
                                            Скачать
                                        </a>
                                    </span>
                                    <span v-else>
                                        {{ links[field.value] }}
                                        {{
                                            field.value.indexOf(".") > -1
                                                ? getTitle(item, field.value)
                                                : item[field.value]
                                        }}</span
                                    >
                                </td>
                            </template>
                            <td v-else class="relative py-4 pr-3 text-sm font-medium text-gray-900">
                                <div class="bg-cover w-24 h-12 rounded-md shadow-md" :style="{backgroundImage: `url(${item.thumbnail})`, }"></div>
                            </td>

                            <td
                                v-if="
                                    props.actions.editButton ||
                                    props.actions.removeButton
                                "
                                class="relative py-4 pl-3 text-right text-sm font-medium flex gap-y-1"
                            >
                                <template
                                    v-if="
                                        props.actions.editButton &&
                                        $page.props.auth.user.role == 'admin'
                                    "
                                >
                                    <button
                                        v-if="!showCustomEditButton"
                                        type="button"
                                        @click="editItem(item)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                            />
                                        </svg>
                                    </button>
                                    <Link
                                        v-else
                                        :href="
                                            route(`${props.name}.edit`, item.id)
                                        "
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                            />
                                        </svg>
                                    </Link>
                                </template>

                                <button
                                    v-if="
                                        props.actions.removeButton &&
                                        $page.props.auth.user.role == 'admin'
                                    "
                                    @click="showDeleteModal(item)"
                                    type="button"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                        />
                                    </svg>
                                </button>
                                <button
                                    v-if="
                                        show_update &&
                                        $page.props.auth.user.role == 'admin' &&
                                        !item.updated
                                    "
                                    type="button"
                                    @click="updateProducts(item)"
                                    class="text-cyan-600 hover:text-cyan-900"
                                    title="Обновить продукты"
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
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                        />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="props.items.total === 0" class="text-center my-2 text-sm text-gray-700">
                    Пусто
                </div>

                <nav class="flex justify-end mt-3">
                    <v-pagination v-model="currentPage" :pages="lastPage" @update:modelValue="updateHandler"/>
                </nav>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50" v-if="showModal">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <form
                @submit.prevent="submit"
                class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6"
            >
                <div>
                    <template v-if="props.contentType == 'text'">


                      <div v-for="(field, index) in props.fillable" :key="index">

                        <template v-if="field.type == 'image'">
                          <label for="name" class="text-gray-700 text-sm"
                          >Изоброжение</label
                          >

                          <label for="image" class="cursor-pointer">
                            <div v-if="item.image" class="relative group">
                              <img
                                  class="object-cover h-[170px] rounded-lg border border-dashed border-gray-900/25 w-full"
                                  :src="item.src"
                                  alt=""
                              />
                              <div
                                  class="group-hover:flex hidden absolute right-0 left-0 bottom-0 top-0 bg-black bg-opacity-50 z-20 items-center justify-center rounded-lg"
                              >
                                    <span
                                        class="inline-block border-2 rounded-lg px-4 py-3 font-bold text-sm text-white"
                                    >Изменить</span
                                    >
                              </div>
                            </div>
                            <div
                                v-else
                                class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10"
                            >
                              <div class="text-center">
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-300"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                  <path
                                      fill-rule="evenodd"
                                      d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                                      clip-rule="evenodd"
                                  ></path>
                                </svg>
                                <div
                                    class="mt-4 flex text-sm leading-6 text-indigo-600 tex"
                                >
                                  <p class="pl-1">
                                    Upload a file. PNG, JPG, GIF up to
                                    10MB
                                  </p>
                                </div>
                              </div>
                            </div>
                          </label>
                          <input
                              id="image"
                              class="w-[1px] h-[1px]"
                              type="file"
                              accept="image/png,image/jpeg,image/jpg,image/gif"
                              or
                              @change="uploadImage2"
                          />
                        </template>

                        <template v-else>
                          <label for="name" class="text-gray-700 text-sm">{{field.label }}</label>
                          <input
                              id="name"
                              v-model="item[field.value]"
                              type="text"
                              required
                              minlength="2"
                              maxlength="255"
                              :placeholder="`Введите ${field.label}`"
                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          />
                        </template>


                        <IError :name="field.value" :errors="errors" />
                      </div>

                    </template>
                    <div v-else>
                        <label for="name" class="text-gray-700 text-sm"
                            >Изоброжение</label
                        >

                        <label for="image" class="cursor-pointer">
                            <div v-if="item.image" class="relative group">
                                <img
                                    class="object-cover h-[170px] rounded-lg border border-dashed border-gray-900/25 w-full"
                                    :src="item.image"
                                    alt=""
                                />
                                <div
                                    class="group-hover:flex hidden absolute right-0 left-0 bottom-0 top-0 bg-black bg-opacity-50 z-20 items-center justify-center rounded-lg"
                                >
                                    <span
                                        class="inline-block border-2 rounded-lg px-4 py-3 font-bold text-sm text-white"
                                        >Изменить</span
                                    >
                                </div>
                            </div>
                            <div
                                v-else
                                class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10"
                            >
                                <div class="text-center">
                                    <svg
                                        class="mx-auto h-12 w-12 text-gray-300"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <div
                                        class="mt-4 flex text-sm leading-6 text-indigo-600 tex"
                                    >
                                        <p class="pl-1">
                                            Upload a file. PNG, JPG, GIF up to
                                            10MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <input
                            id="image"
                            class="w-[1px] h-[1px]"
                            type="file"
                            required
                            accept="image/png,image/jpeg,image/jpg,image/gif"
                            or
                            @change="uploadImage"
                        />
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 flex justify-end gap-x-3">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="inline-flex justify-center rounded-md bg-gray-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-gray-200"
                        :disabled="loading"
                    >
                        Отмена
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-indigo-400"
                    >
                        Сохранить
                    </button>
                </div>
            </form>
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
                                Вы уверены, что хотите удалить {{ props.meta }}?
                                Все следующие объекты и связанные с ними
                                элементы будут удалены.
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
                        @click="removeItem(item)"
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
