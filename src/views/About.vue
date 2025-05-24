<script setup>
import { ref, computed } from 'vue';
import mountain from '@/assets/mountain.jpg';
import ocean    from '@/assets/ocean.jpg';

const first = ref('');
const last  = ref('');
const pic   = ref('mountain');

const fullName = computed(() =>
  first.value || last.value ? `${first.value} ${last.value}`.trim() : ''
);
</script>

<template>
  <h2>About This App</h2>
  <p>This is a  a modern, single-page weather-forecast app built with Vue 3 and Bootstrap 5. Type any city into the search bar and the page instantly pulls live data from the Tomorrow.io.</p>
  <p>Please input your name below</p>

  <hr />

  <form class="row gy-3">
    <div class="col-12 col-md-6">
      <label class="form-label">First&nbsp;name</label>
      <input v-model="first" type="text" class="form-control" placeholder="e.g John" />
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Last&nbsp;name</label>
      <input v-model="last" type="text" class="form-control" placeholder="e.g Snow" />
    </div>

    <fieldset class="col-12">
      <legend class="fs-6">Choose a picture</legend>
      <div class="form-check form-check-inline">
        <input v-model="pic" class="form-check-input" type="radio" value="mountain" id="pic1" />
        <label class="form-check-label" for="pic1">Mountain</label>
      </div>
      <div class="form-check form-check-inline">
        <input v-model="pic" class="form-check-input" type="radio" value="ocean" id="pic2" />
        <label class="form-check-label" for="pic2">Ocean</label>
      </div>
    </fieldset>
  </form>

  <p class="mt-3 h5">Hello, {{ fullName }}!</p>

  <img :src="pic === 'mountain' ? mountain : ocean"
       class="img-fluid rounded shadow" style="max-width: 400px;"
       :alt="pic === 'mountain' ? 'Mountain scene' : 'Ocean scene'" />
</template>
