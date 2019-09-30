<template>
  <v-container fluid ma-0 pa-0>
    <v-layout row wrap>
      <v-flex xs12>
        <v-card>
          <v-toolbar flat class="primary" dark>
            <v-toolbar-items v-if="!customer">
              <v-btn icon @click="previous" :disabled="directory.current == '/'">
                <v-icon>chevron_left</v-icon>
              </v-btn>
            </v-toolbar-items>
            <v-toolbar-title>{{ customer ? 'Bestanden van deze klant' : 'Alle bestanden'}}</v-toolbar-title>
            <v-spacer></v-spacer>
              <v-tooltip bottom v-if="!customer">
                <v-btn icon @click="backup" slot="activator">
                  <v-icon>folder</v-icon>
                </v-btn>Backup maken en downloaden
              </v-tooltip>
          </v-toolbar>
          <v-list two-line avatar>
            <template v-for="(directory, i) in directories">
              <v-list-tile :key="i" @click="browse(directory)">
                <v-avatar>
                  <v-icon color="brown lighten-3">folder</v-icon>
                </v-avatar>
                <v-list-tile-content>
                  {{ directory.title }}
                  <v-list-tile-sub-title>{{ directory.meta_info }}</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
              <v-divider :key="i + 'd'"></v-divider>
            </template>
          </v-list>
          <v-list two-line avatar>
            <template v-for="(file, i) in files">
              <v-list-tile :key="i" @click>
                <v-avatar>
                  <v-icon color="red">picture_as_pdf</v-icon>
                </v-avatar>
                <v-list-tile-content>
                  {{ file.title }}
                  <v-list-tile-sub-title>{{ file.last_modified }} - {{ Math.round(file.size / 1000) }}kb</v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                  <v-btn color="grey lighten-3" @click="download(file)" icon>
                    <v-icon color="primary">save_alt</v-icon>
                  </v-btn>
                </v-list-tile-action>
              </v-list-tile>
              <v-divider :key="i + 'e'"></v-divider>
            </template>
            <v-list-tile @click v-if="files.length === 0 && directories.length === 0">
              <v-list-tile-content>
                <v-list-tile-sub-title>Lege map</v-list-tile-sub-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import Files from "./Files.vue";
import { base64StringToBlob } from "blob-util";
const saveAs: any = require("file-saver");

@Component({})
export default class FileBrowser extends Vue {
  private response = "";
  private dialog: boolean = false;
  private loading: boolean = true;
  private files: any = [];
  private directories: any = [];
  private directory = {
    current: "/",
    previous: "/"
  };

  @Prop()
  customer: any;

  download(file: any) {
    const pdf = file;
    var blob = base64StringToBlob(pdf.content, pdf.mime);
    saveAs(blob, file.title);
  }

  async previous() {
    this.directory.current = this.directory.previous;
    const a = this.directory.current.split("/");
    this.directory.previous = this.directory.current.slice(-1,
      a.length - 1
    );
    await this.getData();
  }

  async browse(directory: any) {
    this.directory.previous = this.directory.current;
    this.directory.current = directory.title;
    await this.getData();
  }

  async getData() {
    this.loading = true;
    try {
      const r = (await axios.post("/api/files/browser", {
        directory: this.directory.current
      })).data;
      this.files = r.files;
      this.directories = r.directories;
    } catch (e) {
      this.response = e.message;
    }
    this.loading = false;
  }

  async mounted() {
    if (this.customer) {
      this.directory.current = this.customer.id.toString();
    }
    await this.getData();
  }

  async backup() {
    const r = (await axios.post("/api/files/backup", {
      directory: this.directory.current
    })).data;
    this.getData();
    this.download(r);
    //
  }
}
</script>
