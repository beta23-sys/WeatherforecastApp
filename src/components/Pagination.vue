<script setup>
import Paginate from 'vuejs-paginate-next';

const { page, pageCount } = defineProps({
  page:      { type: Number, required: true },
  pageCount: { type: Number, required: true }
});
const emit = defineEmits(['update:page']);

function clickCallback(newPage) {
  emit('update:page', newPage);
}
</script>

<template>
  <Paginate
    :page-count="pageCount"
    :page-range="5"
    :margin-pages="2"
    :initial-page="page"
    :click-handler="clickCallback"
    prev-text="«"
    next-text="»"
    container-class="pagination justify-content-center"
    page-class="page-item"
    page-link-class="page-link"
    prev-class="page-item"
    prev-link-class="page-link"
    next-class="page-item"
    next-link-class="page-link"
    break-view-class="page-item disabled"
    break-view-link-class="page-link"
  />
</template>

<style scoped>
/* if you had any custom pagination styles, keep them or remove if unused */
.pagination { display: flex; list-style: none; padding: 0; }
.page-item { margin: 0 0.25rem; }
.page-link {
  display: block; padding: 0.5rem 0.75rem;
  border: 1px solid #dee2e6; border-radius: 0.25rem;
  color: #0d6efd; text-decoration: none;
}
.page-item.disabled .page-link { color: #6c757d; pointer-events: none; }
.page-item.active .page-link {
  background-color: #0d6efd; color: #fff; border-color: #0d6efd;
}
</style>
