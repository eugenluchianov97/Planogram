<script setup>
import {onMounted, ref, watch} from "vue";
import VPagination from "@hennge/vue3-pagination";
import { router } from "@inertiajs/vue3";
import "@hennge/vue3-pagination/dist/vue3-pagination.css";
import "sweetalert2/dist/sweetalert2.min.css";
import Swal from "sweetalert2/dist/sweetalert2";
import IError from "../../../../Components/IError.vue";

import Image from "@/Components/Image.vue";
import EditSVG from "@/Pages/Dashboard/Product/Components/svg/editBtn.vue";
import RemoveSVG from "@/Pages/Dashboard/Product/Components/svg/removeBtn.vue";

const props = defineProps({
    items: Object,
    title: String,
    meta: String,
    name: String
});

let currentPage = props.items.current_page;
let lastPage = props.items.last_page;



let showModal = ref(false);
let deleteModal = ref(false);

let item = ref({
  BoxWidth:"",
  BoxHeight:"",
  BoxDepth:"",
  file:""
});

/*-------------------------*/
let loading = ref(false);

let errors = ref({});

let q = ref(route().params.q)

const emits = defineEmits(["changePage"]);

const updateHandler = (page = 1) => {

    if (props.filterable) {
        emits("changePage", page);
    }
    else {
        let data = {
           page:page,
           q:q.value
        }

        router.get(route(`${props.name}.index`, data,{
          preserveState: true,
          replace: true,
          preserveScroll: true,
        }));
    }
};

const editItem = (data) => {
    item.value = data;
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

    if ("id" in item.value) {
        let form_data = new FormData();

        for( let k in item.value) {
          form_data.append(k,item.value[k]);
        }
        form_data.append('_method', 'PUT')

        let url = route(`${props.name}.update`, item.value.id);

        let config = {headers:{'Content-Type': 'multipart/form-data'}}
        axios.post(url, form_data,config).then((response) => {

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
              console.log(err)
                errors.value = err.response.data.errors;
            })
            .finally(() => {
                loading.value = false;
            });
    } else {
        axios.post(route(`${props.name}.store`), item.value,).then((response) => {
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

const uploadImage = (event) => {
  if (event.target.files && event.target.files[0]) {
      let reader = new FileReader();
      reader.onload = (e) => {

        item.value.file = event.target.files[0];
        item.value.file.img = e.target.result;
      };
      reader.readAsDataURL(event.target.files[0]);
  }
};

const isAdmin = ($page) => {
  return $page.props.auth.user.role === 'admin';
};


</script>

<template>
    <div  class="border rounded-md py-3 bg-white">

        <div class="flex px-3 col-span-2 pb-4 relative d-flex">
            <input type="text"  v-on:keyup.enter="updateHandler()" v-model="q" class="bg-gray-100 rounded-md px-3 py-1.5 w-full border-gray-300 pr-6" placeholder="Поиск...">
            <button v-on:click="updateHandler()" type="button" class="ml-2 p-1 text-md rounded-md flex items-center justify-center bg-cyan-800 text-white">
              Найти
            </button>
        </div>

        <div class="mx-auto sm:x-6 lg:mx-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold leading-6 text-gray-900">{{ props.title }}</h1>
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

                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Название</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Картинка</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Цена</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Ширина</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Высота</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Глубина</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Поставщик</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Сканер код</th>
                          <th class="relative isolate py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">Торговая точка</th>

                          <th scope="col" class="relative py-3.5 pl-3 w-36" >
                                <span>Действия</span>
                          </th>
                        </tr>
                    </thead>

                    <tbody class="">
                        <tr v-for="item in props.items.data" :key="item.id"  class="border-b" >
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900">
                              <span>{{item.Name}}</span>
                            </td>
                            <td>
                              <Image  :element="item" class="w-[70px] h-[80px] object-contain" alt=""/>
                            </td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.Price}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.BoxWidth}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.BoxHeight}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.BoxDepth}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.Producer}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.ScanerCode}}</span></td>
                            <td class="relative py-4 pr-3 text-sm font-medium text-gray-900"><span>{{item.shop.name}}</span></td>


                            <td class="relative py-4 pl-3 text-right text-sm font-medium flex gap-y-1">

                              <button v-if="isAdmin($page)" type="button" @click="editItem(item)" class="text-indigo-600 hover:text-indigo-900">
                                    <EditSVG/>
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
            <form @submit.prevent="submit" class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
              <!-----------------image----------------------------------------------------------------------------------->
                <label for="img" class="text-gray-700 text-sm">Изоброжение</label>
                <label for="img" class="cursor-pointer">

                    <div v-if="item.file" class="relative group">
                        <img class="object-cover h-[170px] rounded-lg border border-dashed border-gray-900/25 w-full" :src="item.file.img" alt=""/>
                        <div class="group-hover:flex hidden absolute right-0 left-0 bottom-0 top-0 bg-black bg-opacity-50 z-20 items-center justify-center rounded-lg">
                          <span class="inline-block border-2 rounded-lg px-4 py-3 font-bold text-sm text-white">Изменить</span>
                        </div>
                    </div>
                    <div v-else class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                        <template v-if="item.barcodes.length > 0">
                            <Image :element="item" class="object-cover h-[170px] rounded-lg border border-dashed border-gray-900/25 w-full" alt=""/>
                            <div class="group-hover:flex hidden absolute right-0 left-0 bottom-0 top-0 bg-black bg-opacity-50 z-20 items-center justify-center rounded-lg">
                              <span class="inline-block border-2 rounded-lg px-4 py-3 font-bold text-sm text-white">Изменить</span>
                            </div>
                        </template>

                        <div v-else class="text-center">
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
                            <div class="mt-4 flex text-sm leading-6 text-indigo-600 tex">
                                <p class="pl-1">
                                  Upload an image
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
                <input id="img" class="w-[1px] h-[1px]" type="file" accept="image/png,image/jpeg,image/jpg" @change="uploadImage"/>
                <!--------------width-------------------------------------------------------------------------------------->
                <label for="width" class="text-gray-700 text-sm">Ширина</label>
                <input
                    id="width"
                    v-model="item.BoxWidth"
                    type="text"
                    required
                    minlength="1"
                    maxlength="255"
                    placeholder="Введите ширину"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                />
              <IError name="BoxWidth" :errors="errors" />
                <!-------------height-------------------------------------------------------------------------------------->
                <label for="height" class="text-gray-700 text-sm">Высота</label>
                <input
                    id="height"
                    v-model="item.BoxHeight"
                    type="text"
                    required
                    minlength="1"
                    maxlength="255"
                    placeholder="Введите ширину"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                />
              <IError name="BoxHeight" :errors="errors" />
              <!-------------depth-------------------------------------------------------------------------------------->
               <label for="depth" class="text-gray-700 text-sm">Глубина</label>
                <input
                    id="depth"
                    v-model="item.BoxDepth"
                    type="text"
                    required
                    minlength="1"
                    maxlength="255"
                    placeholder="Введите ширину"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                />

                <IError name="BoxDepth" :errors="errors" />
                <div class="mt-5 sm:mt-6 flex justify-end gap-x-3">
                    <button type="button"  :disabled="loading" @click="showModal = false" class="inline-flex justify-center rounded-md bg-gray-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-gray-200">
                        Отмена
                    </button>
                    <button type="submit" :disabled="loading" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-indigo-400">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
