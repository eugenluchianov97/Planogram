<script setup>
import axios from "axios";
import Swal from "sweetalert2/dist/sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { h, onMounted, ref } from "vue";
import { router, Head } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { VueDraggable } from "vue-draggable-plus";
import Image from "@/Components/Image.vue";
import VPagination from "@hennge/vue3-pagination";
import "@hennge/vue3-pagination/dist/vue3-pagination.css";
import VueEasyLightbox from "vue-easy-lightbox";
import html2canvas from "html2canvas";

const props = defineProps({
    shop: Object,
    showcases: Array,
    shops: Array,
    categories: Array,
    producers: Array,
    userType: String,
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
const deleteShelvesModal = ref(false);
const showCommentModal = ref(false);
const selectedShelves = ref(null);
const zoom = ref(140);
const search = ref("");
const selectedCategory = ref("");
const selectedProducer = ref("");
const comment = ref("");
const products = ref({});
const currentPage = ref(1);
const lastPage = ref(0);
const productLoading = ref(true);
const comments = ref([]);
const images = ref([]);
const disabledComment = ref(false);
const loadingImage = ref(false);
const selectedProduct = ref(null);
const showSidebar = ref(true);

const showMargin = ref(false);
const showPrice = ref(false);
const showPurchase_price = ref(false);
const printing = ref(false);

const form = ref({
    name: "",
    description: "",
    width: "",
    height: "",
    shelf_depth: "",
    shelves: "",
    shelves: [
        {
            position: 1,
            distance: 20,
            shelf_depth: 20,
        },
    ],
    comment: "",
});

const removeItem = () => {
    loading.value = true;
    axios
        .delete(route("showcase.remove", selectedShowcase.value.id))
        .then((res) => {
            Swal.fire({
                title: `Витрина успешно удалён`,
                timer: 1500,
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
        shelves: [
            {
                position: 1,
                distance: 20,
            },
        ],
        comment: "",
    };
};

const editForm = () => {
    form.value = JSON.parse(JSON.stringify(selectedShowcase.value));
    isEdit.value = true;
    isDublicate.value = false;
    showAddModal.value = true;
};

const uploadImage = (e) => {
    if (e.target.files.length > 0) {
        loadingImage.value = true;
        const fd = new FormData();
        fd.append("image", e.target.files[0]);
        axios
            .post(route("upload.image", selectedShowcase.value.id), fd)
            .then((res) => {
                images.value.push(res.data.image);
            })
            .finally(() => {
                loadingImage.value = false;
            });
    }
};

const dublicateForm = () => {
    form.value = JSON.parse(JSON.stringify(selectedShowcase.value));
    showAddModal.value = true;
    isEdit.value = false;
    isDublicate.value = true;
};

const printPage = () => {
    // window.print();
    printing.value = true;

    setTimeout(() => {
        html2canvas(document.querySelector("#print"))
            .then((canvas) => {
                var dataUrl = canvas.toDataURL(); //attempt to save base64 string to server using this var
                var windowContent = "<!DOCTYPE html>";
                windowContent += "<html>";
                windowContent += "<head><title>Print canvas</title></head>";
                windowContent += "<body>";
                windowContent += '<img src="' + dataUrl + '">';
                windowContent += "</body>";
                windowContent += "</html>";
                var printWin = window.open(
                    "",
                    "",
                    "width=" +
                        window.screen.width +
                        ",height=" +
                        window.screen.height
                );
                printWin.document.open();
                printWin.document.write(windowContent);
                printWin.document.close();

                printWin.document.addEventListener(
                    "load",
                    function () {
                        printWin.focus();
                        printWin.print();
                        printWin.document.close();
                        printWin.close();
                    },
                    true
                );
            })
            .finally(() => {
                printing.value = false;
            });
    }, 500);
};

const saveForm = () => {
    loading.value = true;

    if (isEdit.value) {
        storeProducts();
        axios
            .put(route("showcase.update", form.value.id), form.value)
            .then((res) => {
                const id = form.value.id;
                Swal.fire({
                    title: `Витрина успешно обновлен`,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                }).then((res) => {
                    window.location.href = `${route(
                        "shops.show",
                        props.shop.id
                    )}?s=${id}`;
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
            .post(route("showcase.duplicate", [shop_id.value]), form.value)
            .then((res) => {
                Swal.fire({
                    title: `Витрина успешно дублирован`,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });
                if (shop_id.value == props.shop.id) {
                    window.location.reload();
                }
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
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                });

                window.location.reload();

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

function setSelectedShowCase(showcase) {
    selectedShowcase.value = showcase;

    loadComments();
    loadImages();
}

function addShelves() {
    form.value.shelves.push({
        position: form.value.shelves.length + 1,
        distance: 20,
        shelf_depth: 20,
    });
}

function removeShelves() {
    loading.value = true;
    axios
        .delete(route("shelves.remove", selectedShelves.value.id))
        .then((res) => {
            Swal.fire({
                title: `Полка успешно удалён`,
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
            selectedShowcase.value.shelves =
                selectedShowcase.value.shelves.filter(
                    (sh) => sh.id != selectedShelves.value.id
                );

            form.value.shelves = form.value.shelves.filter(
                (sh) => sh.id != selectedShelves.value.id
            );
            selectedShelves.value = null;
            deleteShelvesModal.value = false;
        })
        .catch((err) => {
            Swal.fire({
                title: `Ошибка при удалении полки`,
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

function showRemoveShelves(shelves_count, index) {
    if (isEdit.value) {
        if ("id" in shelves_count) {
            deleteShelvesModal.value = true;
            selectedShelves.value = shelves_count;
        } else {
            selectedShowcase.value.shelves.splice(index, 1);
            form.value.shelves.splice(index, 1);
        }
    } else {
        form.value.shelves.splice(index, 1);
    }
}

const loadComments = () => {
    comments.value = [];
    axios
        .get(route("showcase.comments", selectedShowcase.value.id))
        .then((res) => {
            comments.value = res.data.comments;
        });
};

const loadImages = () => {
    images.value = [];
    axios
        .get(route("showcase.images", selectedShowcase.value.id))
        .then((res) => {
            images.value = res.data.images;
        });
};

const deleteImage = (image, index) => {
    axios.delete(route("remove.showcase.image", image.id)).then((res) => {
        images.value.splice(index, 1);
    });
};

const setProduct = (product) => {
    selectedProduct.value = product;
};

const getShelvesSize = (shelves) => {
    let sum = 0;
    shelves.products.forEach((product) => {
        sum += parseInt(product.BoxWidth);
    });

    return sum;
};

const onDrag = (event) => {
  console.log(event)
    const li = event.to.parentElement.parentElement.dataset.id;
    const liIndex = event.to.parentElement.parentElement.dataset.index;
    // try {
    //     const height = selectedShowcase.value.shelves[liIndex].distance - 4;

    //     event.clonedData.BoxWidth =
    //         (event.clonedData.BoxWidth / event.clonedData.BoxHeight) * height;
    //     event.clonedData.BoxHeight = height;
    // } catch (error) {}

    event.clonedData.updated = new Date().getTime();

    setTimeout(() => {
        const shelves = document.getElementsByClassName(`shelves_${li}`);
        if (shelves != null && shelves.length > 0) {
            const products = shelves[0].querySelectorAll("div.product-element");

            const totalImageWidth = Array.from(products).reduce(
                (total, img) => {
                    const productRect = img.getBoundingClientRect();

                    return total + productRect.width / (zoom.value / 100);
                },
                0
            );

            // console.log();

            if (event.clonedData.BoxHeight > selectedShowcase.value.shelves[liIndex].distance) {
                selectedShowcase.value.shelves[liIndex].products.sort((a, b) => {
                        if (a.updated < b.updated) {
                            return -1;
                        }
                        if (a.updated > b.updated) {
                            return 1;
                        }
                        return 0;
                    }).pop();
                Swal.fire({
                    title: `Товар не влезет на витрину`,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "warning",
                    toast: true,
                    position: "top-end",
                });
            }

            if (
                event.clonedData.BoxDepth >
                selectedShowcase.value.shelves[liIndex].shelf_depth
            ) {
                selectedShowcase.value.shelves[liIndex].products
                    .sort((a, b) => {
                        if (a.updated < b.updated) {
                            return -1;
                        }
                        if (a.updated > b.updated) {
                            return 1;
                        }
                        return 0;
                    })
                    .pop();

                Swal.fire({
                    title: `Товар не влезет на витрину`,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "warning",
                    toast: true,
                    position: "top-end",
                });
            }

            if (selectedShowcase.value.width < totalImageWidth) {
                selectedShowcase.value.shelves[liIndex].products
                    .sort((a, b) => {
                        if (a.updated < b.updated) {
                            return -1;
                        }
                        if (a.updated > b.updated) {
                            return 1;
                        }
                        return 0;
                    })
                    .pop();
                Swal.fire({
                    title: `Товар не влезет на витрину`,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    icon: "warning",
                    toast: true,
                    position: "top-end",
                });
            }
        }
    }, 1000);
};

function sendComment() {
    disabledComment.value = true;

    let fd = new FormData();

    fd.append("comment", comment.value);
    fd.append("showcase_id", selectedShowcase.value.id);

    if (document.getElementById("file_input").files.length > 0) {
        fd.append("doc", document.getElementById("file_input").files[0]);
    }
    console.log(fd);

    loading.value = true;
    axios
        .post(route("send.comment"), fd)
        .then((res) => {
            Swal.fire({
                title: `Ваш комментария успешно отправлен`,
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
            loadComments();
        })
        .catch((err) => {
            Swal.fire({
                title: `Ошибка при отправки комментарии`,
                timer: 4000,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "error",
                toast: true,
                position: "top-end",
            });
        })
        .finally(() => {
            comment.value = "";
            showCommentModal.value = false;
            loading.value = false;
            disabledComment.value = false;
        });
}

function loadProducts(page = 1) {
    currentPage.value = page;
    productLoading.value = true;

    axios
        .get(route("shops.get.products", props.shop.c_id), {
            params: {
                search: search.value,
                page: currentPage.value,
                selectedCategory: selectedCategory.value,
                producer_id: selectedProducer.value,
            },
        })
        .then((res) => {
            products.value = res.data.products.data.map((p) => {
                p.deg = 0;
                return p;
            });
            currentPage.value = res.data.products.current_page;
            lastPage.value = res.data.products.last_page;
        })
        .catch((err) => {})
        .finally(() => {
            productLoading.value = false;
        });
}

function storeProducts() {
    loading.value = true;
    axios
        .post(route("store.shelves.products", selectedShowcase.value.id), {
            shelves: selectedShowcase.value.shelves,
        })
        .then((res) => {
            Swal.fire({
                title: `Данные витрины успешно обновлены`,
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                icon: "success",
                toast: true,
                position: "top-end",
            });
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
}

const changeCategory = () => {
    loadProducts(1);
};

const changeProducer = () => {
    loadProducts(1);
};

onMounted(() => {
    if (props.userType == "user") {
        showSidebar.value = false;
    }
    let params = new URLSearchParams(window.location.search);
    const s = params.get("s");
    if (s != null && s != undefined) {
        const shcase = props.shop.showcases.find((o) => o.id == s);
        if (shcase) {
            setSelectedShowCase(shcase);
        }
    }

    loadProducts(1);
});

const visibleRef = ref(false);
const indexRef = ref(0);

const onShow = (index) => {
    indexRef.value = index;
    visibleRef.value = true;
};
const onHide = () => (visibleRef.value = false);
</script>

<template>
    <Head :title="shop.name"></Head>
    <DashboardLayout mainClass="bg-gray-100 min-h-[92vh]">
        <section class="flex items-start">
            <aside :class="['min-w-[256px] max-w-[256px] rounded-lg shadow-xl max-h-[95vh] p-2 ',{ hidden: !showSidebar },]">
                <div  class="fixed overflow-y-scroll h-full bg-white max-w-[256px] bottom-0 top-16 w-full">
                    <div class="">
                        <ul class="mt-2 h-64 overflow-y-scroll border-y border-y-gray-400">
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
                                    <li class="flex gap-x-1 items-center cursor-pointer p-1"
                                        :class="{
                                            'bg-indigo-600 text-white rounded-md':
                                                selectedShowcase === showcase,
                                        }"
                                        @click="setSelectedShowCase(showcase)"
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
                                    <li v-for="(shelves, index) in showcase.shelves" :key="shelves.id">
                                        <ul class="ml-9">
                                            <li class="">#{{ index + 1 }}</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <template v-if="$page.props.auth.user.role != 'user'">
                            <h4 class="text-lg font-semibold py-2">Товары</h4>
                            <div class="col-span-2 pb-4 relative">
                                <input
                                    type="search"
                                    v-model="search"
                                    @input="
                                        search.length >= 3
                                            ? loadProducts()
                                            : () => {}
                                    "
                                    class="bg-gray-100 rounded-md px-3 py-1.5 w-full border-gray-300 pr-6"
                                    placeholder="Поиск..."
                                />
                                <button
                                    type="button"
                                    @click="
                                        search = '';
                                        loadProducts();
                                    "
                                    class="absolute right-1 top-2"
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
                                            d="M6 18 18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="mb-3">
                                <select
                                    @change="changeCategory"
                                    v-model="selectedCategory"
                                    class="bg-gray-100 rounded-md px-3 py-1.5 w-full border-gray-300 pr-6"
                                >
                                    <option value="">Выберите категорию</option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <select
                                    @change="changeProducer"
                                    v-model="selectedProducer"
                                    class="bg-gray-100 rounded-md px-3 py-1.5 w-full border-gray-300 pr-6"
                                >
                                    <option value="">
                                        Выберите поставщика
                                    </option>
                                    <option
                                        v-for="producer in producers"
                                        :key="producer.id"
                                        :value="producer.id"
                                    >
                                        {{ producer.name }}
                                    </option>
                                </select>
                            </div>
                            <VueDraggable
                                v-if="products.length > 0"
                                class="grid grid-cols-2 gap-3 pb-4 cursor-pointer"
                                v-model="products"
                                :group="{
                                    name: 'people',
                                    pull: 'clone',
                                    put: false,
                                }"
                                animation="150"
                                ghostClass="ghost"
                                :emptyInsertThreshold="100"
                                :sort="false"
                                @end="onDrag($event)"
                            >
                                <div
                                    v-for="element in products"
                                    :key="element.id"
                                    class="relative group"
                                    :data-width="element.BoxWidth"
                                >
                                    <Image  :url="`https://shelf.agg.md/storage/pictures/${element.ScanerCode}.PNG`" :url2="'https://shelf.agg.md/storage/pictures/'+element.Code+'.PNG'" class="w-[70px] h-[80px] object-contain" alt=""/>

                                    <div class="z-10 absolute hidden group-hover:grid top-1 bg-white rounded-full text-[12px] px-1 shadow-lg">
                                        <span class="">{{ element.BoxWidth }}x{{ element.BoxHeight }}x{{ element.BoxDepth }}</span>
                                    </div>
                                    <div class="relative">
                                        <div class="product-info break-words line-clamp-2 group-hover:line-clamp-none group-hover:absolute group-hover:z-50 group-hover:bg-white group-hover:shadow-lg p-1">
                                            {{ element.Name }}
                                            <span>{{ element.Price }}</span>
                                          <hr>
                                            <span style="justify-content: center;display: flex;">{{ element.Producer }}</span>
                                        </div>
                                    </div>
                                </div>
                            </VueDraggable>
                            <nav
                                class="flex justify-center mt-3 relative z-10 mb-16"
                            >
                                <v-pagination
                                    v-model="currentPage"
                                    :pages="lastPage"
                                    @update:modelValue="loadProducts"
                                />
                            </nav>
                        </template>
                    </div>
                </div>
            </aside>
            <main class="w-full overflow-hidden bg-gray-100 overflow-y-scroll min-h-screen">
                <nav class="w-full bg-white shadow-lg rounded-md px-3 py-2 flex items-center justify-between">
                    <ul class="flex items-center gap-x-2 font-medium h-[38px]">
                        <li>
                            <button
                                type="button"
                                @click="showSidebar = !showSidebar"
                                class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-gray-100 hover:bg-gray-200 transition-all duration-500"
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
                                        d="M3.75 9h16.5m-16.5 6.75h16.5"
                                    />
                                </svg>
                            </button>
                        </li>
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
                                        @click="storeProducts"
                                        class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-blue-500 hover:bg-blue-700 transition-all duration-500 text-white"
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
                                                d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3"
                                            />
                                        </svg>

                                        <span> Сохранить </span>
                                    </button>
                                </li>
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
                            v-if="selectedShowcase != null"
                            type="button"
                            @click="printPage"
                            class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-cyan-500 hover:bg-cyan-600 transition-all duration-500 text-white"
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
                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"
                                />
                            </svg>

                            <span> Печать </span>
                        </button>
                    </div>
                </nav>

                <div id="print" class="py-4 ml-6">
                    <template v-if="selectedShowcase != null">
                        <div class="flex gap-x-2 items-center mb-3">
                            <h2 class="text-xl font-semibold">
                                {{ selectedShowcase.name }}
                            </h2>
                        </div>
                        <div :class="{ hidden: printing }">
                            <div v-if="$page.props.auth.user.role !== 'user'" class="flex items-center">
                                <input
                                    checked
                                    id="showMargin"
                                    type="checkbox"
                                    v-model="showMargin"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                                <label
                                    for="showMargin"
                                    class="ms-2 text-sm font-medium text-gray-900"
                                    >Маржа</label
                                >
                            </div>

                            <div class="flex items-center">
                                <input
                                    checked
                                    id="showPrice"
                                    type="checkbox"
                                    v-model="showPrice"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                                <label
                                    for="showPrice"
                                    class="ms-2 text-sm font-medium text-gray-900"
                                    >Цена магазина</label
                                >
                            </div>

                            <div v-if="$page.props.auth.user.role !== 'user'" class="flex items-center">
                                <input
                                    checked
                                    id="showPurchase_price"
                                    type="checkbox"
                                    v-model="showPurchase_price"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                                <label
                                    for="showPurchase_price"
                                    class="ms-2 text-sm font-medium text-gray-900"
                                    >Входной ценник</label
                                >
                            </div>
                        </div>
                        <div
                            class="overflow-scroll max-w-full bg-white rounded-md p-3 shadow-xl mt-5"
                            :style="{
                                paddingLeft: `${50 * (zoom / 100)}px`,
                                paddingTop: `${10 * (zoom / 100)}px`,
                                minHeight: !printing ? `${400}px` : '',
                            }"
                        >
                            <ul
                                style="border:1px solid green"
                                class="border-x-4 border-t-4 border-x-gray-500 border-t-gray-500"
                                :style="{
                                    width: `${selectedShowcase.width + 8}px`,
                                    minWidth: `${selectedShowcase.width + 8}px`,
                                    maxWidth: `${selectedShowcase.width + 8}px`,
                                    zoom: `${zoom}%`,
                                }"
                            >
                                <li v-for="(shelves, index) in selectedShowcase.shelves"
                                    :key="shelves.id"
                                    :class="[
                                        'flex items-end gap-x-3 border-b-4 border-b-gray-500 bg-cyan-50 relative',
                                        `shelves_${shelves.id}`,
                                    ]"
                                    :style="{
                                        height: `${shelves.distance}px`,
                                    }"
                                    :data-id="shelves.id"
                                    :data-index="index"
                                >
                                    <span class="absolute -left-12 w-5 h-5 text-xs rounded-md flex items-center justify-center bg-cyan-800 text-white">{{ index + 1 }}</span>

                                    <span class="absolute -left-6 top-0 text-xs rotate-90">{{ shelves.distance }}</span>
                                    <div class="product-element flex items-end gap-x-[4px]">
                                        <VueDraggable
                                            class="flex items-end"
                                            v-model="shelves.products"
                                            group="people"
                                            animation="150"
                                            ghostClass="ghost"
                                            :emptyInsertThreshold="100"
                                            :disabled="
                                                $page.props.auth.user.role ==
                                                'user'
                                            "
                                            @end="onDrag($event)"
                                            style="border:1px solid blue;width:100%!important;"
                                        >
                                            <div style="border:1px solid red;" v-for="(element, elementIndex) in shelves.products"
                                                :key="element.id"
                                                @click="setProduct(element)"
                                                class="cursor-move relative group flex items-center justify-around"
                                                :data-width="element.BoxWidth"
                                                :style="{
                                                    width: `${
                                                        element.deg == 90 ||
                                                        element.deg == 270
                                                            ? element.BoxHeight
                                                            : element.BoxWidth
                                                    }px`,
                                                    height: `${
                                                        element.deg == 90 ||
                                                        element.deg == 270
                                                            ? element.BoxWidth
                                                            : element.BoxHeight
                                                    }px`,
                                                }"
                                            >
                                                <div class="z-10 absolute bg-white rounded-full text-[10px] hidden group-hover:grid -top-4 px-1">
                                                    {{
                                                        parseFloat(
                                                            element.BoxWidth
                                                        ).toFixed(1)
                                                    }}x{{
                                                        parseFloat(
                                                            element.BoxHeight
                                                        ).toFixed(1)
                                                    }}x{{
                                                        parseFloat(
                                                            element.BoxDepth
                                                        ).toFixed(1)
                                                    }}
                                                </div>
                                                <div class="absolute bg-black grid z-10 text-[8px] text-white">
                                                    <span v-if="showMargin">{{
                                                        parseFloat(
                                                            element.Margin
                                                        ).toFixed(2)
                                                    }}</span>
                                                    <span v-if="showPrice">{{
                                                        parseFloat(
                                                            element.Price
                                                        ).toFixed(2)
                                                    }}</span>
                                                    <span
                                                        v-if="
                                                            showPurchase_price
                                                        "
                                                        >{{
                                                            parseFloat(
                                                                element.Purchase_price
                                                            ).toFixed(2)
                                                        }}</span
                                                    >
                                                </div>
                                                <div
                                                    class="z-10 absolute top-0 right-0 hidden group-hover:grid bg-white p-[1px] rounded-md"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="
                                                            ($event) => {
                                                                $event.stopImmediatePropagation();
                                                                shelves.products.splice(
                                                                    elementIndex,
                                                                    1
                                                                );
                                                            }
                                                        "
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            stroke="currentColor"
                                                            class="size-2 text-red-600"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                            />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div
                                                    :style="{
                                                        transform: `rotate(${element.deg}deg)`,
                                                        width: `${element.BoxWidth}px`,
                                                        height: `${element.BoxHeight}px`,
                                                        position: 'absolute',
                                                        top: `${
                                                            element.deg == 90 ||
                                                            element.deg == 270
                                                                ? (element.BoxWidth -
                                                                      element.BoxHeight) /
                                                                  2
                                                                : 0
                                                        }px`,
                                                    }"
                                                >
                                                    <Image
                                                        :url="`https://shelf.agg.md/storage/pictures/${element.ScanerCode}.PNG`"
                                                        :url2="`https://shelf.agg.md/storage/pictures/${element.Code}.PNG`"
                                                        alt=""
                                                        class="w-full h-full"
                                                        :style="{
                                                            width: `${element.BoxWidth}px`,
                                                            height: `${element.BoxHeight}px`,
                                                            // transform: `rotate(${element.deg}deg)`,
                                                        }"
                                                    />
                                                </div>
                                            </div>
                                        </VueDraggable>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div
                            :class="{ hidden: printing }"
                            class="mt-7 flex gap-x-2"
                        >
                            <span>
                                <input
                                    type="range"
                                    name="zoom"
                                    id="zoom"
                                    min="10"
                                    max="400"
                                    v-model="zoom"
                                />
                            </span>
                            <label for="zoom">{{ zoom }}%</label>
                            <button
                                type="button"
                                @click="
                                    if (parseInt(zoom) + 10 < 400) {
                                        zoom = parseInt(zoom) + 10;
                                    } else {
                                        zoom = 400;
                                    }
                                "
                                class="p-1 text-xs rounded-md flex items-center justify-center bg-cyan-800 text-white"
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
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                @click="
                                    if (zoom - 10) zoom = parseInt(zoom) - 10;
                                "
                                class="p-1 text-xs rounded-md flex items-center justify-center bg-cyan-800 text-white"
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
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM13.5 10.5h-6"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div
                            :class="{ hidden: printing }"
                            class="mt-5 border-b pb-3"
                        >
                            <div class="">
                                <h2 class="font-bold text-lg my-3">
                                    Комментария витрины
                                </h2>
                                <div>
                                    {{ selectedShowcase.comment }}
                                </div>
                            </div>
                        </div>

                        <div
                            :class="{ 'my-7': !printing }"
                            v-for="(shelves, index) in selectedShowcase.shelves"
                            :key="shelves.id"
                        >
                            <h2 class="text-lg font-semibold mb-2">
                                Полка #{{ index + 1 }}
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
                                            <th scope="col" class="px-6 py-3">
                                                №
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Название
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Цена
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            class="bg-white border-b"
                                            v-for="product in shelves.products"
                                            :key="product.id"
                                        >
                                            <th
                                                scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                            >
                                                {{ product.id }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ product.Name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ product.Price }} MDL
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div :class="{ hidden: printing }">
                            <h2 class="font-bold text-lg mb-3">Изображение</h2>

                            <div
                                class="grid gap-4 lg:grid-cols-3 sm:grid-cols-2"
                            >
                                <div
                                    v-for="(image, index) in images"
                                    :key="image.id"
                                    class="flex items-center justify-center group relative rounded-md max-w-sm bg-white border border-gray-200 shadow hover:bg-gray-100"
                                >
                                    <div
                                        class="absolute top-1 right-1 z-10 hidden group-hover:grid gap-2"
                                    >
                                        <button
                                            type="button"
                                            @click="onShow(index)"
                                            class="p-1 bg-blue-300/50 rounded-lg"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="size-5 text-blue-500"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteImage(image, index)"
                                            type="button"
                                            class="p-1 bg-red-300/50 rounded-lg"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="size-5 text-red-600"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <img
                                        :src="image.src"
                                        class="h-36 rounded-md"
                                        alt=""
                                    />
                                </div>
                                <label
                                    v-if="images.length < 6"
                                    for="upload_image"
                                    class="flex items-center justify-center max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100"
                                >
                                    <input
                                        type="file"
                                        @change="uploadImage"
                                        :disabled="loadingImage"
                                        accept="image/jpeg,image/gif,image/png"
                                        id="upload_image"
                                        class="w-1 h-1 opacity-10"
                                    />
                                    <div
                                        class="grid items-center justify-center"
                                    >
                                        <svg
                                            v-if="loadingImage"
                                            aria-hidden="true"
                                            class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600 mx-auto"
                                            viewBox="0 0 100 101"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor"
                                            />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="size-6 mx-auto"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"
                                            />
                                        </svg>
                                        <div>
                                            <span>Загружать изображения</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div :class="{ hidden: printing }" class="mt-5">
                            <div class="sm:flex items-center gap-x-3">
                                <h2 class="font-bold text-lg my-3">
                                    Комментарии
                                </h2>
                                <button
                                    type="button"
                                    @click="showCommentModal = true"
                                    class="flex gap-x-1 text-sm items-center rounded-md px-2.5 py-1 bg-blue-500 hover:bg-blue-700 transition-all duration-500 text-white"
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
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"
                                        />
                                    </svg>
                                    <span> Добавить комментарий </span>
                                </button>
                            </div>

                            <table
                                class="w-full text-sm text-left rtl:text-right text-gray-500"
                            >
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-3">№</th>
                                        <th scope="col" class="px-6 py-3">
                                            Пользователь
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Коментария
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Файл
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Дата
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        class="bg-white border-b"
                                        v-for="comment in comments"
                                        :key="comment.id"
                                    >
                                        <th
                                            scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                        >
                                            {{ comment.id }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ comment.user.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ comment.comment }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a
                                                v-if="
                                                    comment.document.length > 0
                                                "
                                                :href="comment.document"
                                                class="underline"
                                                target="_blank"
                                            >
                                                Скачать</a
                                            >
                                        </td>
                                        <td class="px-6 py-4">
                                            {{
                                                new Date(
                                                    comment.created_at
                                                ).toLocaleString()
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <vue-easy-lightbox
                            :visible="visibleRef"
                            :imgs="images.map((o) => o.src)"
                            :index="indexRef"
                            @hide="onHide"
                        ></vue-easy-lightbox>
                    </template>
                </div>
            </main>
        </section>

        <div
            v-if="showAddModal"
            class="fixed inset-0 bg-gray-500/75 transition-opacity z-[51]"
        >
            <div class="fixed inset-0 z-[51] w-screen overflow-y-auto">
                <div
                    ref="modalForm"
                    class="modalForm active pointer-events-none fixed inset-y-0 flex max-w-full pl-10 sm:pl-16 right-0"
                >
                    <div class="pointer-events-auto w-screen max-w-2xl">
                        <form
                            @submit.prevent="saveForm"
                            class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl"
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
                                    <!-- <div
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
                                    </div> -->

                                    <div
                                        class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5"
                                    >
                                        <div>
                                            <label
                                                for="shelves"
                                                class="block text-sm font-medium leading-6 text-gray-900 sm:mt-1.5"
                                                >Полки (расстояние между)
                                            </label>
                                        </div>
                                        <div class="sm:col-span-2 grid gap-3">
                                            <div
                                                v-for="(
                                                    shelves_count, index
                                                ) in form.shelves"
                                                :key="shelves_count.position"
                                                class="flex items-center gap-x-2"
                                            >
                                                <span class="w-10">
                                                    #{{ index + 1 }}
                                                </span>
                                                <div
                                                    class="grid grid-cols-2 items-end gap-3"
                                                >
                                                    <div class="">
                                                        <label
                                                            class="text-xs leading-1 inline-block"
                                                            for=""
                                                            >Расстояние между
                                                            полками</label
                                                        >
                                                        <input
                                                            type="number"
                                                            :name="`distance_${shelves_count}`"
                                                            :id="`distance_${shelves_count}`"
                                                            min="15"
                                                            max="200"
                                                            placeholder="Введите расстояние между полками"
                                                            required
                                                            v-model="
                                                                shelves_count.distance
                                                            "
                                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        />
                                                    </div>
                                                    <div class="">
                                                        <label
                                                            class="text-xs leading-1"
                                                            for=""
                                                            >Глубина
                                                            полок</label
                                                        >
                                                        <input
                                                            type="number"
                                                            :name="`distance_${shelves_count}`"
                                                            :id="`distance_${shelves_count}`"
                                                            min="15"
                                                            max="300"
                                                            placeholder="Введите глубина полок"
                                                            required
                                                            v-model="
                                                                shelves_count.shelf_depth
                                                            "
                                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        />
                                                    </div>
                                                </div>
                                                <button
                                                    @click="
                                                        showRemoveShelves(
                                                            shelves_count,
                                                            index
                                                        )
                                                    "
                                                    type="button"
                                                    class="bg-red-600 p-2 rounded-md text-white"
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
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div>
                                                <button
                                                    @click="addShelves"
                                                    type="button"
                                                    class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                >
                                                    Добавить полку
                                                </button>
                                            </div>
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
                                                name="shop_id"
                                                id="shop_id"
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
                                        :disabled="loading"
                                        class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:bg-gray-300"
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
                                    следующие объекты и связанные с ними
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

        <div
            v-if="deleteShelvesModal"
            class="fixed inset-0 z-[51] overflow-y-auto bg-black bg-opacity-50"
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
                                Вы действительно хотите удалить полку с витрины?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    При удалении полки с витрины все связанные с
                                    ней продукты будут удалены.
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
                            @click="removeShelves"
                        >
                            Удалить
                        </button>
                        <button
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                            @click="deleteShelvesModal = false"
                        >
                            Отмена
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showCommentModal"
            class="fixed inset-0 z-[51] overflow-y-auto bg-black bg-opacity-50"
        >
            <div
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
            >
                <form
                    @submit.prevent="sendComment"
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                >
                    <div>
                        <label for="comment">Введите комментарий</label>
                        <div class="mx-auto flex items-center rounded-full">
                            <textarea
                                name="comment"
                                id="comment"
                                v-model="comment"
                                minlength="3"
                                rows="5"
                                required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            ></textarea>
                        </div>
                        <div>
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="file_input"
                                >Upload file</label
                            >
                            <input
                                class="py-3 px-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"
                                aria-describedby="file_input_help"
                                id="file_input"
                                type="file"
                            />
                            <p
                                class="mt-1 text-sm text-gray-800 font-semibold"
                                id="file_input_help"
                            >
                                PNG, JPG, GIF or PDF (10 Мб).
                            </p>
                        </div>
                    </div>
                    <div
                        class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3"
                    >
                        <button
                            type="submit"
                            :disabled="disabledComment"
                            class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2"
                        >
                            Отправить
                        </button>
                        <button
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                            @click="showCommentModal = false"
                        >
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            v-if="selectedProduct != null"
            class="fixed inset-0 z-[51] overflow-y-auto bg-black bg-opacity-50"
        >
            <div
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
            >
                <form @submit.prevent="sendComment"
                    class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                >
                    <div>
                        <label for="comment" class="mb-3">Размер товара</label>
                        <div class="mx-auto grid grid-cols-3 gap-3 items-center rounded-full mb-3">
                            <div>
                                <label for="BoxWidth" class="text-xs font-medium">
                                    Ширина
                                </label>
                                <input
                                    type="number"
                                    name="BoxWidth"
                                    placeholder="Ширина"
                                    v-model="selectedProduct.BoxWidth"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    for="BoxWidth"
                                    class="text-xs font-medium"
                                >
                                    Высота
                                </label>
                                <input
                                    type="number"
                                    placeholder="Высота"
                                    v-model="selectedProduct.BoxHeight"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    for="BoxWidth"
                                    class="text-xs font-medium"
                                >
                                    Глубина
                                </label>
                                <input
                                    type="number"
                                    placeholder="Глубина"
                                    v-model="selectedProduct.BoxDepth"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    required
                                />

                            </div>

                        </div>
                    </div>

                    <hr />

                    <div class="mt-2">
                        <label for="comment" class="mb-3"
                            >Положение товара на витрине ({{
                                selectedProduct.deg
                            }}°)</label
                        >
                        <div
                            class="mx-auto grid items-center rounded-full mb-3"
                        >
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    @click="
                                        selectedProduct.deg =
                                            selectedProduct.deg == 0
                                                ? 270
                                                : selectedProduct.deg - 90
                                    "
                                    class="rounded-md bg-blue-500 hover:bg-blue-600 text-white px-2.5 py-0.5 flex items-center gap-1"
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
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"
                                        />
                                    </svg>
                                    на лево
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        selectedProduct.deg =
                                            selectedProduct.deg >= 270
                                                ? 0
                                                : selectedProduct.deg + 90
                                    "
                                    class="rounded-md bg-blue-500 hover:bg-blue-600 text-white px-2.5 py-0.5 flex items-center gap-1"
                                >
                                    на право
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
                                            d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="mt-2 p-2">
                          <div><b>Name:</b>{{selectedProduct.Name}}</div>
                          <div><b>Code:</b>{{selectedProduct.Code}}</div>
                          <div><b>ScanerCode:</b>{{selectedProduct.ScanerCode}}</div>
                          <div><b>Price:</b>{{selectedProduct.Price}}</div>
                          <div><b>Full description:</b>{{selectedProduct.FullDescription}}</div>
                          <div><b>Producer:</b>{{selectedProduct.Producer}}</div>
                          <div><b>Margin:</b>{{selectedProduct.Margin}}</div>
                          <div><b>Margin rate:</b>{{selectedProduct.Margin_rate}}</div>
                          <div>{{console.log(selectedProduct)}}</div>
                    </div>
                    <hr />

                    <div class="grid justify-end mt-3">
                        <button
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                            @click="selectedProduct = null"
                        >
                            Закрыть
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
