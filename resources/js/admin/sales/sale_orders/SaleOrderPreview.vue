<template>
  <v-row>
    <v-col cols="12" md="9">
      <v-card id="invoice-to-print">
        <v-card-text class="py-9 px-8">
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
                Office 149, 450 South Brand Brooklyn
              </span>
              <span class="d-block"> San Diego County, CA 91905, USA </span>
              <span class="d-block">
                +1 (123) 456 7891, +44 (876) 543 2198
              </span>
            </div>
            <div>
              <div>
                <p class="font-weight-medium text-xl text--primary mb-4">
                  Invoice #4987
                </p>
                <p class="mb-2">
                  <span>Date Issued: </span
                  ><span class="font-weight-semibold">2019-12-13</span>
                </p>
                <p class="mb-2">
                  <span>Due Date: </span
                  ><span class="font-weight-semibold">2019-04-23</span>
                </p>
              </div>
            </div>
          </div>
        </v-card-text>
        <v-divider />
        <v-card-text class="py-9 px-8">
          <div
            class="payment-details d-flex justify-space-between flex-wrap flex-column flex-sm-row"
          >
            <div class="mb-8 mb-sm-0">
              <p class="font-weight-semibold payment-details-header">
                Invoice To:
              </p>
              <p class="mb-1">Jordan Stevenson</p>
              <p class="mb-1">Hall-Robbins PLC</p>
              <p class="mb-1">7777 Mendez Plains, USA</p>
              <p class="mb-1">(616) 865-4180</p>
              <p class="mb-0">don85@johnson.com</p>
            </div>
            <div>
              <p class="font-weight-semibold payment-details-header">
                Bill To:
              </p>
              <table>
                <tr>
                  <td class="pe-6">Total Due:</td>
                  <td>$12,110.55</td>
                </tr>
                <tr>
                  <td class="pe-6">Bank Name:</td>
                  <td>American Bank</td>
                </tr>
                <tr>
                  <td class="pe-6">Country:</td>
                  <td>United States</td>
                </tr>
                <tr>
                  <td class="pe-6">IBAN:</td>
                  <td>ETD95476213874685</td>
                </tr>
                <tr>
                  <td class="pe-6">SWIFT Code:</td>
                  <td>BR91905</td>
                </tr>
              </table>
            </div>
          </div>
        </v-card-text>
        <v-divider />
        <v-simple-table class="purchased-items-table">
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">
                  Name
                </th>
                <th class="text-left">
                  Description
                </th>
                <th class="text-left">
                  Calories #1
                </th>
                <th class="text-left">
                  Calories #2
                </th>
                <th class="text-left">
                  Calories #3
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in desserts" :key="item.name">
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.calories }}</td>
                <td>{{ item.calories2 }}</td>
                <td>{{ item.calories3 }}</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <v-divider />
        <v-card-text class="py-9 px-8">
          <div
            class="invoice-total d-flex justify-space-between flex-column flex-sm-row"
          >
            <div class="mb-2 mb-sm-0">
              <p class="mb-1">
                <span class="font-weight-semibold">Salesperson:</span>
                <span>Jenny Parker</span>
              </p>
              <p>Thanks for your business</p>
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
        <v-card-text class="py-9 px-8">
          <p class="mb-0">
            <span class="font-weight-semibold">Note: </span
            ><span
              >It was a pleasure working with you and your team. We hope you
              will keep us in mind for future freelance projects. Thank
              You!</span
            >
          </p>
        </v-card-text>
      </v-card>
    </v-col>
    <v-col cols="12" md="3">
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
    },
  },
};
</script>
<style></style>
