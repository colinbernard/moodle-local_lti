<template>
  <div>
    <h2>{{title}}</h2>
    <a :href="returnUrl">Return to course</a>
    <ul>
      <li v-for="page in pages">
        <a @click="changePage(page.pagenum)">{{ page.name }}</a>
      </li>
    </ul>
    <div v-if="resource_content && ! loading" v-html="resource_content"></div>
    <div v-if="loading">
      <p>Loading...</p>
    </div>
  </div>
</template>

<script>
import moodleAjax from "@/mixins/moodleAjax";

export default {
  name: "Resource",
  props: ['token', 'returnUrl'],
  mixins: [moodleAjax],
  data() {
    return {
      resource_content: null,
      title: "",
      loading: true,
      pages: [],
      currentPage: 0
    }
  },
  methods: {
    loadContent() {
      this.moodleAjax('local_lti_get_content', this.token, {pagenum: this.currentPage}).then(response => {
        this.resource_content = response.raw_content;
        this.title = response.title;
        this.pages = response.pages;
        this.loading = false;
      });
    },
    changePage(pagenum) {
      this.loading = true;
      this.currentPage = pagenum;
      this.loadContent();
    }
  },
  mounted() {
    this.loadContent();
  }
}
</script>

<style scoped>

</style>