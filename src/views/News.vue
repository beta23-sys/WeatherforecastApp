<script setup>
import { ref, computed, onMounted } from 'vue';
import Pagination from '@/components/Pagination.vue';
import newsRaw from '@/data/news.json';

// --- Search & pagination state ---
const search   = ref('');
const page     = ref(1);
const perPage  = 5;
const news     = ref([]);

onMounted(() => {
  news.value = newsRaw;
  loadActions();
});

// --- Like/dislike state ---
const userActions = ref({}); // { [title]: 'liked' | 'disliked' }

function loadActions() {
  const saved = localStorage.getItem('news_actions');
  userActions.value = saved ? JSON.parse(saved) : {};
}

function saveActions() {
  localStorage.setItem('news_actions', JSON.stringify(userActions.value));
}

// Toggle like: set to 'liked' or remove if already liked
function onLike(item) {
  const key = item.title;
  if (userActions.value[key] === 'liked') {
    delete userActions.value[key];
  } else {
    userActions.value[key] = 'liked';
  }
  saveActions();
}

// Toggle dislike: set to 'disliked' or remove if already disliked
function onDislike(item) {
  const key = item.title;
  if (userActions.value[key] === 'disliked') {
    delete userActions.value[key];
  } else {
    userActions.value[key] = 'disliked';
  }
  saveActions();
}

// --- Filtering & pagination (unchanged) ---
const filtered = computed(() => {
  const q = search.value.toLowerCase();
  return news.value.filter(n =>
    n.title.toLowerCase().includes(q) ||
    n.content.toLowerCase().includes(q) ||
    n.category.toLowerCase().includes(q) ||
    n.date.includes(q)
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

    <div class="d-flex align-items-center mt-2">
      <!-- Like button -->
      <button
        class="btn btn-sm me-2"
        :class="userActions[item.title] === 'liked' ? 'btn-primary' : 'btn-outline-primary'"
        @click="onLike(item)"
      >
        👍
      </button>

      <!-- Dislike button -->
      <button
        class="btn btn-sm"
        :class="userActions[item.title] === 'disliked' ? 'btn-danger' : 'btn-outline-danger'"
        @click="onDislike(item)"
      >
        👎
      </button>
    </div>
  </article>

  <Pagination v-model:page="page" :page-count="pageCount" />
</template>

<style scoped>
.btn-sm { min-width: 2.5rem; }
</style>
