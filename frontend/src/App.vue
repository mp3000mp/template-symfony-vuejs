<template>
  <header id="nav">
    <router-link to="/">Home</router-link> |
    <router-link to="/admin/users">Users</router-link> |
    <router-link to="/about">About</router-link>
  </header>
  <router-view/>
  <footer><font-aw icon="magic" /> Invoked from the magic world by <a id="madeby" href="https://github.com/mp3000mp">mp3000 <font-aw icon="hand-sparkles" /></a></footer>
</template>
