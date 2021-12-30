<template>
  <v-row>
    <v-col cols="12" md="9">
      <v-card id="invoice-to-print">
        <!-- HEader Datos de Sucursal -->
        <v-card-text class="py-9 px-8" id="header-invoice">
          <div
            class="invoice-header d-flex flex-wrap justify-space-between flex-column flex-sm-row"
          >
            <div class="mb-8 mb-sm-0">
              <div class="d-flex align-center mb-6">
                <v-img
                  src="../img/etbsa-logo-agricola.png"
                  contain
                  max-width="180"
                >
                </v-img>
              </div>
              <span class="d-block">
                Carretera Panamericana Celaya-Salamanca Km.61
              </span>
              <span class="d-block">
                1ra Fracc Crespo C.P. 38120 Celaya, Gto
              </span>
              <span class="d-block">
                (461) 614 23 23, (461) 614 23 24, (461) 614 23 25
              </span>
            </div>
            <div class="text-right">
              <div class="mb-4 d-flex align-center justify-end">
                <span class="me-2 font-weight-medium text-xl text--primary">
                  Pedido:
                </span>
                <v-text-field
                  placeholder="5009"
                  persistent-placeholder
                  class="flex-grow-0"
                  hide-details
                  outlined
                  filled
                  solo
                  dense
                ></v-text-field>
              </div>
              <div class="mb-4 d-flex align-center justify-end">
                <span class="me-2">Fecha de Emision:</span>
                <v-text-field
                  outlined
                  placeholder="5009"
                  persistent-placeholder
                  class="flex-grow-0"
                  hide-details
                  filled
                  dense
                  solo
                ></v-text-field>
              </div>
              <div class="mb-2 d-flex align-center justify-end">
                <span class="me-2">Fecha Vencimiento:</span>
                <v-text-field
                  placeholder="5009"
                  persistent-placeholder
                  class="flex-grow-0"
                  hide-details
                  outlined
                  filled
                  dense
                  solo
                ></v-text-field>
              </div>
            </div>
          </div>
        </v-card-text>
        <v-divider />
        <!-- Datos Cliente -->
        <v-card-text class="py-9 px-8">
          <div
            class="payment-details d-flex justify-space-between flex-wrap flex-column flex-sm-row"
          >
            <div class="mb-8 mb-sm-0">
              <p class="font-weight-semibold payment-details-header">
                Facturar a:
              </p>
              <v-select
                v-model="selected"
                :items="['foo', 'bar', 'zaz']"
                outlined
                dense
                placeholder="Seleccionar Cliente"
                persistent-placeholder
              >
              </v-select>
              <template v-if="selected">
                <p class="mb-1">Jordan Stevenson</p>
                <p class="mb-1">Hall-Robbins PLC</p>
                <p class="mb-1">7777 Mendez Plains, USA</p>
                <p class="mb-1">(616) 865-4180</p>
                <p class="mb-0">don85@johnson.com</p>
              </template>
            </div>
            <div>
              <p class="font-weight-semibold payment-details-header">
                Cobrar a:
              </p>
              <v-simple-table>
                <template v-slot:default>
                  <tr>
                    <td class="pe-6">Total Vencido:</td>
                    <td>none</td>
                  </tr>
                  <tr>
                    <td class="pe-6">Nombre Banco:</td>
                    <td>none</td>
                  </tr>
                  <tr>
                    <td class="pe-6">Ciudad:</td>
                    <td>none</td>
                  </tr>
                  <tr>
                    <td class="pe-6">CUENTA:</td>
                    <td>none</td>
                  </tr>
                  <tr>
                    <td class="pe-6">CLABE:</td>
                    <td>none</td>
                  </tr>
                </template>
              </v-simple-table>
            </div>
          </div>
        </v-card-text>
        <v-divider />
        <!-- Datos Productos -->
        <v-card-text class="add-products-form pt-9 pb-10 px-8">
          <div v-for="item in items" :key="item" class="single-product-form">
            <div class="add-products-header mb-2 d-none d-md-flex">
              <div class="px-5 flex-grow-1 font-weight-semibold">
                <div class="row">
                  <div class="col-md-6 col-12"><span>Item</span></div>
                  <div class="col-md-2 col-12"><span>Costo</span></div>
                  <div class="col-md-2 col-12"><span>Cantidad</span></div>
                  <div class="col-md-2 col-12"><span>PRecio</span></div>
                </div>
              </div>
              <div class="header-spacer"></div>
            </div>
            <div class="d-flex v-card v-sheet v-sheet--outlined theme--light">
              <div class="pa-5 flex-grow-1">
                <div class="row">
                  <div class="col-md-6 col-12">
                    <v-select
                      class="mb-3"
                      hide-details
                      dense
                      outlined
                      placeholder="Select Item"
                      persistent-placeholder
                      clearable
                      label="Select Item"
                    ></v-select>
                    <v-textarea
                      hide-details
                      outlined
                      placeholder="Description"
                      label="Description"
                    ></v-textarea>
                  </div>
                  <div class="col-sm-4 col-md-2 col-12">
                    <v-text-field
                      outlined
                      hide-details
                      dense
                      type="number"
                      label="Costo"
                    ></v-text-field>
                  </div>
                  <div class="col-sm-4 col-md-2 col-12">
                    <v-text-field
                      outlined
                      hide-details
                      dense
                      type="number"
                      label="Cantidad"
                    ></v-text-field>
                  </div>
                  <div class="col-sm-4 col-md-2 col-12">
                    <p class="my-2">
                      <span class="d-inline d-md-none">Price: </span>
                      <span>$0</span>
                    </p>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column item-actions rounded-0 pa-1">
                <v-btn icon rounded small @click="items--">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-spacer />
                <v-btn icon rounded small>
                  <v-icon>mdi-settings</v-icon>
                </v-btn>
              </div>
            </div>
          </div>
          <!-- buttom ADD -->
          <v-btn
            text
            large
            outlined
            color="primary"
            class="mt-4"
            @click="items++"
            >Add Item</v-btn
          >
        </v-card-text>
        <v-divider />
        <!-- Resumen de Costo -->
        <v-card-text class="py-9 px-8">
          <div
            class="invoice-total d-flex justify-space-between flex-column flex-sm-row"
          >
            <div class="mb-2 mb-sm-0">
              <p class="mb-4 d-flex align-center">
                <span class="font-weight-semibold me-2">Vendodor(a):</span>
                <v-text-field hide-details outlined solo dense></v-text-field>
              </p>
              <v-text-field
                dense
                outlined
                hide-details
                placeholder="Mensaje"
              ></v-text-field>
            </div>
            <div>
              <table>
                <tr>
                  <td class="pe-16">Subtotal:</td>
                  <th class="text-right">$1800</th>
                </tr>
                <tr>
                  <td class="pe-16">Discount:</td>
                  <th class="text-right">$28</th>
                </tr>
                <tr>
                  <td class="pe-16">Tax:</td>
                  <th class="text-right">21%</th>
                </tr>
              </table>
              <hr
                role="separator"
                aria-orientation="horizontal"
                class="mt-4 mb-3 v-divider theme--light"
              />
              <table class="w-full">
                <tr>
                  <td class="pe-16">Total:</td>
                  <th class="text-right">$1690</th>
                </tr>
              </table>
            </div>
          </div>
        </v-card-text>
        <v-divider />
        <!-- Observaciones Notas -->
        <v-card-text class="py-9 px-8">
          <p class="font-weight-semibold mb-2">
            Nota:
          </p>
          <v-textarea outlined dense></v-textarea>
        </v-card-text>
      </v-card>
    </v-col>
    <v-col cols="12" md="3" class="d-print-none">
      <v-card class="mb-6">
        <v-card-text>
          <v-btn block class="mb-3" color="primary">
            Send Invoice
          </v-btn>
          <v-btn block class="mb-3" color="primary" outlined>
            Download
          </v-btn>
          <v-btn
            block
            class="mb-3"
            color="primary"
            outlined
            @click="print('invoice-to-print')"
          >
            Print
          </v-btn>
          <v-btn block class="mb-3" color="primary" outlined>
            Edit Invoice
          </v-btn>
          <v-btn block class="mb-3" color="secondary">
            Add Payment
          </v-btn>
        </v-card-text>
      </v-card>
      <v-select
        :items="['foo', 'bar', 'zaz']"
        placeholder="Account Bank"
        outlined
        dense
      ></v-select>
      <div class="d-flex align-center justify-space-between mb-3">
        <span>Payment Terms</span>
        <v-switch color="primary"></v-switch>
      </div>
      <div class="d-flex align-center justify-space-between mb-3">
        <span>Payment Terms</span>
        <v-switch color="primary"></v-switch>
      </div>
      <div class="d-flex align-center justify-space-between mb-3">
        <span>Payment Terms</span>
        <v-switch color="primary"></v-switch>
      </div>
    </v-col>
  </v-row>
</template>
<script>
export default {
  data() {
    return {
      selected: null,
      items: 0,
      desserts: [
        {
          name: "Frozen Yogurt",
          calories: 159,
          description: "Frozen Yogurt",
          calories2: 159,
          calories3: 159,
        },
        {
          name: "Ice cream sandwich",
          calories: 159,
          description: "Ice cream sandwich",
          calories2: 159,
          calories3: 159,
        },
      ],
    };
  },
  methods: {
    print(divName) {
      window.print();
      // var printwin = window.open("");
      // printwin.document.write(
      //   document.getElementById("invoice-to-print").innerHTML
      // );
      // printwin.print();
    },
  },
};
</script>
<style scoped>
.item-actions {
  border-left: thin solid rgba(94, 86, 105, 0.14);
  border-left-width: thin;
  border-left-style: solid;
  border-left-color: rgba(94, 86, 105, 0.14);
}
.add-products-form .single-product-form:not(:first-child) {
  margin-top: 2rem;
}

@media only print {
  /* body {
    visibility: hidden;
    width: 100vh;
    height: 100vh;
  } */
  /* 
  #nav-drawer {
    display: none;
    visibility: hidden;
  } */

  #invoice-to-print {
    visibility: visible;
    width: 100vh;
    min-height: calc(100vh - 58px);
  }
}

@media only screen and (max-width: 600px) {
  #invoice-to-print {
    width: 100%;
    display: block;
    text-align: center;
  }
}
@page {
  margin: 0px 0px;
}

/* @media print {
  body {
    overflow: auto;
    height: auto;
  }
} */
</style>
