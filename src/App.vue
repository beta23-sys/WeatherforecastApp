<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <router-link class="navbar-brand" to="/">WeatherApp</router-link>

      <button class="navbar-toggler" type="button"
              data-bs-toggle="collapse" data-bs-target="#nav"
              aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><router-link class="nav-link" to="/">Home</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/news">News</router-link></li>
          <li class="nav-item"><router-link class="nav-link" to="/about">About</router-link></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <router-view />
  </main>
</template>
