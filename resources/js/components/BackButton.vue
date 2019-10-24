<template>
<v-btn icon @click="$router.back()">
  <v-icon>chevron_left</v-icon>
</v-btn>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";

@Component({})
export default class BackButton extends Vue {
}
</script>