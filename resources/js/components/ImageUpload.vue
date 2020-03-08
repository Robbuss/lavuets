<template>
  <div>
    <fileupload
      style="height: 100%"
      v-show="!result"
      ref="fileupload"
      :accept="(regular ? '.svg,' : '') + '.jpg,.jpeg,.png,.bmp'"
      maxsize="10"
      v-model="file"
      :buttontext="buttontext || 'Kies een afbeelding'"
      :startopen="startopen"
      :emitmeta="true"
      :key="(file && file.data) || 0"
    >
      <slot name="empty" v-if="!result">
        <slot></slot>
      </slot>
    </fileupload>

    <div v-if="result && file === null">
      <div v-on:click="chooseFile" class="pointer">
        <slot name="choosen">
          <slot></slot>
        </slot>
      </div>
      <input v-if="name !== null" :name="name" value type="hidden" />
    </div>
    <div v-else>
      <v-dialog
        max-width="500px"
        :value="true"
        v-if="status === 1"
        @input="emptyFile"
        :closeable="false"
      >
        <v-card>
          <v-card-text>
            <Cropper
              :resize="resize"
              :ext="ext"
              :width="width"
              :height="height"
              :type="type"
              :image="file.data"
              :enforceboundary="enforceboundary"
              v-on:input="cropperdone"
              v-on:closemodal="closemodal"
            />
          </v-card-text>
        </v-card>
      </v-dialog>
      <img v-show="false" ref="img" :src="file" />
      <div v-if="status === 2" class="alert-danger static-alert alert-error">
        De afbeelding moet minimaal {{ width }} pixels breed zijn
        <br />
        De afbeelding moet minimaal {{ height }} pixels hoog zijn
        <br />
      </div>

      <div v-on:click="chooseFile" class="pointer" v-if="file && result">
        <slot name="choosen">
          <slot></slot>
        </slot>
      </div>
      <input v-if="name !== null" :name="name" :value="result" type="hidden" />
    </div>
  </div>
</template>
<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import FileUpload from "./FileUpload.vue";
import Cropper from "./Cropper.vue";

@Component({
  components: {
    fileupload: FileUpload,
    Cropper
  }
})
export default class ImageUpload extends Vue {
  @Prop()
  maxisize: any;

  @Prop()
  value: any;

  @Prop()
  width: any;

  @Prop()
  height: any;

  @Prop()
  preheight: any;

  @Prop()
  name: string;

  @Prop()
  ext: any;

  @Prop()
  enforceboundary: boolean;

  @Prop()
  resize: any;

  @Prop()
  regular: any;

  @Prop()
  buttontext: string;

  @Prop()
  startopen: boolean;

  @Prop()
  type: any;

  private file: any = null;
  private result: any = null;
  private status: any = 0;
  private showGrid = false;

  created() {
    this.result = this.value !== "/image/empty.png" ? this.value : null;
  }

  @Watch("file")
  onFileChanged() {
    setTimeout(() => {
      if (this.file === null) return (this.status = 0);
      const img = this.$refs.img as any;
      if (
        !img.naturalHeight ||
        !img.naturalWidth ||
        (img.naturalHeight >= (this.height || 1) &&
          img.naturalWidth >= (this.width || 1))
      ) {
        if (this.regular || this.file.type === "image/svg+xml") {
          this.cropperdone(this.file.data);
          return;
        }
        this.status = 1;
        return;
      }
      this.status = 2;
    });
  }

  @Watch("value")
  onValueChanged() {
    if (this.value === "" || !this.value) this.file = null;
  }

  get previewheight() {
    if (this.preheight) {
      return this.preheight;
    }
    return 100;
  }

  emptyFile() {
    this.file = null;
    this.status = 0;
    this.result = null;
  }

  cropperdone(result: any) {
    this.result = result.base;
    this.status = 3;
    this.$emit("input", result);
  }

  closemodal() {
    this.file = null;
    this.status = 0;
    this.result = this.value !== "/image/empty.png" ? this.value : null;
  }

  chooseFile() {
    (this.$refs as any).fileupload.open();
  }
}
</script>
