<template>
  <v-flex fluid v-if="!loading" fill-height>
    <v-layout row wrap>
      <v-flex xs12>
        <v-layout row wrap mb-3>
          <v-flex xs12>
            <v-toolbar class="primary" dark>
              <BackButton />
              <v-toolbar-title
                style="cursor:pointer;"
                @click="$router.push('/customers/' + contract.customer.id)"
              >{{ contract.customer.name }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-tooltip bottom>
                <v-btn icon slot="activator" @click="download">
                  <v-icon>insert_drive_file</v-icon>
                </v-btn>
                <span>Contract downloaden</span>
              </v-tooltip>
              <v-tooltip bottom>
                <v-btn icon @click="dialog=!dialog" slot="activator">
                  <v-icon>edit</v-icon>
                </v-btn>
                <span>Contract aanpassen</span>
              </v-tooltip>
            </v-toolbar>
            <v-card>
              <v-layout row wrap>
                <v-flex xs12 md6>
                  <v-list dense>
                    <v-subheader>Contract informatie</v-subheader>
                    <v-tooltip bottom>
                      <v-list-tile slot="activator" @click>
                        <v-list-tile-action>
                          <v-icon color="primary">date_range</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                          <v-list-tile-title>Vanaf {{ formatDate(contract.start_date) }}</v-list-tile-title>
                        </v-list-tile-content>
                      </v-list-tile>
                      <span>Startdatum van het contract</span>
                    </v-tooltip>
                    <v-divider></v-divider>
                    <v-tooltip bottom>
                      <v-list-tile slot="activator" @click>
                        <v-list-tile-action>
                          <v-icon color="primary">edit</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                          <v-list-tile-title
                            v-if="contract.default_note"
                          >{{ contract.default_note }}</v-list-tile-title>
                          <v-list-tile-title v-else>Geen standaard notitie</v-list-tile-title>
                        </v-list-tile-content>
                      </v-list-tile>
                      <span>Standaard notitie op elke factuur</span>
                    </v-tooltip>
                  </v-list>
                  <v-list two-line>
                    <v-subheader>Producten in dit contract</v-subheader>
                    <template v-for="(u, i) in contract.units">
                      <v-divider :key="i + 'd'" v-if="i >0 && contract.units.length > 1"></v-divider>
                      <v-list-tile :key="i">
                        <v-list-tile-avatar>
                          <v-avatar>
                            <v-img src="/open_box.png" />
                          </v-avatar>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                          <v-list-tile-title
                            v-if="u.pivot"
                          >{{ u.name }} voor €{{ u.pivot.price }} per maand</v-list-tile-title>
                          <v-list-tile-sub-title>(standaard prijs: €{{ u.price }})</v-list-tile-sub-title>
                        </v-list-tile-content>
                      </v-list-tile>
                    </template>
                    <v-list-tile v-if="contract.units.length === 0">
                      <v-list-tile-avatar>
                        <v-avatar>
                          <v-img src="/closed_box.png" />
                        </v-avatar>
                      </v-list-tile-avatar>
                      <v-list-tile-content>
                        <v-list-tile-title>Geen Units in dit contract..</v-list-tile-title>
                      </v-list-tile-content>
                    </v-list-tile>
                  </v-list>
                </v-flex>
                <v-flex xs12 sm6 style="border-left: 1px solid #0000001f">
                  <v-list dense>
                    <v-subheader>Klant informatie</v-subheader>
                    <template v-for="(f, i) in customerFields">
                      <v-tooltip bottom :key="i+'t'">
                        <v-list-tile :key="i" slot="activator" @click>
                          <v-list-tile-action>
                            <v-icon color="primary">{{ f.icon }}</v-icon>
                          </v-list-tile-action>
                          <v-list-tile-content>
                            <v-list-tile-title>{{ f.field }}</v-list-tile-title>
                          </v-list-tile-content>
                        </v-list-tile>
                        <span>{{ f.tooltip }}</span>
                      </v-tooltip>
                      <v-divider :key="i + 'd'" v-if="i !== customerFields.length - 1"></v-divider>
                    </template>
                  </v-list>
                </v-flex>
              </v-layout>
            </v-card>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex xs6>
            <p class="font-weight-bold pa-0 ma-0">De status van dit contract</p>
            <v-switch
              :disabled="!isActive"
              class="pa-0"
              v-model="isActive"
              :label="!contract.deactivated_at ? 'Dit contract is actief' : 'Gedeactiveerd op ' + formatDate(contract.deactivated_at)"
              color="green"
            ></v-switch>
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <invoices :contract="contract"></invoices>
        </v-layout>
      </v-flex>
    </v-layout>
    <edit-create :contract="contract" v-if="dialog" :dialog="dialog" @input="finished"></edit-create>
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
            <p>Voor gedeactiveerde contract worden geen facturen meer gemaakt of verzonden.</p>
            <p>Kies of de verhuurde boxen gelijk vrij komen of dat ze bezet blijven tot de opzegdatum van het contract (volgende termijn)</p>
            <v-btn color="orange" dark @click="deactivate">Tot einddatum bezet houden</v-btn>
            <v-btn
              color="red"
              dark
              @click="freeUnits = !freeUnits; deactivate()"
            >Boxen gelijk vrij geven voor verhuur</v-btn>
            <v-btn
              color="grey lighten-3"
              @click="showWarning = !showWarning; contract.deactivated_at = null"
            >Nee, laat maar</v-btn>
          </v-flex>
        </v-layout>
      </v-card>
    </v-dialog>
  </v-flex>
</template>

<script lang="ts">
import Vue from "vue";
import Invoices from "../invoices/Invoices.vue";
import EditCreate from "./EditCreate.vue";
import { Component, Prop, Watch } from "vue-property-decorator";
import axios from "js/axios";
import { base64StringToBlob } from "blob-util";
const saveAs: any = require("file-saver");
import * as moment from "moment";
import store from "js/store";

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
  private freeUnits: boolean = false;
  private dialog: boolean = false;
  private showWarning: boolean = false;

  get customerFields() {
    return [
      {
        field: this.contract.customer.name,
        icon: "person",
        tooltip: "Klantnaam"
      },
      {
        field: this.contract.customer.email,
        icon: "mail",
        tooltip: "Klant e-mailadres"
      },
      {
        field: this.contract.customer.phone,
        icon: "phone",
        tooltip: "Klant telefoonnummer"
      },
      {
        field: this.contract.payment_method,
        icon: "money",
        tooltip: "Betaalwijze"
      },
      {
        field: this.contract.auto_invoice
          ? "Facturen automagisch verzenden aan"
          : "Facturen niet verzenden",
        icon: "money",
        tooltip: "Automagische facturatie"
      },
      {
        field: this.contract.customer.mandate_id
          ? "Geldig mandaat in Mollie"
          : "Geen mandaat",
        icon: "money",
        tooltip: "Is er een geldig mandaat in mollie?"
      },
      {
        field: this.contract.customer.company_name || "Particulier",
        icon: "store",
        tooltip: "Bedrijf of particulier"
      },
      {
        field: this.contract.customer.id,
        icon: "android",
        tooltip: "Klant ID"
      }
    ];
  }

  formatDate(date: any) {
    return moment(date).format("LL");
  }

  get isActive() {
    if (!this.contract) return;
    return this.contract.deactivated_at ? false : true; //v-switch wont work with a date and null so convert to boolean
  }

  set isActive(value: any) {
    if (!value) this.contract.deactivated_at = moment().format("YYYY-MM-DD");
  }

  @Watch("contract.deactivated_at")
  onActiveChanged(newval: boolean, oldval: boolean) {
    if (oldval === null) this.showWarning = true;
  }

  async mounted() {
    await this.getData();
    this.loading = false;
  }

  finished() {
    this.loading = true;
    this.getData();
    this.dialog = !this.dialog;
    this.loading = false;
  }

  async saveContract() {
    this.contract.free_units = this.freeUnits;
    axios.post("/api/contracts/" + this.contract.id, this.contract);
    store.commit("snackbar", { type: "success", message: "Contract opgeslagen" });
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

  async download() {
    const pdf = ((await axios.post(
      "/api/contracts/" + this.$route.params.id + "/pdf"
    )) as any).data;
    if (pdf.success === false) {
      store.commit("snackbar", { type: "error", message: pdf.message });
      return;
    }
    var blob = base64StringToBlob(pdf.content, pdf.mime);
    saveAs(blob, "huurcontract." + pdf.extension);
  }

  async deactivate() {
    this.saveContract();
    this.showWarning = false;
  }
}
</script>
