<template>
  <div style="height: 100%">
    <div @click="open()" style="height: 100%">
      <slot></slot>
    </div>
    <input v-show="false" ref="uploadfile" type="file" v-on:change="(e) => handleFileChange(e)" :accept="accept" />
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Inject, Watch } from "vue-property-decorator";

@Component({})
export default class FileUpload extends Vue {
  @Prop({ default: 30000000})
  maxsize: number;

  @Prop({ default: false })
  emitmeta: boolean;

  @Prop()
  value: string;

  @Prop()
  accept: string;

  @Prop({ default: false})
  startopen !: boolean;

  mounted() {
    if(this.startopen)
      this.open();
  }

  get msize() {
    return this.maxsize * 1024 * 1024;
  }

  get refs(): { uploadfile: { value: string, click: ()=>void}} {
    return this.$refs as any;
  }

  @Watch('value')
  valueWatcher() {
    if (this.value === null) {
      this.refs.uploadfile.value = null;
    }
  }
  handleFileChange(e: { target: { files: Array<any>}}) {
    const file = e.target.files[0];
    if (file !== undefined && file !== null && file.size < this.msize) {
      var reader = new FileReader();
      reader.onload = (event) => {
        if((reader.result as string).length < 10)
          return;
        if(this.emitmeta)
          return this.$emit("input", {
            data: reader.result,
            mime: file.mime,
            name: file.name
          });
        return this.$emit("input", reader.result);
      };
      reader.readAsDataURL(file);
    }
    //e.target.files[0];
  }
  open() {
    this.refs.uploadfile.click();
  }
};
</script>
