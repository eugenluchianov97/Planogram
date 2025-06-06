<script type="text/javascript">
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2/dist/sweetalert2";
import IError from "@/Components/IError.vue";
import { Head } from "@inertiajs/vue3";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default {
    components: {
        DashboardLayout,
        vSelect,
        IError,
        Head,
    },
    props: {
        shops: { type: Array, default: [] },
        admin: { type: Object, default: {} },
    },
    data() {
        return {
            item: {
                name: "",
                login: "",
                password: "",
                shop_id: "",
                showcases: "",
            },
            showcases: [],
            errors: [],
        };
    },
    mounted() {
        this.item = this.admin;
        this.changeShop();
    },

    methods: {
        submit() {
            axios
                .put(route("users.update", this.item.id), this.item)
                .then((res) => {
                    Swal.fire({
                        title: `Пользователь успешно обновлен`,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: "success",
                        toast: true,
                        position: "top-end",
                    }).then((res) => {
                        window.location.href = route("users.index");
                    });
                })
                .catch((err) => {
                    console.log(err);
                    this.errors = err.response.data.errors;
                });
        },
        changeShop() {
            if (!this.item.shop_id) return;
            this.showcases = [];
            axios
                .get(
                    route("users.index", {
                        source: "showcases",
                        shop_id: this.item.shop_id,
                    })
                )
                .then((res) => {
                    this.showcases = res.data.showcases;
                });
        },
    },
};
</script>

<template>
    <Head title="Редактирование" />
    <DashboardLayout>
        <div class="mb-6">
            <h2 class="font-bold text-xl">Редактирование</h2>
        </div>
        <form class="sm:grid sm:grid-cols-2 gap-6" @submit.prevent="submit()">
            <div class="">
                <label class="block" for=""
                    >Имя:<span class="font-bold text-red-600">*</span></label
                >
                <input
                    type="text"
                    class="form-controls"
                    required
                    v-model="item.name"
                />
                <IError name="name" :errors="errors" />
            </div>
            <div class="">
                <label class="block" for=""
                    >Логин:<span class="font-bold text-red-600">*</span></label
                >
                <input
                    type="text"
                    class="form-controls"
                    required
                    v-model="item.login"
                />
                <IError name="login" :errors="errors" />
            </div>
            <div class="">
                <label class="block" for="">Пароль:</label>
                <input
                    type="password"
                    class="form-controls"
                    v-model="item.password"
                    minlength="8"
                    @click="($event) => ($event.target.type = 'text')"
                    @blur="($event) => ($event.target.type = 'password')"
                />
                <IError name="password" :errors="errors" />
            </div>
            <div class="col-span-2 border-t-2 pt-3">
                <div class="grid grid-cols-3 gap-3">
                    <div class="">
                        <select
                            @change="changeShop"
                            class="form-controls"
                            required
                            v-model="item.shop_id"
                        >
                            <option value="">Выберите торговую точку</option>
                            <option
                                v-for="shop in shops"
                                :key="shop.id"
                                :value="shop.id"
                            >
                                {{ shop.name }}
                            </option>
                        </select>
                        <IError name="shop_id" :errors="errors" />
                    </div>

                    <div class="" v-if="showcases.length > 0">
                        <!-- shelves -->
                        <v-select
                            :options="showcases"
                            label="name"
                            :searchable="true"
                            multiple
                            :clearable="false"
                            v-model="item.showcases"
                        >
                            <template #search="{ attributes, events }">
                                <input
                                    class="vs__search"
                                    :required="item.showcases.length === 0"
                                    v-bind="attributes"
                                    v-on="events"
                                />
                            </template>
                        </v-select>
                        <IError name="showcases" :errors="errors" />
                    </div>
                </div>
            </div>
            <div class="col-span-2 border-t-2 pt-3">
                <div class="flex items-center justify-end gap-x-3">
                    <Link
                        class="dash-btn !bg-gray-300 !text-gray-800"
                        :href="route('users.index')"
                        >Назад</Link
                    >
                    <button class="dash-btn" :href="route('users.index')">
                        Сохранить
                    </button>
                </div>
            </div>
        </form>
    </DashboardLayout>
</template>
