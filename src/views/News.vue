<script setup>
import { ref, computed, onMounted } from 'vue';
import Pagination from '@/components/Pagination.vue';
import newsRaw from '@/data/news.json';

const search = ref('');
const page = ref(1);
const perPage = 5;
const news = ref([]);

onMounted(() => { news.value = newsRaw; });

const filtered = computed(() => {
  const q = search.value.toLowerCase();
  return news.value.filter(item =>
    item.title.toLowerCase().includes(q) ||
    item.content.toLowerCase().includes(q) ||
    item.category.toLowerCase().includes(q) ||
    item.date.includes(q)
  );
});

const pageCount = computed(() => Math.ceil(filtered.value.length / perPage));
const paginated = computed(() => {
  const start = (page.value - 1) * perPage;
  return filtered.value.slice(start, start + perPage);
});
</script>

<template>
  <h2 class="mb-3">Weather-Related News</h2>

  <input v-model="search" v-focus type="search" class="form-control mb-3"
         placeholder="Search title, content, date, category…" />

  <article v-for="item in paginated" :key="item.title" class="mb-4 border-bottom pb-2">
    <h5>{{ item.title }}</h5>
    <small class="text-muted">{{ item.date }} • {{ item.category }}</small>
    <p class="mt-2">{{ item.content }}</p>
  </article>

  <Pagination v-model:page="page" :page-count="pageCount" />
</template>
