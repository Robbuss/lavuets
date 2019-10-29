<template>
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
                <v-list-tile-title
                  v-if="contract.start_date"
                >Vanaf {{ formatDate(contract.start_date) }}</v-list-tile-title>
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
                <v-list-tile-title v-if="contract.default_note">{{ contract.default_note }}</v-list-tile-title>
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
                <v-list-tile-title>
                  {{ u.name }} voor €
                  <span v-if="u.pivot">{{ u.pivot.price }}</span>
                  <span v-else>{{ u.price}}</span> per maand
                </v-list-tile-title>
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
          <template v-for="(f, i) in tenantFields">
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
            <v-divider :key="i + 'd'" v-if="i !== tenantFields.length - 1"></v-divider>
          </template>
        </v-list>
      </v-flex>
    </v-layout>
    <v-card-actions>
      <v-btn class="primary" flat dark @click="dialog= !dialog">Aanpassen</v-btn>
      <v-spacer></v-spacer>
      <v-flex shrink>
        <span class="pl-0 grey--text" v-if="!contract.deactivated_at">Dit contract is actief</span>
        <span
          class="pl-0 grey--text"
          v-else
        >Gedeactiveerd op {{ formatDate(contract.deactivated_at)}}</span>
        <v-switch class="mt-0" hide-details :disabled="!isActive" v-model="isActive" color="green"></v-switch>
      </v-flex>
    </v-card-actions>
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
  </v-card>
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
export default class SingleContractInformation extends Vue {
  private response = "";
  private loading: boolean = true;
  private freeUnits: boolean = false;
  private dialog: boolean = false;
  private showWarning: boolean = false;

  @Prop() // available free units to add to the contract
  units: any;

  @Prop()
  contract: any;

  get tenantFields() {
    return [
      {
        field: this.contract.tenant.name,
        icon: "person",
        tooltip: "Klantnaam"
      },
      {
        field: this.contract.tenant.email,
        icon: "mail",
        tooltip: "Klant e-mailadres"
      },
      {
        field: this.contract.tenant.phone,
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
        field: this.contract.tenant.mandate_id
          ? "Geldig mandaat in Mollie"
          : "Geen mandaat",
        icon: "money",
        tooltip: "Is er een geldig mandaat in mollie?"
      },
      {
        field: this.contract.tenant.company_name || "Particulier",
        icon: "store",
        tooltip: "Bedrijf of particulier"
      },
      {
        field: this.contract.tenant.id,
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

  finished() {
    this.$emit('renew')
    this.dialog = !this.dialog;
  }

  async saveContract() {
    this.contract.free_units = this.freeUnits;
    axios.post("/api/contracts/" + this.contract.id, this.contract);
    store.commit("snackbar", {
      type: "success",
      message: "Contract opgeslagen"
    });
  }

  async deactivate() {
    this.saveContract();
    this.showWarning = false;
  }
}
</script>
