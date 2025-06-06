<script setup>
import axios from "axios";
import { ref } from "vue";
import { onMounted } from "vue";

const loading = ref(true);

const props = defineProps({
  element: Object,

});
const image = ref("");


const getImage = () => {

  let barcodes = props.element.barcodes;
  let img = '/images/empty.webp';
  if(barcodes.length > 0){
    let item  = barcodes.find(el => el.exists);
    if(item !== undefined){
      let time = Date.now();
      img = item.img + '?timestamp='+time
    }

  }

  return img;

}


</script>

<template>
  <img :src="getImage()" @error="'/images/empty.webp'" alt=""/>

</template>
