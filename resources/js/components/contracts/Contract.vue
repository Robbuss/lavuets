<template>
  <v-container fluid v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12>
        <v-layout row wrap mb-3>
          <v-flex xs12>
            <v-toolbar class="primary" dark>
              <v-toolbar-title>{{ contract.customer.name }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-tooltip bottom>
                <v-btn icon @click="dialog=!dialog" slot="activator">
                  <v-icon>edit</v-icon>
                </v-btn>
                <span>Contract aanpassen</span>
              </v-tooltip>
            </v-toolbar>
            <v-card class="pa-3">
              <v-layout row wrap>
                <v-flex xs12 md6>
                  <v-list two-line>
                    <v-subheader>Contract informatie</v-subheader>
                    <v-list-tile>
                      <v-list-tile-content>
                        <v-list-tile-title>Vanaf {{ contract.start_date }}</v-list-tile-title>
                        <v-list-tile-sub-title>Startdatum van het contract</v-list-tile-sub-title>
                      </v-list-tile-content>
                    </v-list-tile>
                    <v-subheader>Producten in dit contract</v-subheader>
                    <v-list-tile v-for="(u, i) in contract.units" :key="i">
                      <v-list-tile-content>
                        <v-list-tile-title
                          v-if="u.pivot"
                        >{{ u.name }} voor €{{ u.pivot.price }} per maand</v-list-tile-title>
                        <v-list-tile-sub-title>(standaard prijs: €{{ u.price }})</v-list-tile-sub-title>
                      </v-list-tile-content>
                    </v-list-tile>
                  </v-list>
                </v-flex>
                <v-flex xs12 sm6>
                  <v-list two-line>
                    <v-list-tile>
                      <v-list-tile-content>
                        <v-list-tile-title>{{ contract.payment_method }}</v-list-tile-title>
                        <v-list-tile-sub-title>Betalingsmethode</v-list-tile-sub-title>
                      </v-list-tile-content>
                    </v-list-tile>
                  </v-list>
                  <v-subheader>Standaard notitie / instructie voor alle facturen in dit contract</v-subheader>
                  <v-flex xs12 px-3 v-if="editDefaultNote">
                    <v-textarea v-model="contract.default_note"></v-textarea>
                    <v-btn class="ml-0" color="primary" @click="saveDefaultNote">Opslaan</v-btn>
                  </v-flex>
                  <v-flex xs12 v-if="!editDefaultNote" px-3>
                    <p>{{ contract.default_note }}</p>
                    <v-tooltip bottom>
                      <v-btn
                        class="ml-0"
                        slot="activator"
                        color="primary--text"
                        icon
                        @click="editDefaultNote = !editDefaultNote"
                      >
                        <v-icon>edit</v-icon>
                      </v-btn>
                      <span>Aanpassen van standaard notitie</span>
                    </v-tooltip>
                  </v-flex>
                </v-flex>
              </v-layout>
            </v-card>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex xs12>
            <p class="font-weight-bold pa-0 ma-0">De status van dit contract</p>
            <v-switch :disabled="!contract.active" class="pa-0" v-model="contract.active" :label="contract.active ? 'Dit contract is actief' : 'Dit contract is niet actief'" color="green"></v-switch>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <invoices :contract="contract" @generate="generate"></invoices>
        </v-layout>
      </v-flex>
    </v-layout>
    <edit-create :contract="contract" v-if="dialog" :dialog="dialog" @input="dialog = !dialog"></edit-create>
    <v-dialog v-model="showWarning" width="80%" persistent>
      <v-card>
        <v-toolbar class="primary" dark>
          <v-toolbar-title>Contract deactiveren?</v-toolbar-title>
        </v-toolbar>
          <v-layout row wrap>
            <v-flex xs12 pa-3>
              <h3 class="headline primary--text">Deactiveren contract</h3>
              <p>
                Weet je zeker dat je het contract wil deactiveren? Je kunt het contract daarna niet meer activeren!
                Je heft met deze actie namelijk de toestemming van de klant op. Je zult een nieuw contract moeten maken wanneer dit een fout is.
              </p>
              <p>
                Voor gedeactiveerde contract wordt niet meer gecontroleerd of er nog facturen open staan en of deze verzonden dienen te worden
              </p>
              <p>
                De boxen die bij dit contract horen kunnen weer verhuurd worden.
              </p>
              <v-btn color="red" dark @click="deactivate">Zeker weten</v-btn>
              <v-btn color="grey lighten-3" @click="showWarning = !showWarning; contract.active = true">Nee, laat maar</v-btn>
            </v-flex>
          </v-layout>              
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script lang="ts">
import Vue from "vue";
import Invoices from "../invoices/Invoices.vue";
import EditCreate from "./EditCreate.vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";

@Component({
  components: {
    invoices: Invoices,
    editCreate: EditCreate
  }
})
export default class SingleContract extends Vue {
  private response = "";
  private loading: boolean = true;
  private contract: any = null;
  private dialog: boolean = false;
  private editDefaultNote: boolean = false;
  private showWarning: boolean = false;

  @Watch('contract.active')
  onActiveChanged(newval: boolean, oldval: boolean){
    if(oldval)
      this.showWarning = true;
  }

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  async generate() {
    this.loading = true;
    await axios.post("/api/invoices/generate", {
      contract_id: this.contract.id
    });
    await this.getData();
    this.loading = false;
  }

  async saveDefaultNote() {
    this.saveContract();
    this.editDefaultNote = !this.editDefaultNote;
  }

  async saveContract(){
    axios.post("/api/contracts/" + this.contract.id, this.contract);
  }

  async getData() {
    try {
      this.contract = (await axios.get(
        "/api/contracts/" + this.$route.params.id
      )).data;
    } catch (e) {
      this.response = e.message;
    }
  }

  async deactivate(){
    this.saveContract();
    this.showWarning = false;
  }
}
</script>
