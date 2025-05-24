<script setup>
defineProps({ page: Number, pageCount: Number });
const emit = defineEmits(['update:page']);
</script>

<template>
  <nav aria-label="News pagination">
    <ul class="pagination justify-content-center">
      <li :class="['page-item', { disabled: page === 1 }]">
        <button class="page-link" @click="emit('update:page', page-1)" aria-label="Prev">«</button>
      </li>

      <li v-for="n in pageCount" :key="n" :class="['page-item', { active: n === page }]">
        <button class="page-link" @click="emit('update:page', n)">{{ n }}</button>
      </li>

      <li :class="['page-item', { disabled: page === pageCount }]">
        <button class="page-link" @click="emit('update:page', page+1)" aria-label="Next">»</button>
      </li>
    </ul>
  </nav>
</template>
