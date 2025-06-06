<script type="text/javascript">
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2/dist/sweetalert2";
import IError from "@/Components/IError.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        DashboardLayout,
        IError,
        Head,
    },
    props: {
        roles: { type: Array, default: [] },
        admin: { type: Object, default: {} },
    },
    data() {
        return {
            item: {
                name: "",
                login: "",
                password: "",
                telegram_chat_id: null,
            },
            errors: [],
        };
    },
    mounted() {
        this.item = this.admin;
    },

    methods: {
        changeImage($event, type) {
            if ($event.target.files.length > 0) {
                var input = $event.target;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = (e) => {
                        this.item.image = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        },

        submit() {
            axios
                .put(route("admins.update", this.admin.id), this.item)
                .then((res) => {
                    Swal.fire({
                        title: `Администратор успешно создан`,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: "success",
                        toast: true,
                        position: "top-end",
                    });
                    router.visit(route("admins.index"), { replace: true });
                })
                .catch((err) => {
                    this.errors = err.response.data.errors;
                });
        },
    },
};
</script>

<template>
    <Head title="Редактировать администратор" />
    <DashboardLayout>
        <div class="mb-6">
            <h2 class="font-bold text-xl">Редактировать администратор</h2>
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
            <div class="">
                <label class="block" for="telegram_chat_id"
                    >Telegram chat ID:<span class="font-bold text-red-600"
                        >*</span
                    ></label
                >
                <input
                    id="telegram_chat_id"
                    type="number"
                    class="form-controls"
                    v-model="item.telegram_chat_id"
                />
                <IError name="telegram_chat_id" :errors="errors" />
            </div>
            <div class="col-span-2 border-t-2 pt-3">
                <div class="flex items-center justify-end gap-x-3">
                    <Link
                        class="dash-btn !bg-gray-300 !text-gray-800"
                        :href="route('admins.index')"
                        >Назад</Link
                    >
                    <button class="dash-btn" :href="route('admins.index')">
                        Сохранить
                    </button>
                </div>
            </div>
        </form>
    </DashboardLayout>
</template>
