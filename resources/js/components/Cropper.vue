<template>
  <v-row wrap>
    <v-col cols="12" sm="12" style="height:50%">
      <img ref="image" :src="image" style="height:100%; width:100%" />
    </v-col>
    <v-col cols="12" class="text-center py-0">
      <v-btn color="primary" rounded @click="done">Afbeelding uitsnijden</v-btn>
      <v-btn color="primary" rounded @click="rotate">Afbeelding draaien</v-btn>
    </v-col>
    <v-divider></v-divider>
    <v-col cols="12" class="text-center py-0">
      <v-btn text depressed @click="$emit('closemodal')">Annuleren</v-btn>
    </v-col>
  </v-row>
</template>

<style scoped>
@import "../../../node_modules/cropperjs/dist/cropper.css";
</style>
<script lang="ts">
import { Mixins, Component, Prop, Watch } from "vue-property-decorator";
import Vue from "vue";
import CropperJs from "cropperjs";

@Component({
  components: {}
})
export default class Cropper extends Vue {
  @Prop()
  image: any;

  @Prop()
  type: any;

  @Prop()
  width: any;

  @Prop()
  height: any;

  @Prop()
  value: any;

  @Prop()
  ext: any;

  @Prop()
  resize: any;

  @Prop()
  enforceboundary: any;

  cropper = null as CropperJs;

  get imageElement() {
    return this.$refs.image as HTMLImageElement;
  }

  croppable = false;

  mounted() {
    this.cropper = new CropperJs(this.imageElement, {
      aspectRatio: 1,
      viewMode: 1,
      ready: function() {
        this.croppable = true;
      }
    });
  }

  rotate() {
    this.cropper.rotate(90);
  }

  getRoundedCanvas(sourceCanvas: HTMLCanvasElement) {
    var canvas = document.createElement("canvas");
    var context = canvas.getContext("2d");
    var width = sourceCanvas.width;
    var height = sourceCanvas.height;

    canvas.width = width;
    canvas.height = height;
    context.imageSmoothingEnabled = true;
    context.drawImage(sourceCanvas, 0, 0, width, height);
    context.globalCompositeOperation = "destination-in";
    context.beginPath();
    context.arc(
      width / 2,
      height / 2,
      Math.min(width, height) / 2,
      0,
      2 * Math.PI,
      true
    );
    context.fill();
    return canvas;
  }

  async done() {
    const croppedCanvas = this.cropper.getCroppedCanvas();
    // const roundedCanvas = this.getRoundedCanvas(croppedCanvas);

    return this.$emit("input", croppedCanvas.toDataURL());

    // if(this.resize){
    //   this.$emit("input", {base: result, viewport: this.croppie.options.viewport});
    // }else{
    //   this.$emit("input", result);
    // }
  }
}
</script>
