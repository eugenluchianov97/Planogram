<script setup>
import axios from "axios";
import { ref } from "vue";
import { onMounted } from "vue";


const image = ref("");

const props = defineProps({
  element: {
    type: Object,
    required:false
  },
  src:{
    type: Boolean,
    default: null,
    required:false
  }

});

const src = () => {

  let img = props.src;

  let barcodes = props.element.barcodes;

  if(barcodes.length > 0){
    let item  = barcodes.find(el => el.exists);
    if(item !== undefined){
      let time = Date.now();
      img = item.img + '?timestamp='+time
    }

  }

  if(img == null){
    img = '/images/empty.webp'
  }


  return img;

}


</script>

<template>
  <img :src="src()" @error="'/images/empty.webp'" alt=""/>
</template>
