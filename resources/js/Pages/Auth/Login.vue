<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref } from "vue";
import "sweetalert2/dist/sweetalert2.min.css";
import Swal from "sweetalert2/dist/sweetalert2";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = ref({
    login: "",
    password: "",
    remember: false,
});

const loading = ref(false);
const errors = ref({});

const submit = () => {
    loading.value = true;
    errors.value = {};
    axios
        .post(route("login.store"), form.value)
        .then((res) => {
            Swal.fire({
                title: `Успешно`,
                text: "Вы успешно вошли в систему",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
            window.location.href = route("dashboard");
        })
        .catch((err) => {
            if (err.response.data.errors) {
                Object.keys(err.response.data.errors).forEach((key) => {
                    errors.value[key] = err.response.data.errors[key][0];
                });
                console.log(errors.value);
            }
            if (err.response.status == 401 || err.response.status == 419) {
                window.location.href = route("login");
            }
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>

<template>
    <Head title="Войти" />

    <AuthenticationCard>
        <!-- <template #logo>
            <AuthenticationCardLogo />
        </template> -->

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="login" value="Логин" />
                <TextInput
                    id="login"
                    v-model="form.login"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="errors.login" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Пароль" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Запомнить меня</span
                    >
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <!-- <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link> -->

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
